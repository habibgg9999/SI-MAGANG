<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Magang') - SIMAGANG</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite & CDN Fallback for complete styling consistency) -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 20px -3px rgba(37, 99, 235, 0.25)',
                        'oxygen': '0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 10px 25px -5px rgba(37, 99, 235, 0.04)',
                        'oxygen-lg': '0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 20px 30px -10px rgba(37, 99, 235, 0.07)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11', 'tnum';
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
        .shadow-oxygen {
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 10px 25px -5px rgba(37, 99, 235, 0.04);
        }
        .shadow-oxygen-lg {
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 20px 30px -10px rgba(37, 99, 235, 0.07);
        }
        .tactile-btn {
            transition: all 150ms ease-out;
        }
        .tactile-btn:hover {
            transform: translateY(-2px);
        }
        .tactile-btn:active {
            transform: translateY(0) scale(0.99);
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }
        .pulse-live {
            animation: pulse-dot 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased bg-slate-50 selection:bg-blue-600 selection:text-white">

    <!-- TOP BAR: User Simulator & Quick Switcher -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-oxygen transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('companies.index') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-lg text-slate-900 tracking-tight">SIMAGANG</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-live"></span>
                                    v2.4
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 hidden sm:block font-medium">Sistem Informasi Magang Kampus</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                @php
                    $currentUser = Auth::user();
                    $allDummyUsers = \App\Models\User::orderBy('role', 'desc')->get();
                @endphp
                <nav class="hidden md:flex items-center gap-1.5">
                    <!-- Dashboard Tracker -->
                    <a href="{{ route('dashboard') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 shadow-2xs border border-brand-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Radar Instansi -->
                    <a href="{{ route('companies.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'bg-brand-50 text-brand-700 shadow-2xs border border-brand-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Radar Kuota</span>
                    </a>

                    <!-- Kelompok Magang -->
                    <a href="{{ route('groups.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('groups.*') ? 'bg-brand-50 text-brand-700 shadow-2xs border border-brand-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('groups.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Kelompok</span>
                    </a>

                    <!-- Forum Matchmaking -->
                    <a href="{{ route('forum.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 {{ request()->routeIs('forum.*') ? 'bg-brand-50 text-brand-700 shadow-2xs border border-brand-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('forum.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Forum Tim</span>
                    </a>

                    <!-- Nav Koordinator Admin -->
                    @if($currentUser && $currentUser->isKoordinator())
                        <div class="h-4 w-px bg-slate-200 mx-1"></div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('admin.*') ? 'bg-purple-100 text-purple-800 border border-purple-300' : 'text-purple-700 hover:bg-purple-50 hover:text-purple-900' }}">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Panel Admin</span>
                        </a>
                    @endif
                </nav>

                <!-- Role Switcher & Mobile Toggle -->
                <div class="flex items-center gap-2">
                    <!-- Simulator Switch User -->
                    <form action="{{ route('dev.switch-user') }}" method="POST" class="flex items-center gap-1.5 bg-slate-100/90 p-1 rounded-xl border border-slate-200/90 shadow-2xs">
                        @csrf
                        <div class="flex items-center gap-1.5 pl-2 pr-1 text-[11px] font-semibold text-slate-600">
                            <span class="w-2 h-2 rounded-full {{ $currentUser ? ($currentUser->isKoordinator() ? 'bg-purple-500' : 'bg-emerald-500') : 'bg-slate-400' }}"></span>
                            <span class="hidden xl:inline">Role:</span>
                        </div>
                        <select name="user_id" onchange="this.form.submit()" class="text-xs bg-white font-medium text-slate-800 rounded-lg px-2.5 py-1.5 border border-slate-200 shadow-2xs focus:ring-2 focus:ring-brand-500 focus:outline-hidden cursor-pointer max-w-[180px] sm:max-w-[220px] truncate">
                            <option value="guest" {{ !$currentUser ? 'selected' : '' }}>👤 Mode Tamu (Guest)</option>
                            <optgroup label="Koordinator Magang">
                                @foreach($allDummyUsers->where('role', 'koordinator') as $u)
                                    <option value="{{ $u->id }}" {{ $currentUser && $currentUser->id == $u->id ? 'selected' : '' }}>
                                        🎓 {{ $u->name }} (Koordinator)
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Mahasiswa / Ketua Kelompok">
                                @foreach($allDummyUsers->where('role', 'mahasiswa') as $u)
                                    @php
                                        $led = $u->ledGroups()->first();
                                    @endphp
                                    <option value="{{ $u->id }}" {{ $currentUser && $currentUser->id == $u->id ? 'selected' : '' }}>
                                        👨‍🎓 {{ $u->name }} {{ $led ? '[Ketua: '.$led->name.']' : '[Mahasiswa]' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </form>

                    <!-- Mobile Hamburger Button -->
                    <button type="button" 
                            id="mobileMenuBtn" 
                            class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" 
                            aria-label="Toggle Navigation">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white/95 px-4 pt-3 pb-4 space-y-1 backdrop-blur-md">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                📊 Dashboard Mahasiswa
            </a>
            <a href="{{ route('companies.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                🎯 Radar Kuota Instansi
            </a>
            <a href="{{ route('groups.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('groups.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                👥 Kelompok Magang
            </a>
            <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('forum.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                💬 Forum Matchmaking Tim
            </a>
            @if($currentUser && $currentUser->isKoordinator())
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-purple-600">Admin Area</p>
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-purple-700 hover:bg-purple-50">
                        🛡️ Dashboard Koordinator
                    </a>
                    <a href="{{ route('admin.companies.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-purple-700 hover:bg-purple-50">
                        🏢 Kelola Instansi Mitra
                    </a>
                    <a href="{{ route('admin.requests.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-purple-700 hover:bg-purple-50">
                        📋 Review Usulan Mahasiswa
                    </a>
                </div>
            @endif
        </div>
    </header>

    <!-- FLASH MESSAGES & SYSTEM ALERTS -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div id="flash-success" class="flex items-start justify-between gap-3 p-4 mb-4 rounded-2xl bg-emerald-50/90 border border-emerald-200 text-emerald-800 shadow-sm transition-all animate-fade-in">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs sm:text-sm font-medium pt-1">
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flash-success').remove()" class="text-emerald-500 hover:text-emerald-800 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-error" class="flex items-start justify-between gap-3 p-4 mb-4 rounded-2xl bg-rose-50/90 border border-rose-200 text-rose-800 shadow-sm transition-all animate-fade-in">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs sm:text-sm font-medium pt-1">
                        {{ session('error') }}
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flash-error').remove()" class="text-rose-500 hover:text-rose-800 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div id="flash-info" class="flex items-start justify-between gap-3 p-4 mb-4 rounded-2xl bg-indigo-50/90 border border-indigo-200 text-indigo-800 shadow-sm transition-all animate-fade-in">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs sm:text-sm font-medium pt-1">
                        {{ session('info') }}
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flash-info').remove()" class="text-indigo-500 hover:text-indigo-800 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- MAIN APPLICATION CONTENT -->
    <main class="flex-1 pb-16">
        @yield('content')
    </main>

    <!-- CLEAN FOOTER -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-bold text-slate-800">SIMAGANG Radar Kuota</span>
                <span>&bull; Real-time Concurrency & Zero-Trust Architecture</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500">
                <span>🟢 Hijau (&lt; 50%)</span>
                <span>🟡 Kuning (50% - 89%)</span>
                <span>🔴 Merah (&ge; 90%)</span>
            </div>
        </div>
    </footer>

    <!-- ZERO-BUG POLISH: Global JavaScript Handlers -->
    <script>
        // 1. Mobile Menu Drawer Toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // 2. Anti Double-Submit Guard & Visual Loading Spinner
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            form.dataset.submitting = 'true';

            const submitBtn = form.querySelector('button[type="submit"]:not([data-no-disable])');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.dataset.originalContent = originalHtml;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                `;
            }
        });

        // 3. Auto-Dismiss Flash Messages after 6 seconds
        setTimeout(() => {
            ['flash-success', 'flash-error', 'flash-info'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => el.remove(), 500);
                }
            });
        }, 6000);
    </script>

    @stack('scripts')
</body>
</html>
