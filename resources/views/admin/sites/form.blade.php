@extends('layouts.admin')

@section('title', ($site->exists ? 'Edit' : 'New') . ' Location — Citinet Admin')

@section('content')
<h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin-bottom:20px;">{{ $site->exists ? 'Edit' : 'New' }} Location</h2>

<div class="citi-card" style="max-width:560px;"><div class="citi-card-body">
    <form method="POST" action="{{ $site->exists ? route('admin.sites.update', $site) : route('admin.sites.store') }}">
        @csrf
        @if ($site->exists) @method('PUT') @endif

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Name</label>
            <input type="text" name="name" placeholder="e.g. Citinet 5" value="{{ old('name', $site->name) }}" required style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
            @if ($site->exists)
                <div style="font-size:12px;color:#9ca3af;margin-top:6px;">Slug: <code>{{ $site->slug }}</code> (used in CSV imports — not editable after creation to avoid breaking existing import scripts)</div>
            @endif
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Address / Description</label>
            <input type="text" name="address" placeholder="e.g. Along Airport Road, opposite the market" value="{{ old('address', $site->address) }}" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">WhatsApp Number (optional, overrides global support number for this location)</label>
            <input type="text" name="whatsapp_number" placeholder="e.g. 0808 652 2739" value="{{ old('whatsapp_number', $site->whatsapp_number) }}" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Telegram Admin Chat ID (optional — for routing site-specific bot notifications)</label>
            <input type="text" name="telegram_admin_chat_id" value="{{ old('telegram_admin_chat_id', $site->telegram_admin_chat_id) }}" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $site->sort_order ?? 0) }}" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;">
        </div>
        <div style="margin-bottom:18px;">
            <label style="font-size:13px;color:#374151;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $site->is_active ?? true) ? 'checked' : '' }}> Active (visible to customers at checkout)</label>
        </div>
        <button class="btn-citi btn-citi-primary">Save Location</button>
    </form>
</div></div>
@endsection
