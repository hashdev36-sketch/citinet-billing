@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="citi-section" style="max-width:560px;">
    <a href="{{ route('dashboard') }}" style="font-size:13px; color:#1C4E6F;">← Back to Dashboard</a>

    <div class="citi-card" style="margin-top:14px;">
        <div class="citi-card-body">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px;">
                <div>
                    <h3 style="font-size:17px; margin:0;">Order {{ $order->order_number }}</h3>
                    <span style="font-size:12px; color:#6b7280;">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <span class="pill pill-{{ $order->status === 'fulfilled' ? 'success' : ($order->status === 'failed' ? 'danger' : 'neutral') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="info-rows" style="margin-bottom:16px;">
                <div class="info-row"><span class="info-label">Location</span><span class="info-value">📍 {{ $order->site->name }}</span></div>
                <div class="info-row"><span class="info-label">Package</span><span class="info-value">{{ $order->package->name }} ({{ $order->package->duration_label }})</span></div>
                <div class="info-row"><span class="info-label">{{ in_array($order->status, ['paid', 'fulfilled']) ? 'Amount Paid' : 'Amount' }}</span><span class="info-value">&#8358;{{ number_format($order->amount) }}</span></div>
                <div class="info-row"><span class="info-label">Payment Ref</span><span class="info-value"><code>{{ $order->paystack_reference }}</code></span></div>
            </div>

            @if ($order->voucher)
                <div class="callout-green">
                    <h3 style="display:block; margin-bottom:8px;">🌐 Your Voucher</h3>
                    <div class="info-rows">
                        <div class="info-row"><span class="info-label">Username</span><span class="info-value"><code>{{ $order->voucher->username }}</code></span></div>
                        <div class="info-row"><span class="info-label">Password</span><span class="info-value"><code>{{ $order->voucher->revealPassword() }}</code></span></div>
                    </div>
                    <p>Expires {{ $order->voucher->expires_at->format('d M Y, h:i A') }}</p>
                </div>
            @elseif ($order->status === 'paid')
                <div class="pill pill-warning" style="display:block; padding:14px; line-height:1.5;">
                    ⏳ Your payment was confirmed, but voucher assignment is pending. Our support team has been notified
                    and will reach out shortly. You can also contact us on WhatsApp with your order number above.
                </div>
            @elseif ($order->status === 'pending')
                <div class="pill pill-neutral" style="display:block; padding:14px; line-height:1.5;">
                    This order hasn't been paid for yet. If you started checkout and didn't finish, you can
                    <a href="{{ route('packages.show', $order->package->slug) }}">buy this package again</a> to get a fresh payment link.
                </div>
            @elseif ($order->status === 'failed')
                <div class="pill pill-danger" style="display:block; padding:14px; line-height:1.5;">
                    This payment didn't go through, so nothing was charged for this order specifically. If you were
                    charged by your bank and don't see a working voucher here, please contact support with this
                    order number and your bank reference.
                </div>
            @endif

            <button onclick="window.print()" class="btn-citi btn-citi-secondary" style="width:100%; margin-top:16px;">
                🖨️ Print Receipt
            </button>
        </div>
    </div>
</div>
@endsection
