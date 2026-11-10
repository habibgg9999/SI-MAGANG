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

    <!-- 1. LEFT SIDEBAR (DARK NAVY - MATCH FIGMA) -->
    <aside class="w-64 bg-[#0c1322] text-slate-300 min-h-screen flex flex-col justify-between p-4 shrink-0 border-r border-slate-800/80 sticky top-0 h-screen z-40 hidden md:flex">
        
        <div class="space-y-6">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 pt-1 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-500 flex items-center justify-center text-white shadow-md shadow-blue-500/30 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-base text-white tracking-tight leading-none">SI-MAGANG</h1>
                    <p class="text-[9px] font-semibold text-slate-400 tracking-wider uppercase mt-1">Langkah Awal Kariermu</p>
                </div>
            </a>

            <!-- Workspace Selector -->
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2 mb-1.5">Ruang Kerja</span>
                <div class="bg-[#111c2e] border border-slate-800/90 rounded-xl px-3 py-2 flex items-center justify-between text-xs font-semibold text-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>{{ $currentUser && $currentUser->isKoordinator() ? 'Koordinator' : 'Mahasiswa' }}</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </div>
            </div>

            <!-- Main Navigation Links -->
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2 mb-2">Menu Utama</span>
                <nav class="space-y-1">
                    <!-- Ringkasan (Dashboard) -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#4338ca] text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Ringkasan</span>
                    </a>

                    <!-- Radar Kuota -->
                    <a href="{{ route('companies.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'bg-[#4338ca] text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('companies.*') && !request()->routeIs('admin.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Radar Kuota</span>
                    </a>

                    <!-- Kelompok -->
                    <a href="{{ route('groups.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('groups.*') ? 'bg-[#4338ca] text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('groups.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Kelompok</span>
                    </a>

                    <!-- Forum Tim -->
                    <a href="{{ route('forum.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('forum.*') ? 'bg-[#4338ca] text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('forum.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Forum Tim</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#1a263d] text-slate-300">3</span>
                    </a>

                    <!-- Admin Link (Jika Koordinator) -->
                    @if($currentUser && $currentUser->isKoordinator())
                        <div class="pt-2 border-t border-slate-800/80 mt-2">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-purple-300 hover:text-white hover:bg-purple-900/40">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Panel Koordinator</span>
                            </a>
                        </div>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Bottom Widgets -->
        <div class="space-y-4 pt-4">
            <!-- Help Box Widget -->
            <div class="bg-[#111c2e] border border-slate-800/80 rounded-2xl p-4 text-xs">
                <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 mb-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="font-bold text-white text-xs">Butuh panduan?</h4>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Temukan alur dan ketentuan program magang.</p>
                <a href="#" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-300 hover:text-white mt-2.5 group">
                    <span>Buka pusat bantuan</span>
                    <span class="group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">↗</span>
                </a>
            </div>

            <!-- User Profile Box -->
            <div class="border-t border-slate-800/80 pt-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-slate-800 text-slate-200 font-bold text-xs flex items-center justify-center border border-slate-700 shrink-0">
                        {{ $userInitials }}
                    </div>
                    <div class="truncate">
                        <p class="font-bold text-xs text-white truncate leading-tight">{{ $currentUser?->name ?? 'Aditya Nugraha' }}</p>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $currentUser?->nim ?? '22.11.4821' }} • {{ $currentUser?->prodi ?? 'Informatika' }}</p>
                    </div>
                </div>

                <a href="{{ route('home') }}" title="Ganti Mode / Kembali" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800/80 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- 2. MAIN CONTENT AREA (RIGHT) -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
        
        <!-- TOP BREADCRUMB & HEADER BAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <!-- Left: Breadcrumb -->
            <div class="flex items-center gap-2 text-xs font-medium">
                <span class="text-slate-400">Ruang kerja</span>
                <span class="text-slate-300">›</span>
                <span class="font-semibold text-slate-800">
                    @if(request()->routeIs('dashboard'))
                        Ringkasan
                    @elseif(request()->routeIs('companies.*'))
                        Radar Kuota
                    @elseif(request()->routeIs('groups.*'))
                        Kelompok
                    @elseif(request()->routeIs('forum.*'))
                        Forum Tim
                    @else
                        Halaman
                    @endif
                </span>
            </div>

            <!-- Right: Action Icons & Simulator -->
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex px-3 py-1 bg-slate-50 text-slate-600 text-xs rounded-full border border-slate-200/80 font-medium">
                    Semester Ganjil 2026/2027
                </span>

                <!-- Quick Switch Simulator (Data Contoh) -->
                <form action="{{ route('dev.switch-user') }}" method="POST" class="inline-flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-xl border border-slate-200/80">
                    @csrf
                    <span class="text-[11px] font-semibold text-slate-500 hidden md:inline">Data:</span>
                    <select name="user_id" onchange="this.form.submit()" class="text-xs bg-transparent font-medium text-slate-700 outline-none cursor-pointer max-w-[150px] truncate">
                        @foreach($allDummyUsers as $u)
                            <option value="{{ $u->id }}" {{ $currentUser && $currentUser->id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Notification Bell with Dot -->
                <button type="button" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 relative transition-colors" aria-label="Notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-blue-600 absolute top-1.5 right-1.5 ring-2 ring-white"></span>
                </button>

                <!-- Avatar Circle -->
                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-100 shrink-0">
                    {{ $userInitials }}
                </div>
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
        <main class="flex-1 pb-16">
            @yield('content')
        </main>
    </div>

</body>
</html>
