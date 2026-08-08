<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — Citinet Billing')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/citinet-brand.css') }}">
</head>
<body>
<div style="display:flex;">
    <div class="admin-sidebar" style="display:flex; flex-direction:column; padding:8px;">
        <div class="brand">⚡ Citinet Admin</div>
        <nav style="flex:1;">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">📦 Packages</a>
            <a href="{{ route('admin.sites.index') }}" class="{{ request()->routeIs('admin.sites.*') ? 'active' : '' }}">📍 Locations</a>
            <a href="{{ route('admin.vouchers.index') }}" class="{{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">🎫 Voucher Inventory</a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">🧾 Orders &amp; Payments</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}" style="padding:8px;">
            @csrf
            <button class="btn-citi btn-citi-secondary btn-citi-sm" style="width:100%;">🚪 Logout</button>
        </form>
    </div>
    <div class="admin-main">
        @if (session('success'))
            <div class="pill pill-success" style="display:block; padding:12px 16px; margin-bottom:16px;">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</div>
</body>
</html>
