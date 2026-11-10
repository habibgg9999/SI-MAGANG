<div class="space-y-6">
    <!-- Brand Logo & Optional Mobile Close Button -->
    <div class="flex items-center justify-between px-2 pt-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-500 flex items-center justify-center text-white shadow-md shadow-blue-500/30 group-hover:scale-105 transition-transform shrink-0">
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

        @if(!empty($isMobile))
            <button type="button" 
                    onclick="closeMobileSidebar()" 
                    aria-label="Tutup menu sidebar"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

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
<div class="space-y-4 pt-4 mt-auto">
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
