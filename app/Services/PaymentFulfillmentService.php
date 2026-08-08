<?php

namespace App\Services;

use App\Exceptions\NoVoucherStockException;
use App\Jobs\SendVoucherEmail;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentFulfillmentService
{
    public function __construct(
        private PaystackService $paystack,
        private VoucherAssignmentService $voucherAssignment,
    ) {}

    /**
     * Verify a Paystack reference and fulfil the order if it's genuinely paid.
     *
     * Called from TWO places that can race each other: Paystack's webhook, and the
     * browser callback after redirect — Paystack fires both for the same successful
     * charge, often within milliseconds of each other. Both funnel through here so
     * there's exactly one fulfillment code path, and the whole thing is wrapped in a
     * transaction with a row lock on the order, so whichever call arrives first holds
     * the lock until it fully commits (including voucher assignment) — the second call
     * blocks, then re-reads the now-'fulfilled' status and returns immediately as a
     * no-op. Without the lock spanning the whole method, both calls could pass the
     * idempotency check simultaneously and each assign a *different* voucher to the
     * same order — burning two vouchers for one payment.
     */
    public function fulfillByReference(string $reference): Order
    {
        return DB::transaction(function () use ($reference) {
            $order = Order::where('paystack_reference', $reference)->lockForUpdate()->firstOrFail();

            // Idempotency guard: webhook and callback both call this for the same order.
            // If it's already fulfilled (or was already marked failed/refunded), don't reprocess.
            if (in_array($order->status, ['fulfilled', 'failed', 'refunded'], true)) {
                return $order;
            }

            $verified = $this->paystack->verify($reference);

            if (($verified['status'] ?? null) !== 'success') {
                $order->update(['status' => 'failed']);
                return $order;
            }

            // Defensive check: amount actually paid (kobo) must match what we charged for.
            $expectedKobo = (int) round($order->amount * 100);
            if ((int) $verified['amount'] !== $expectedKobo) {
                Log::critical('Paystack amount mismatch — possible tampering.', [
                    'order_id' => $order->id,
                    'expected_kobo' => $expectedKobo,
                    'paystack_amount' => $verified['amount'],
                ]);
                $order->update(['status' => 'failed']);
                return $order;
            }

            Payment::updateOrCreate(
                ['reference' => $reference],
                [
                    'order_id' => $order->id,
                    'amount' => $order->amount,
                    'currency' => $verified['currency'] ?? 'NGN',
                    'channel' => $verified['channel'] ?? null,
                    'status' => 'success',
                    'gateway_response' => $verified,
                    'paid_at' => $verified['paid_at'] ?? now(),
                ]
            );

            $order->update(['status' => 'paid', 'paid_at' => now()]);

            try {
                $voucher = $this->voucherAssignment->assign($order->fresh(), $order->customer);

                // afterCommit() matters here regardless of which queue driver ends up
                // configured: it guarantees the email job only fires once this whole
                // transaction has actually committed. With the 'database' queue driver
                // (the .env.example default) this is naturally true anyway since the job
                // row itself lives in the same transaction — but if this ever moves to
                // Redis/SQS (a real possibility once this is on a proper VPS), dispatching
                // from inside an open transaction sends the job to an external queue
                // immediately, and a fast worker could try to process it before the Order/
                // Voucher rows are even visible yet, failing the fetch entirely.
                SendVoucherEmail::dispatch($order->fresh(), $voucher)->afterCommit();
            } catch (NoVoucherStockException $e) {
                // Customer HAS paid but we have nothing to give them — this must surface loudly,
                // not fail silently. Order stays 'paid' (not 'fulfilled') so it shows up in an
                // admin "needs attention" queue for manual fulfillment + customer contact.
                Log::critical($e->getMessage(), ['order_id' => $order->id]);
            }

            return $order->fresh();
        });
    }
}
