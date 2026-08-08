@extends('layouts.app')

@section('title', 'My Dashboard — Citinet WiFi')

@section('content')
<div class="citi-section">
    <h2 style="text-align:left;">My Dashboard</h2>

    @if ($activeVouchers->isNotEmpty())
        @foreach ($activeVouchers as $activeVoucher)
            <div class="callout-green" style="margin-bottom:16px;">
                <span class="callout-badge">ACTIVE</span><h3>{{ $activeVoucher->site->name }} — {{ $activeVoucher->package->name }}</h3>
                <div class="info-rows" style="margin-top:12px;">
                    <div class="info-row"><span class="info-label">Location</span><span class="info-value">📍 {{ $activeVoucher->site->name }}</span></div>
                    <div class="info-row"><span class="info-label">Username</span><span class="info-value"><code>{{ $activeVoucher->username }}</code></span></div>
                    <div class="info-row"><span class="info-label">Password</span><span class="info-value"><code>{{ $activeVoucher->revealPassword() }}</code></span></div>
                </div>
                <p>Expires {{ $activeVoucher->expires_at->format('d M Y, h:i A') }}</p>
            </div>
        @endforeach
    @else
        <div class="citi-card" style="margin-bottom:24px;">
            <div class="citi-card-body" style="text-align:center;">
                <p style="color:#6b7280;">You don't have an active voucher right now.</p>
                <a href="{{ route('packages.index') }}" class="btn-citi btn-citi-primary">🎁 Buy a Voucher</a>
            </div>
        </div>
    @endif

    <div class="citi-card">
        <div class="citi-card-body">
            <h3 style="font-size:16px; margin-bottom:14px;">Purchase History</h3>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="text-align:left; color:#6b7280; border-bottom:1px solid #f3f4f6;">
                            <th style="padding:8px 6px;">Order #</th><th>Location</th><th>Package</th><th>Amount</th><th>Status</th><th>Date</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr style="border-bottom:1px solid #f9fafb;">
                                <td style="padding:10px 6px;">{{ $order->order_number }}</td>
                                <td>📍 {{ $order->site->name }}</td>
                                <td>{{ $order->package->name }}</td>
                                <td>&#8358;{{ number_format($order->amount) }}</td>
                                <td>
                                    <span class="pill pill-{{ $order->status === 'fulfilled' ? 'success' : ($order->status === 'failed' ? 'danger' : 'neutral') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><a href="{{ route('dashboard.orders.show', $order) }}" class="btn-citi btn-citi-outline btn-citi-sm">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center; color:#6b7280; padding:24px;">No purchases yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
