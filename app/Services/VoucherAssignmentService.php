<?php

namespace App\Services;

use App\Exceptions\NoVoucherStockException;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherAssignmentService
{
    /**
     * Atomically claim one unused voucher for the given order's package and mark it sold.
     *
     * The whole thing runs inside a transaction with a row lock (`lockForUpdate`) on the
     * candidate voucher, so two webhook deliveries (Paystack retries webhooks) or two
     * concurrent requests for the same package can never walk away with the same voucher.
     *
     * @throws NoVoucherStockException if no unused voucher exists for the package
     */
    public function assign(Order $order, User $customer): Voucher
    {
        return DB::transaction(function () use ($order, $customer) {
            $voucher = Voucher::where('site_id', $order->site_id)   // scoped to the location the customer actually chose
                ->where('package_id', $order->package_id)
                ->where('status', 'unused')
                ->oldest('id')          // sell oldest stock first (FIFO), not random
                ->lockForUpdate()        // blocks any other transaction from grabbing this row until we commit
                ->first();

            if (! $voucher) {
                throw new NoVoucherStockException(
                    "No unused voucher stock for package #{$order->package_id} at site #{$order->site_id} (order {$order->order_number})."
                );
            }

            $voucher->update([
                'status' => 'sold',
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'sold_at' => now(),
                'expires_at' => now()->addMinutes($order->package->duration_minutes),
            ]);

            $order->update([
                'voucher_id' => $voucher->id,
                'status' => 'fulfilled',
                'fulfilled_at' => now(),
            ]);

            return $voucher;
        });
    }
}
