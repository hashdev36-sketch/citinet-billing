<?php

namespace App\Http\Controllers;

use App\Services\PaymentFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        private PaystackService $paystack,
        private PaymentFulfillmentService $fulfillment,
    ) {}

    /**
     * Paystack POSTs here on charge.success (and other events). This is the
     * authoritative fulfillment path — it does not rely on the customer's
     * browser ever coming back.
     *
     * Route for this MUST be excluded from CSRF verification (see bootstrap/app.php)
     * since Paystack can't send a CSRF token.
     */
    public function handle(Request $request): Response
    {
        $signature = $request->header('X-Paystack-Signature');

        if (! $this->paystack->isValidWebhookSignature($request->getContent(), $signature)) {
            Log::warning('Rejected Paystack webhook with invalid signature.', ['ip' => $request->ip()]);
            return response('Invalid signature', 401);
        }

        $event = $request->input('event');
        $reference = $request->input('data.reference');

        if ($event === 'charge.success' && $reference) {
            try {
                $this->fulfillment->fulfillByReference($reference);
            } catch (ModelNotFoundException) {
                // Reference doesn't match any order we created — most likely Paystack's
                // dashboard "Test Webhook" button (which sends a fake reference), or a
                // stale/replayed request. Retrying won't help either way, so log and
                // still return 200 rather than letting this become an uncaught 500 that
                // Paystack will retry-storm indefinitely.
                Log::warning('Paystack webhook referenced an unknown order.', ['reference' => $reference]);
            } catch (\Throwable $e) {
                // Anything else (Paystack API timeout during verify, DB briefly down, etc.)
                // might genuinely be transient, so let Paystack's retry mechanism help —
                // return 500 here instead of swallowing it.
                Log::error('Paystack webhook processing failed: ' . $e->getMessage(), ['reference' => $reference]);
                return response('Processing error', 500);
            }
        }

        // Always 200 quickly so Paystack doesn't retry-storm us; unhandled events are just ignored.
        return response('OK', 200);
    }
}
