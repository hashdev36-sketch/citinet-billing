<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['customer', 'site', 'package', 'voucher'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('site_id'), fn ($q) => $q->where('site_id', $request->site_id))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(50);

        $sites = Site::orderBy('sort_order')->get();

        return view('admin.orders.index', compact('orders', 'sites'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        // Mirror index()'s filters exactly — the Export button passes along the current
        // query string, so a filtered export must actually reflect that filter instead
        // of quietly exporting everything in the date range regardless of status/site.
        $orders = Order::with(['customer', 'site', 'package', 'voucher'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('site_id'), fn ($q) => $q->where('site_id', $request->site_id))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderBy('created_at')
            ->cursor();

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Date', 'Customer', 'Email', 'Location', 'Package', 'Amount', 'Status', 'Voucher Username', 'Paystack Ref']);

            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer->name,
                    $order->customer->email,
                    $order->site->name,
                    $order->package->name,
                    $order->amount,
                    $order->status,
                    $order->voucher->username ?? '',
                    $order->paystack_reference,
                ]);
            }

            fclose($out);
        }, 'citinet-sales-' . now()->format('Ymd-His') . '.csv');
    }
}
