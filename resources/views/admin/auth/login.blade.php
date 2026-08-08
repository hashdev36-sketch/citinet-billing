<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — Citinet Billing</title>
    <link rel="stylesheet" href="{{ asset('css/citinet-brand.css') }}">
    <style>body{background:linear-gradient(150deg,#0B2A3B 0%,#1C4E6F 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}</style>
</head>
<body>
<div style="max-width:380px;width:100%;">
    <div class="citi-card">
        <div class="citi-card-body">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="font-size:32px;">⚡</div>
                <h1 style="font-size:20px;font-weight:800;color:#0B2A3B;margin:6px 0 0;">Citinet Admin</h1>
            </div>
            @if ($errors->any())
                <div class="pill pill-danger" style="display:block; padding:10px 14px; margin-bottom:14px;">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Email</label>
                    <input type="email" name="email" required autofocus style="width:100%;padding:13px 16px;border:1.5px solid #d1d5db;border-radius:12px;font-size:16px;background:#f9fafb;">
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Password</label>
                    <input type="password" name="password" required style="width:100%;padding:13px 16px;border:1.5px solid #d1d5db;border-radius:12px;font-size:16px;background:#f9fafb;">
                </div>
                <button class="btn-citi btn-citi-primary" style="width:100%;" type="submit">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
