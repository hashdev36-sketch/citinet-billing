@extends('layouts.admin')

@section('title', 'Import Vouchers — Citinet Admin')

@section('content')
<h2 style="font-size:22px;font-weight:800;color:#0B2A3B;margin-bottom:20px;">Import Voucher Stock</h2>

<div class="citi-card" style="max-width:560px;"><div class="citi-card-body">
    @if (session('import_errors') && count(session('import_errors')))
        <div class="pill pill-warning" style="display:block;padding:12px 14px;margin-bottom:14px;">
            <strong>Some rows were skipped:</strong>
            <ul style="margin:6px 0 0 18px;padding:0;">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.vouchers.import') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Default Location (used if CSV has no site column)</label>
            <select name="site_id" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;">
                <option value="">— none, site column required in CSV —</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}">📍 {{ $site->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Default Package (used if CSV has no package column)</label>
            <select name="package_id" style="width:100%;padding:12px 14px;border:1.5px solid #d1d5db;border-radius:12px;">
                <option value="">— none, package column required in CSV —</option>
                @foreach ($packages as $pkg)
                    <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:18px;">
            <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">CSV File</label>
            <input type="file" name="csv_file" accept=".csv,.txt" required style="width:100%;">
            <div style="font-size:12px;color:#9ca3af;margin-top:6px;">
                Format: <code>username,password</code> or <code>username,password,package_slug</code> or
                <code>username,password,package_slug,site_slug</code>. If a row includes its own package/site,
                that overrides the defaults above. Duplicates (same location + package + username) are skipped automatically.
            </div>
        </div>
        <button class="btn-citi btn-citi-primary">Import</button>
    </form>
</div></div>
@endsection
