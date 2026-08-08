@extends('layouts.admin')

@section('title', 'Voucher Inventory — Citinet Admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin:0;">Voucher Inventory</h2>
    <a href="{{ route('admin.vouchers.import.form') }}" class="btn-citi btn-citi-primary btn-citi-sm">⬆ Import CSV</a>
</div>

<form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    <select name="site_id" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
        <option value="">All Locations</option>
        @foreach ($sites as $site)
            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>📍 {{ $site->name }}</option>
        @endforeach
    </select>
    <select name="package_id" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
        <option value="">All Packages</option>
        @foreach ($packages as $pkg)
            <option value="{{ $pkg->id }}" {{ request('package_id') == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }}</option>
        @endforeach
    </select>
    <select name="status" style="padding:8px 12px;border:1.5px solid #d1d5db;border-radius:10px;">
        <option value="">All Statuses</option>
        @foreach (['unused', 'reserved', 'sold', 'expired'] as $status)
            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="btn-citi btn-citi-outline btn-citi-sm">Filter</button>
</form>

<div class="citi-card"><div class="citi-card-body">
    <table style="width:100%;font-size:13px;border-collapse:collapse;">
        <thead><tr style="text-align:left;color:#6b7280;border-bottom:1px solid #f3f4f6;"><th style="padding:8px 0;">Username</th><th>Password</th><th>Location</th><th>Package</th><th>Status</th><th>Customer</th><th>Sold At</th></tr></thead>
        <tbody>
        @foreach ($vouchers as $voucher)
            <tr style="border-bottom:1px solid #f9fafb;">
                <td style="padding:10px 0;"><code>{{ $voucher->username }}</code></td>
                <td><code>{{ $voucher->revealPassword() }}</code></td>
                <td>📍 {{ $voucher->site->name }}</td>
                <td>{{ $voucher->package->name }}</td>
                <td><span class="pill pill-{{ ['unused'=>'neutral','reserved'=>'warning','sold'=>'success','expired'=>'danger'][$voucher->status] }}">{{ ucfirst($voucher->status) }}</span></td>
                <td>{{ $voucher->customer->name ?? '—' }}</td>
                <td>{{ $voucher->sold_at?->format('d M Y H:i') ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $vouchers->links() }}
</div></div>
@endsection
