@extends('layouts.admin')

@section('title', 'Locations — Citinet Admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin:0;">Locations</h2>
    <a href="{{ route('admin.sites.create') }}" class="btn-citi btn-citi-primary btn-citi-sm">+ Add Location</a>
</div>

<p style="font-size:13px;color:#6b7280;margin-bottom:16px;">
    Each location has its own independent voucher stock. Adding a new hotspot site here is all it takes —
    no code changes needed. Once added, you can import CSV voucher stock for it and it'll appear as a
    checkout option to customers automatically.
</p>

<div class="citi-card"><div class="citi-card-body">
    <table style="width:100%;font-size:13px;border-collapse:collapse;">
        <thead><tr style="text-align:left;color:#6b7280;border-bottom:1px solid #f3f4f6;"><th style="padding:8px 0;">Name</th><th>Slug</th><th>Address</th><th>Vouchers</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach ($sites as $site)
            <tr style="border-bottom:1px solid #f9fafb;">
                <td style="padding:10px 0;">📍 {{ $site->name }}</td>
                <td><code>{{ $site->slug }}</code></td>
                <td>{{ $site->address ?? '—' }}</td>
                <td>{{ $site->vouchers_count }}</td>
                <td><span class="pill pill-{{ $site->is_active ? 'success' : 'neutral' }}">{{ $site->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    <a href="{{ route('admin.sites.edit', $site) }}" class="btn-citi btn-citi-outline btn-citi-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.sites.destroy', $site) }}" style="display:inline" onsubmit="return confirm('Remove this location?')">
                        @csrf @method('DELETE')
                        <button class="btn-citi btn-citi-secondary btn-citi-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
@endsection
