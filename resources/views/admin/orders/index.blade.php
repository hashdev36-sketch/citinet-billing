@extends('layouts.admin')

@section('title', 'Orders — Citinet Admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin:0;">Orders &amp; Payments</h2>
    <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn-citi btn-citi-outline btn-citi-sm">⬇ Export CSV</a>
</div>

<form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    <select name="site_id" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
        <option value="">All Locations</option>
        @foreach ($sites as $site)
            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>📍 {{ $site->name }}</option>
        @endforeach
    </select>
    <select name="status" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
        <option value="">All Statuses</option>
        @foreach (['pending', 'paid', 'fulfilled', 'failed', 'refunded'] as $status)
            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
    <input type="date" name="to" value="{{ request('to') }}" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
    <button class="btn-citi btn-citi-outline btn-citi-sm">Filter</button>
</form>

<div class="citi-card"><div class="citi-card-body">
    <table style="width:100%;font-size:13px;border-collapse:collapse;">
        <thead><tr style="text-align:left;color:#6b7280;border-bottom:1px solid #f3f4f6;"><th style="padding:8px 0;">Order #</th><th>Customer</th><th>Location</th><th>Package</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        @foreach ($orders as $order)
            <tr style="border-bottom:1px solid #f9fafb;">
                <td style="padding:10px 0;">{{ $order->order_number }}</td>
                <td>{{ $order->customer->name }}<br><span style="font-size:11px;color:#9ca3af;">{{ $order->customer->email }}</span></td>
                <td>📍 {{ $order->site->name }}</td>
                <td>{{ $order->package->name }}</td>
                <td>&#8358;{{ number_format($order->amount) }}</td>
                <td><span class="pill pill-{{ ['pending'=>'neutral','paid'=>'warning','fulfilled'=>'success','failed'=>'danger','refunded'=>'neutral'][$order->status] }}">{{ ucfirst($order->status) }}</span></td>
                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
</div></div>
@endsection
