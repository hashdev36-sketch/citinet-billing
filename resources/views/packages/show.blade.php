@extends('layouts.app')

@section('title', $package->name . ' — Citinet WiFi')

@section('content')
<div class="citi-section" style="max-width:520px;">
    <div class="citi-card">
        <div class="citi-card-body">
            <div style="text-align:center; margin-bottom:16px;">
                <div style="font-size:32px;">📡</div>
                <h1 style="font-size:22px; font-weight:800; color:#0B2A3B; margin:6px 0 2px;">{{ $package->name }}</h1>
                <p style="font-size:13px; color:#6b7280;">{{ $package->duration_label }} &middot; {{ $package->device_limit }} device{{ $package->device_limit > 1 ? 's' : '' }}</p>
            </div>
            <p style="font-size:14px; color:#374151; text-align:center;">{{ $package->description }}</p>
            <div style="text-align:center; font-size:30px; font-weight:800; color:#ea580c; margin:14px 0;">&#8358;{{ number_format($package->price) }}</div>

            @auth
                @if ($sites->isEmpty())
                    <div class="pill pill-danger" style="display:block; padding:14px; text-align:center;">
                        Out of stock at every location right now — check back shortly.
                    </div>
                @else
                    <form method="POST" action="{{ route('checkout.initiate', $package) }}" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').textContent = 'Redirecting to Paystack…';">
                        @csrf
                        <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:8px;">📍 Choose a location</label>
                        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">
                            @foreach ($sites as $site)
                                <label style="display:flex; align-items:center; gap:10px; border:1.5px solid #e5e7eb; border-radius:12px; padding:12px 14px; cursor:pointer;">
                                    <input type="radio" name="site_id" value="{{ $site->id }}" {{ $loop->first ? 'checked' : '' }} required>
                                    <span style="flex:1;">{{ $site->name }}@if($site->address) <span style="color:#9ca3af; font-size:12px;"> — {{ $site->address }}</span>@endif</span>
                                </label>
                            @endforeach
                        </div>
                        <button type="submit" class="btn-citi btn-citi-primary" style="width:100%;">
                            🔒 Pay with Paystack
                        </button>
                    </form>
                    <p style="font-size:11px; color:#9ca3af; text-align:center; margin-top:8px;">Tap once — the button will show "Redirecting…" while it takes you to Paystack.</p>
                @endif
            @else
                <a href="{{ route('login.with-redirect', ['to' => route('packages.show', $package->slug, absolute: false)]) }}"
                   class="btn-citi btn-citi-primary" style="width:100%; display:block;">
                    Login to Buy
                </a>
                <p style="font-size:13px; color:#6b7280; text-align:center; margin-top:10px;">New here? <a href="{{ route('register') }}">Create an account</a> — it only takes a minute.</p>
            @endauth
        </div>
    </div>
</div>
@endsection
