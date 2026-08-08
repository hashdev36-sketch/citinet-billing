<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\Site;
use App\Services\PaymentFulfillmentService;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private PaystackService $paystack,
        private PaymentFulfillmentService $fulfillment,
    ) {}

    /**
     * Customer clicks "Buy". Create a pending Order, then hand off to Paystack.
     * No voucher is touched here — stock is only ever claimed after payment is verified.
     */
    public function initiate(Request $request, Package $package): RedirectResponse
    {
        abort_unless($package->is_active, 404);

        $validated = $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
        ]);

        $site = Site::where('id', $validated['site_id'])->where('is_active', true)->first();

        if (! $site) {
            throw ValidationException::withMessages(['site_id' => 'Please select a valid location.']);
        }

        // Re-check stock server-side even though the form only ever shows sites with stock —
        // someone else could have bought the last voucher between page load and this submit.
        if ($package->availableStockCount($site) < 1) {
            return back()->with('error', "Sorry, {$site->name} is out of stock for {$package->name} right now. Please pick another location.");
        }

        $customer = $request->user();
        $reference = 'CIT_' . Str::upper(Str::random(12));

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'site_id' => $site->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'currency' => 'NGN',
            'status' => 'pending',
            'paystack_reference' => $reference,
        ]);

        try {
            $init = $this->paystack->initialize(
                email: $customer->email,
                amountKobo: (int) round($package->price * 100),
                reference: $reference,
                metadata: ['order_id' => $order->id, 'package' => $package->slug, 'site' => $site->slug],
            );
        } catch (\Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException $e) {
            // Paystack's API itself failed to respond (network blip, bad key, an outage on
            // their end) — without this catch, the customer would see a raw 500 crash page,
            // and this order would sit as 'pending' forever since it never even reached
            // Paystack's payment page. Mark it failed so it's clearly dead rather than a
            // silent zombie order, and send the customer back with something they can act on.
            \Illuminate\Support\Facades\Log::error('Paystack initialize failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            $order->update(['status' => 'failed']);

            return back()->with('error', 'We could not start the payment right now. Please try again in a moment, or contact support if this keeps happening.');
        }

        return redirect()->away($init['authorization_url']);
    }

    /**
     * Paystack redirects the customer's browser back here after they pay.
     *
     * This is NEVER trusted on its own — it calls the same verify-against-Paystack-API
     * path as the webhook. Whichever of (webhook, this callback) reaches Paystack's
     * verify endpoint first performs the fulfillment; the other is a no-op.
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference') or abort(400, 'Missing payment reference.');

        try {
            $order = $this->fulfillment->fulfillByReference($reference);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()->route('packages.index')
                ->with('error', "We couldn't find that payment reference. If you were charged, please contact support with your bank reference.");
        }

        return match ($order->status) {
            'fulfilled' => redirect()->route('dashboard.orders.show', $order)
                ->with('success', 'Payment confirmed — your voucher is ready below.'),
            'paid' => redirect()->route('dashboard.orders.show', $order)
                ->with('warning', 'Payment confirmed but voucher assignment is pending — our team has been notified and will follow up shortly.'),
            default => redirect()->route('packages.index')
                ->with('error', 'Payment was not successful. No amount was deducted for a failed attempt, or contact support if you were charged.'),
        };
    }
}
