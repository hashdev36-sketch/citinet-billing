@extends('layouts.app')

@section('title', 'Packages — Citinet WiFi')

@section('content')
<div class="citi-section">
    <h2 style="text-align:left;">Choose Your Package</h2>
    <div class="grid-plans" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
        @forelse ($packages as $package)
            <div class="citi-card" style="text-align:left;">
                <div class="citi-card-body">
                    <div class="plan-name" style="font-size:16px;">{{ $package->name }}</div>
                    <div class="plan-sub">{{ $package->duration_label }} &middot; {{ $package->device_limit }} device{{ $package->device_limit > 1 ? 's' : '' }}</div>
                    <p style="font-size:13px; color:#6b7280; margin:8px 0;">{{ $package->description }}</p>
                    <div class="plan-price" style="margin-bottom:10px;">&#8358;{{ number_format($package->price) }}</div>
                    <a href="{{ route('packages.show', $package->slug) }}" class="btn-citi btn-citi-primary btn-citi-sm" style="width:100%;">Buy Now</a>
                </div>
            </div>
        @empty
            <p style="color:#6b7280;">No packages available right now.</p>
        @endforelse
    </div>
</div>
@endsection
