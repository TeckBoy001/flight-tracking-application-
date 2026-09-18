<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SkyBook') &mdash; Travel with Confidence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3f12',
                            900: '#7c2d12',
                        },
                        navy: {
                            950: '#0f172a',
                            900: '#111827',
                        },
                    },
                },
            },
        };
    </script>
    @stack('head')
</head>
<body class="bg-stone-50 text-slate-900 min-h-screen flex flex-col antialiased">

    <nav class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-brand-500 text-white text-lg font-bold shadow-sm">S</span>
                    <span class="text-xl font-black tracking-tight text-slate-900">SkyBook</span>
                </a>

                <div class="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-700">
                    <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Home</a>
                    <a href="{{ route('about') }}" class="hover:text-brand-600 transition">About</a>
                    <a href="{{ route('destinations') }}" class="hover:text-brand-600 transition">Destinations</a>
                    <a href="{{ route('travel-information') }}" class="hover:text-brand-600 transition">Travel Info</a>
                    <a href="{{ route('flight-safety') }}" class="hover:text-brand-600 transition">Safety</a>
                    <a href="{{ route('faq') }}" class="hover:text-brand-600 transition">FAQ</a>
                    <a href="{{ route('contact') }}" class="hover:text-brand-600 transition">Contact</a>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('flights.search') }}" class="hidden md:inline text-slate-700 hover:text-brand-600 transition">Search Flights</a>
                    <a href="{{ route('track.index') }}" class="hidden md:inline text-slate-700 hover:text-brand-600 transition">Track Flight</a>

                    @auth
                        <a href="{{ route('bookings.index') }}" class="hidden md:inline text-slate-700 hover:text-brand-600 transition">My Bookings</a>

                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-brand-700 hover:text-brand-800 font-semibold">Admin</a>
                        @endif

                        <span class="hidden md:inline text-slate-300">|</span>
                        <span class="hidden md:inline text-slate-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button class="bg-slate-900 text-white px-3 py-2 rounded-md hover:bg-slate-800 transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline text-slate-700 hover:text-brand-600 transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-brand-500 text-white px-4 py-2.5 rounded-md font-semibold hover:bg-brand-600 transition shadow-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            @if (session('status'))
                <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-slate-950 text-slate-300 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-500 text-white font-bold text-sm">S</span>
                        <span class="text-lg font-bold text-white">SkyBook</span>
                    </div>
                    <p class="text-sm text-slate-400">Professional flight booking and tracking support for modern travelers.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400 mb-3">Explore</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-brand-300">Home</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-brand-300">About</a></li>
                        <li><a href="{{ route('destinations') }}" class="hover:text-brand-300">Destinations</a></li>
                        <li><a href="{{ route('track.index') }}" class="hover:text-brand-300">Track Flight</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400 mb-3">Support</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('travel-information') }}" class="hover:text-brand-300">Travel Info</a></li>
                        <li><a href="{{ route('flight-safety') }}" class="hover:text-brand-300">Flight Safety</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-brand-300">FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-brand-300">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400 mb-3">Need help?</h3>
                    <ul class="space-y-2 text-sm">
                        <li>support@skybook.example</li>
                        <li>+1 (800) 555-0147</li>
                        <li>24/7 support</li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-slate-800 pt-6 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
                <span>&copy; {{ date('Y') }} SkyBook. All rights reserved.</span>
                <span>Travel with confidence.</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
