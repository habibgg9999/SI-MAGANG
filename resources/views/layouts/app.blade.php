<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Mahasiswa') - SI-MAGANG</title>

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
                        },
                        navy: {
                            900: '#0c1322',
                            800: '#111c2e',
                            700: '#1a263d',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex bg-slate-50">

    @php
        $currentUser = Auth::user();
        if (!$currentUser) {
            $currentUser = \App\Models\User::where('name', 'LIKE', '%Aditya%')->first() 
                ?? \App\Models\User::where('role', 'mahasiswa')->first();
        }
        $allDummyUsers = \App\Models\User::orderBy('role', 'desc')->get();
        $userInitials = 'AN';
        if ($currentUser && $currentUser->name) {
            $parts = explode(' ', trim($currentUser->name));
            $userInitials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
        }
    @endphp

    <!-- BACKDROP OVERLAY FOR MOBILE SIDEBAR -->
    <div id="mobile-sidebar-backdrop" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden" 
         onclick="closeMobileSidebar()"></div>

    <!-- MOBILE SIDEBAR DRAWER (SLIDE-OVER FROM LEFT) -->
    <aside id="mobile-sidebar" 
           class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-[#0c1322] text-slate-300 z-50 flex flex-col justify-between p-4 shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full md:hidden overflow-y-auto custom-scrollbar">
        @include('layouts.sidebar-content', ['isMobile' => true])
    </aside>

    <!-- 1. DESKTOP LEFT SIDEBAR (DARK NAVY - MATCH FIGMA) -->
    <aside class="w-64 bg-[#0c1322] text-slate-300 min-h-screen flex flex-col justify-between p-4 shrink-0 border-r border-slate-800/80 sticky top-0 h-screen z-40 hidden md:flex">
        @include('layouts.sidebar-content', ['isMobile' => false])
    </aside>

    <!-- 2. MAIN CONTENT AREA (RIGHT) -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
        
        <!-- TOP BREADCRUMB & HEADER BAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <!-- Left: Mobile Hamburger & Breadcrumb -->
            <div class="flex items-center gap-2.5 min-w-0">
                <!-- Hamburger Menu Button (Mobile Portrait & Tablet) -->
                <button type="button" 
                        onclick="openMobileSidebar()" 
                        aria-label="Buka navigasi sidebar" 
                        class="md:hidden p-2 -ml-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Breadcrumb -->
                <div class="flex items-center gap-1.5 sm:gap-2 text-xs font-medium truncate">
                    <span class="text-slate-400 hidden xs:inline">Ruang kerja</span>
                    <span class="text-slate-300 hidden xs:inline">›</span>
                    <span class="font-semibold text-slate-800 truncate">
                        @if(request()->routeIs('dashboard'))
                            Ringkasan
                        @elseif(request()->routeIs('companies.*'))
                            Radar Kuota
                        @elseif(request()->routeIs('groups.*'))
                            Kelompok
                        @elseif(request()->routeIs('forum.*'))
                            Forum Tim
                        @elseif(request()->routeIs('admin.*'))
                            Panel Koordinator
                        @else
                            Halaman
                        @endif
                    </span>
                </div>
            </div>

            <!-- Right: Action Icons & Simulator -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Semester Badge (Hanya tampil di Desktop/Layar Lebar) -->
                <span class="hidden lg:inline-flex px-3 py-1 bg-slate-50 text-slate-600 text-xs rounded-full border border-slate-200/80 font-medium whitespace-nowrap">
                    Semester Ganjil 2026/2027
                </span>

                <!-- Quick Switch Simulator (Data Contoh) -->
                <form action="{{ route('dev.switch-user') }}" method="POST" class="inline-flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-xl border border-slate-200/80 shrink-0">
                    @csrf
                    <span class="text-[11px] font-semibold text-slate-500 hidden md:inline">Data:</span>
                    <select name="user_id" onchange="this.form.submit()" class="text-xs bg-transparent font-medium text-slate-700 outline-none cursor-pointer max-w-[105px] sm:max-w-[150px] truncate">
                        @foreach($allDummyUsers as $u)
                            <option value="{{ $u->id }}" {{ $currentUser && $currentUser->id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Notification Bell with Dot -->
                <button type="button" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 relative transition-colors shrink-0" aria-label="Notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-blue-600 absolute top-1.5 right-1.5 ring-2 ring-white"></span>
                </button>

                <!-- Avatar Circle (Bisa diklik untuk buka drawer di mobile) -->
                <button type="button" onclick="openMobileSidebar()" class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-100 shrink-0 cursor-pointer md:cursor-default" title="{{ $currentUser?->name }}">
                    {{ $userInitials }}
                </button>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        @if(session('success'))
            <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 font-bold ml-2">&times;</button>
            </div>
        @endif
        @if(session('info'))
            <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs flex items-center justify-between">
                <span>{{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-indigo-500 font-bold ml-2">&times;</button>
            </div>
        @endif

        <!-- MAIN BODY VIEW -->
        <main class="flex-1 pb-24 md:pb-16">
            @yield('content')
        </main>
    </div>

    <!-- 3. MOBILE BOTTOM NAVIGATION BAR (FIXED ON SMARTPHONES) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[#0c1322]/95 backdrop-blur-md border-t border-slate-800/90 z-40 px-2 py-2 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.2)]">
        <!-- Ringkasan -->
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-[10px] leading-tight">Ringkasan</span>
        </a>

        <!-- Radar Kuota -->
        <a href="{{ route('companies.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="text-[10px] leading-tight">Radar</span>
        </a>

        <!-- Kelompok -->
        <a href="{{ route('groups.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all {{ request()->routeIs('groups.*') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('groups.*') ? 'text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[10px] leading-tight">Kelompok</span>
        </a>

        <!-- Forum Tim -->
        <a href="{{ route('forum.index') }}" 
           class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl transition-all relative {{ request()->routeIs('forum.*') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ request()->routeIs('forum.*') ? 'text-indigo-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="w-2 h-2 rounded-full bg-blue-500 absolute -top-0.5 -right-0.5"></span>
            </div>
            <span class="text-[10px] leading-tight">Forum</span>
        </a>

        <!-- Menu Drawer Trigger -->
        <button type="button" 
                onclick="openMobileSidebar()" 
                class="flex flex-col items-center gap-1 py-1 px-2.5 rounded-xl text-slate-400 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span class="text-[10px] leading-tight">Menu</span>
        </button>
    </nav>

    <!-- MOBILE SIDEBAR INTERACTIVITY SCRIPT -->
    <script>
        function openMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (sidebar && backdrop) {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (sidebar && backdrop) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
            }
        });
    </script>

</body>
</html>
