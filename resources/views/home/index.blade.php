@extends('layouts.app')

@section('title', 'Citinet WiFi — Fast, Affordable Internet Vouchers')

@section('content')
    <section class="citi-hero">
        <div style="font-size:40px; margin-bottom:8px;">⚡</div>
        <h1>Fast internet, bought in seconds.</h1>
        <p>Buy a Citinet WiFi voucher online, pay securely with Paystack, and get connected instantly — no queues, no delays.</p>
        <a href="{{ route('packages.index') }}" class="btn-citi btn-citi-primary">🎁 Buy a Voucher Now</a>
        <p style="font-size:13px; color:#dbeafe; margin-top:16px;">📍 Available across {{ $siteCount }} Citinet location{{ $siteCount > 1 ? 's' : '' }} — pick yours at checkout</p>
    </section>

    <section class="citi-section">
        <h2>Current Internet Packages</h2>
        <div class="grid-plans" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
            @forelse ($packages as $package)
                <div class="citi-card" style="text-align:left;">
                    <div class="citi-card-body">
                        <div class="plan-name" style="font-size:16px;">{{ $package->name }}</div>
                        <div class="plan-sub">{{ $package->duration_label }} &middot; {{ $package->device_limit }} device{{ $package->device_limit > 1 ? 's' : '' }}</div>
                        <div class="plan-price" style="margin:8px 0;">&#8358;{{ number_format($package->price) }}</div>
                        <a href="{{ route('packages.show', $package->slug) }}" class="btn-citi btn-citi-outline btn-citi-sm" style="width:100%;">View Details</a>
                    </div>
                </div>
            @empty
                <p style="text-align:center; color:#6b7280;">No packages available right now — check back soon.</p>
            @endforelse
        </div>
    </section>

    <section class="citi-section" style="background:#fff; border-radius:20px;">
        <h2>Why Choose Citinet</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:20px; text-align:center;">
            <div><div style="font-size:28px;">🚀</div><h6 style="margin-top:8px;">High Speed</h6></div>
            <div><div style="font-size:28px;">🔒</div><h6 style="margin-top:8px;">Secure Payments</h6></div>
            <div><div style="font-size:28px;">⚡</div><h6 style="margin-top:8px;">Instant Delivery</h6></div>
            <div><div style="font-size:28px;">📱</div><h6 style="margin-top:8px;">WhatsApp Support</h6></div>
        </div>
    </section>

    <section id="how-it-works" class="citi-section">
        <h2>How It Works</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:20px; text-align:center;">
            <div><div class="pill pill-info" style="font-size:16px; padding:10px 16px;">1</div><p style="margin-top:10px;">Choose a package</p></div>
            <div><div class="pill pill-info" style="font-size:16px; padding:10px 16px;">2</div><p style="margin-top:10px;">Pay with Paystack</p></div>
            <div><div class="pill pill-info" style="font-size:16px; padding:10px 16px;">3</div><p style="margin-top:10px;">Voucher assigned instantly</p></div>
            <div><div class="pill pill-info" style="font-size:16px; padding:10px 16px;">4</div><p style="margin-top:10px;">Connect &amp; browse</p></div>
        </div>
    </section>

    <section id="faq" class="citi-section" style="max-width:800px; background:#fff; border-radius:20px;">
        <h2>Frequently Asked Questions</h2>
        <div style="margin-bottom:16px;">
            <strong>How fast will I get my voucher?</strong>
            <p style="color:#6b7280; font-size:14px; margin-top:4px;">Immediately after successful payment — it's shown on screen and also saved to your dashboard.</p>
        </div>
        <div>
            <strong>Can I retrieve a voucher I already bought?</strong>
            <p style="color:#6b7280; font-size:14px; margin-top:4px;">Yes — log in and open "My Dashboard" to see every voucher and receipt you've purchased.</p>
        </div>
    </section>
@endsection
