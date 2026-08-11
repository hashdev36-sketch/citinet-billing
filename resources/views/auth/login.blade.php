@extends('layouts.app')

@section('title', 'Login — Citinet WiFi')

@section('content')
    <div class="auth-shell">
        <div class="auth-header">
            <div class="auth-header-inner">
                <a href="{{ route('home') }}" class="brand-link"><span class="brand-mark">⚡</span><span class="brand-name">Citinet WiFi</span></a>
                <div class="auth-nav">
                    <a href="{{ route('register') }}" class="btn-citi btn-citi-outline btn-citi-sm">Sign Up</a>
                </div>
            </div>
        </div>

        <div class="auth-main">
            <div class="auth-page-grid">
                <div class="auth-hero-panel">
                    <h2 style="font-size:28px; margin-bottom:8px;">Welcome back</h2>
                    <p style="color:#475569;">Log in to manage your vouchers and view receipts.</p>
                </div>
                <div class="auth-form-panel">
                    <div class="form-card">
                        <div class="form-card-header">
                            <h2>Sign in</h2>
                        </div>
                        <div style="padding:20px;">
                            @if (session('status'))
                                <div class="pill pill-info">{{ session('status') }}</div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="field-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" class="w-full form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                                    @error('email') <div class="text-muted">{{ $message }}</div> @enderror
                                </div>

                                <div class="field-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" class="w-full form-input" type="password" name="password" required autocomplete="current-password" />
                                    @error('password') <div class="text-muted">{{ $message }}</div> @enderror
                                </div>

                                <div class="block mt-4">
                                    <label for="remember_me" class="inline-flex items-center">
                                        <input id="remember_me" type="checkbox" name="remember" />
                                        <span class="ms-2 text-sm text-muted">Remember me</span>
                                    </label>
                                </div>

                                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:12px;">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="link-secondary">Forgot your password?</a>
                                    @endif
                                    <button type="submit" class="btn-citi btn-citi-primary">Log in</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
