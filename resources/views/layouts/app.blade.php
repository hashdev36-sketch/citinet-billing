<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Citinet WiFi — Fast, Affordable Internet Vouchers')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/citinet-brand.css') }}">
    @stack('styles')
</head>
<body>
    <div class="citi-nav">
        <div class="citi-nav-inner">
            <a class="citi-brand" href="{{ route('home') }}">⚡ Citinet WiFi</a>
            <div class="citi-nav-links">
                <a href="{{ route('packages.index') }}">Packages</a>
                <a href="{{ route('home') }}#how-it-works">How it Works</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-citi btn-citi-primary btn-citi-sm">My Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}" class="btn-citi btn-citi-primary btn-citi-sm">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="citi-section" style="padding-bottom:0"><div class="pill pill-success" style="display:block;padding:12px 16px;">{{ session('success') }}</div></div>
    @endif
    @if (session('warning'))
        <div class="citi-section" style="padding-bottom:0"><div class="pill pill-warning" style="display:block;padding:12px 16px;">{{ session('warning') }}</div></div>
    @endif
    @if (session('error'))
        <div class="citi-section" style="padding-bottom:0"><div class="pill pill-danger" style="display:block;padding:12px 16px;">{{ session('error') }}</div></div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="citi-footer">
        <div class="citi-footer-inner">
            <div style="display:flex; flex-wrap:wrap; gap:32px; margin-bottom:8px;">
                <div style="flex:1; min-width:200px;">
                    <h6>⚡ Citinet WiFi</h6>
                    <p style="font-size:13px;">Fast, affordable internet access wherever you see the Citinet hotspot.</p>
                </div>
                <div style="flex:1; min-width:160px;">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="{{ route('packages.index') }}">Packages</a></li>
                        <li><a href="{{ route('home') }}#faq">FAQ</a></li>
                        <li><a href="{{ route('login') }}">My Account</a></li>
                    </ul>
                </div>
                <div style="flex:1; min-width:200px;">
                    <h6>Support</h6>
                    <ul>
                        <li>📱 WhatsApp: {{ \App\Models\Setting::get('business_whatsapp', '0808 652 2739') }}</li>
                        <li>💬 Telegram: @Citihub_ng</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="footnote">Fair Usage Policy applies • &copy; {{ date('Y') }} Citinet WiFi. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
