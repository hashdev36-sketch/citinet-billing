<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user();

        $orders = $customer->orders()
            ->with(['package', 'site', 'voucher'])
            ->latest()
            ->paginate(10);

        // A customer can have more than one active voucher at once if they bought
        // access at two different locations (e.g. one for home, one for the office) —
        // show all of them, not just the most recent.
        $activeVouchers = $customer->vouchers()
            ->where('status', 'sold')
            ->where('expires_at', '>', now())
            ->with(['package', 'site'])
            ->latest('sold_at')
            ->get();

        return view('dashboard.index', compact('orders', 'activeVouchers'));
    }

    public function showOrder(Request $request, Order $order): View
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        $order->load(['package', 'site', 'voucher', 'payments']);

        return view('dashboard.order', compact('order'));
    }
}
