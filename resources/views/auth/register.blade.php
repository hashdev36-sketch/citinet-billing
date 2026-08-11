@extends('layouts.app')

@section('title', 'Sign up — Citinet WiFi')

@section('content')
    <div class="auth-shell">
        <div class="auth-header">
            <div class="auth-header-inner">
                <a href="{{ route('home') }}" class="brand-link"><span class="brand-mark">⚡</span><span class="brand-name">Citinet WiFi</span></a>
                <div class="auth-nav">
                    <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>

        <div class="auth-main">
            <div class="auth-page-grid">
                <div class="auth-hero-panel">
                    <h2 style="font-size:28px; margin-bottom:8px;">Welcome to Citinet</h2>
                    <p style="color:#475569;">Create an account to buy vouchers, manage receipts and access your dashboard.</p>
                </div>
                <div class="auth-form-panel">
                    <div class="form-card">
                        <div class="form-card-header">
                            <h2>Sign up</h2>
                        </div>
                        <div style="padding:20px;">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="field-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input id="name" class="w-full form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                                    @error('name') <div class="text-muted">{{ $message }}</div> @enderror
                                </div>

                                <div class="field-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" class="w-full form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                                    @error('email') <div class="text-muted">{{ $message }}</div> @enderror
                                </div>

                                <div class="field-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" class="w-full form-input" type="password" name="password" required autocomplete="new-password" />
                                    @error('password') <div class="text-muted">{{ $message }}</div> @enderror
                                </div>

                                <div class="field-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input id="password_confirmation" class="w-full form-input" type="password" name="password_confirmation" required autocomplete="new-password" />
                                </div>

                                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:12px;">
                                    <a href="{{ route('login') }}" class="link-secondary">Already registered?</a>
                                    <button type="submit" class="btn-citi btn-citi-primary">Create account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
