@extends('layouts.admin')

@section('title', 'Packages — Citinet Admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin:0;">Packages</h2>
    <a href="{{ route('admin.packages.create') }}" class="btn-citi btn-citi-primary btn-citi-sm">+ New Package</a>
</div>
<div class="citi-card"><div class="citi-card-body">
    <table style="width:100%;font-size:13px;border-collapse:collapse;">
        <thead><tr style="text-align:left;color:#6b7280;border-bottom:1px solid #f3f4f6;"><th style="padding:8px 0;">Name</th><th>Price</th><th>Duration</th><th>Vouchers</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach ($packages as $package)
            <tr style="border-bottom:1px solid #f9fafb;">
                <td style="padding:10px 0;">{{ $package->name }}</td>
                <td>&#8358;{{ number_format($package->price) }}</td>
                <td>{{ $package->duration_label }}</td>
                <td>{{ $package->vouchers_count }}</td>
                <td><span class="pill pill-{{ $package->is_active ? 'success' : 'neutral' }}">{{ $package->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn-citi btn-citi-outline btn-citi-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" style="display:inline" onsubmit="return confirm('Remove this package?')">
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
