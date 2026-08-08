@extends('layouts.admin')

@section('title', ($package->exists ? 'Edit' : 'New') . ' Package — Citinet Admin')

@section('content')
<h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin-bottom:20px;">{{ $package->exists ? 'Edit' : 'New' }} Package</h2>

<div class="citi-card" style="max-width:560px;"><div class="citi-card-body">
    <form method="POST" action="{{ $package->exists ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
        @csrf
        @if ($package->exists) @method('PUT') @endif
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Name</label>
            <input type="text" name="name" value="{{ old('name', $package->name) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="display:flex;gap:12px;margin-bottom:14px;">
            <div style="flex:1;">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Price (&#8358;)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $package->price) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
            </div>
            <div style="flex:1;">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Device Limit</label>
                <input type="number" name="device_limit" value="{{ old('device_limit', $package->device_limit ?? 1) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-bottom:14px;">
            <div style="flex:1;">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Duration Label</label>
                <input type="text" name="duration_label" placeholder="e.g. 24 Hours" value="{{ old('duration_label', $package->duration_label) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
            </div>
            <div style="flex:1;">
                <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
            </div>
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Description</label>
            <textarea name="description" rows="3" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">{{ old('description', $package->description) }}</textarea>
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="margin-bottom:18px;">
            <label style="font-size:13px;color:#374151;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}> Active (visible to customers)</label>
        </div>
        <button class="btn-citi btn-citi-primary">Save Package</button>
    </form>
</div></div>
@endsection
