@extends('layouts.admin')

@section('title', 'Dashboard — Citinet Admin')

@section('content')
<h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin-bottom:20px;">Dashboard</h2>

@if ($needsAttention > 0)
    <div class="pill pill-danger" style="display:block;padding:14px 16px;margin-bottom:20px;">
        ⚠️ <strong>{{ $needsAttention }}</strong> order(s) are paid but not yet fulfilled (likely out of stock).
        <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" style="color:#b91c1c;font-weight:700;">Review now</a>.
    </div>
@endif

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px;">
    <div class="stat-card"><div class="stat-label">Today's Sales</div><div class="stat-value">&#8358;{{ number_format($today) }}</div></div>
    <div class="stat-card"><div class="stat-label">This Week</div><div class="stat-value">&#8358;{{ number_format($thisWeek) }}</div></div>
    <div class="stat-card"><div class="stat-label">This Month</div><div class="stat-value">&#8358;{{ number_format($thisMonth) }}</div></div>
</div>


<div class="citi-card" style="margin-bottom:14px;"><div class="citi-card-body">
    <h3 style="font-size:15px;margin-bottom:4px;">Voucher Stock — Package &times; Location</h3>
    <p style="font-size:12px;color:#9ca3af;margin-bottom:12px;">Which location is about to run out of which package.</p>
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;color:#6b7280;border-bottom:1px solid #f3f4f6;">
                    <th style="padding:6px 8px 6px 0;">Package</th>
                    @foreach ($sites as $site)
                        <th style="text-align:center;padding:6px 8px;">📍 {{ $site->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach ($stockMatrix as $row)
                <tr style="border-bottom:1px solid #f9fafb;">
                    <td style="padding:8px 0;font-weight:600;">{{ $row['package']->name }}</td>
                    @foreach ($sites as $site)
                        <td style="text-align:center;padding:8px;">
                            <span class="pill pill-{{ $row['bySite'][$site->id] < 10 ? 'danger' : 'success' }}">{{ $row['bySite'][$site->id] }}</span>
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div></div>

<div class="citi-card"><div class="citi-card-body">
    <h3 style="font-size:15px;margin-bottom:12px;">Most Popular Package</h3>
    @if ($mostPopularPackage)
        <p style="font-size:18px;font-weight:700;color:#0B2A3B;margin:0;">{{ $mostPopularPackage->package->name }} — {{ $mostPopularPackage->sales }} sales</p>
    @else
        <p style="color:#6b7280;">No sales yet.</p>
    @endif
</div></div>
@endsection
