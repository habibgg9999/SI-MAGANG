@extends('layouts.app')

@section('title', 'Radar Kuota Instansi Mitra')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- HERO & RADAR STATS (THE "CLEAN OXYGEN & KINETIC BLUE" ARCHITECTURE) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-50/90 via-indigo-50/40 to-white ring-1 ring-blue-100/80 p-6 sm:p-10 shadow-oxygen">
        <!-- Dot-matrix Micro Background (24px size) -->
        <div class="absolute inset-0 opacity-35 bg-[radial-gradient(#93c5fd_0.8px,transparent_0.8px)] [background-size:20px_20px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <div class="space-y-3.5 max-w-2xl">
                <!-- Super-Title -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100/90 text-blue-800 border border-blue-200/80 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 pulse-live"></span>
                    <span class="uppercase tracking-wider text-[11px]">Monitoring Realtime SIMAGANG</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 leading-tight">
                    Radar Kuota Instansi Magang
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed">
                    Pantau ketersediaan slot kuota instansi mitra secara akurat dengan indikator <span class="text-slate-900 font-bold">Traffic Light Semiotik</span>. Kuota dihitung otomatis dari total mahasiswa kelompok yang statusnya <strong class="text-emerald-700 font-bold">Diterima</strong>.
                </p>

                <!-- Traffic Light Legend Pills (Frosted Crisp Badges) -->
                <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                    <span class="text-slate-500 font-bold text-[11px] uppercase tracking-wider">Status:</span>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'green']) }}" 
                       class="tactile-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50/80 text-emerald-800 border border-emerald-200/90 hover:bg-emerald-100 transition-all font-bold shadow-2xs {{ request('status') === 'green' ? 'ring-2 ring-emerald-500 shadow-sm' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 pulse-live"></span>
                        <span>🟢 Hijau: &lt; 50% (Longgar)</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'yellow']) }}" 
                       class="tactile-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50/80 text-amber-900 border border-amber-200/90 hover:bg-amber-100 transition-all font-bold shadow-2xs {{ request('status') === 'yellow' ? 'ring-2 ring-amber-500 shadow-sm' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-amber-500 ring-4 ring-amber-100"></span>
                        <span>🟡 Kuning: 50% - 89% (Terbatas)</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'red']) }}" 
                       class="tactile-btn inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50/80 text-rose-800 border border-rose-200/90 hover:bg-rose-100 transition-all font-bold shadow-2xs {{ request('status') === 'red' ? 'ring-2 ring-rose-500 shadow-sm' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                        <span>🔴 Merah: &ge; 90% (Penuh)</span>
                    </a>
                </div>
            </div>

            <!-- Bento Metric Cards (Glass-Surface & Semiotic Traffic Light Precision) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4 shrink-0">
                <!-- 1. Total Mitra -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-oxygen hover:border-slate-300 transition-all duration-150 text-center relative overflow-hidden group">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Total Mitra</p>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight tabular-nums mt-1.5">{{ $totalCompanies ?? 0 }}</p>
                    <span class="inline-block mt-1 text-[11px] font-semibold text-slate-600 bg-slate-100/90 px-2.5 py-0.5 rounded-full">Katalog Aktif</span>
                </div>
                <!-- 2. 🟢 Longgar -->
                <div class="bg-emerald-50/70 rounded-2xl p-4 sm:p-5 border border-emerald-200/80 shadow-oxygen hover:border-emerald-300 transition-all duration-150 text-center relative overflow-hidden group">
                    <div class="flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 pulse-live"></span>
                        <p class="text-[11px] text-emerald-800 font-extrabold uppercase tracking-wider">Longgar</p>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-emerald-700 tracking-tight tabular-nums mt-1.5">{{ $greenCount ?? 0 }}</p>
                    <span class="inline-block mt-1 text-[11px] font-bold text-emerald-800 bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-200/60">&lt; 50% Terisi</span>
                </div>
                <!-- 3. 🟡 Terbatas -->
                <div class="bg-amber-50/70 rounded-2xl p-4 sm:p-5 border border-amber-200/80 shadow-oxygen hover:border-amber-300 transition-all duration-150 text-center relative overflow-hidden group">
                    <div class="flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 ring-4 ring-amber-100"></span>
                        <p class="text-[11px] text-amber-800 font-extrabold uppercase tracking-wider">Terbatas</p>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-amber-800 tracking-tight tabular-nums mt-1.5">{{ $yellowCount ?? 0 }}</p>
                    <span class="inline-block mt-1 text-[11px] font-bold text-amber-900 bg-amber-100/80 px-2.5 py-0.5 rounded-full border border-amber-200/60">50% - 89%</span>
                </div>
                <!-- 4. 🔴 Penuh -->
                <div class="bg-rose-50/70 rounded-2xl p-4 sm:p-5 border border-rose-200/80 shadow-oxygen hover:border-rose-300 transition-all duration-150 text-center relative overflow-hidden group">
                    <div class="flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                        <p class="text-[11px] text-rose-800 font-extrabold uppercase tracking-wider">Penuh</p>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-rose-700 tracking-tight tabular-nums mt-1.5">{{ $redCount ?? 0 }}</p>
                    <span class="inline-block mt-1 text-[11px] font-bold text-rose-800 bg-rose-100/80 px-2.5 py-0.5 rounded-full border border-rose-200/60">&ge; 90% Terisi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FLOATING SEARCH BAR & CATEGORY PILLS FILTER -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-oxygen border border-slate-200/70 space-y-4">
        <form method="GET" action="{{ route('companies.index') }}" class="space-y-4">
            
            <!-- Floating Search Input with Kinetic Focus Ring -->
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama instansi mitra, bidang keahlian, kota, atau kata kunci..." 
                           class="w-full pl-11 pr-4 py-3 text-xs sm:text-sm bg-slate-50/80 hover:bg-slate-100/70 focus:bg-white text-slate-800 rounded-2xl border border-slate-200/80 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-hidden transition-all shadow-2xs">
                </div>

                <!-- Quick Category Status Filter Pills (Rounded Full & Blue Vibrant) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                       class="tactile-btn px-4 py-2.5 text-xs font-bold rounded-full transition-all whitespace-nowrap {{ !request('status') ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua Radar
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'green']) }}" 
                       class="tactile-btn px-4 py-2.5 text-xs font-bold rounded-full transition-all whitespace-nowrap flex items-center gap-1.5 {{ request('status') === 'green' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>🟢</span> Kuota Longgar
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'yellow']) }}" 
                       class="tactile-btn px-4 py-2.5 text-xs font-bold rounded-full transition-all whitespace-nowrap flex items-center gap-1.5 {{ request('status') === 'yellow' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>🟡</span> Terbatas
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'red']) }}" 
                       class="tactile-btn px-4 py-2.5 text-xs font-bold rounded-full transition-all whitespace-nowrap flex items-center gap-1.5 {{ request('status') === 'red' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>🔴</span> Penuh
                    </a>
                </div>
            </div>

            <!-- Dropdown Filter Fields (Tipe, Kota, Bidang Kerja, Urutan) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
                <!-- Tipe Instansi -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tipe Lembaga</label>
                    <select name="type" class="w-full text-xs font-semibold bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                        <option value="">Semua Lembaga (BUMN, Pemerintah, dll)</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                                {{ strtoupper($t) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kota / Wilayah -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Kota / Lokasi</label>
                    <select name="city" class="w-full text-xs font-semibold bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                        <option value="">Semua Lokasi</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bidang Kerja -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Bidang Kerja</label>
                    <select name="field" class="w-full text-xs font-semibold bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                        <option value="">Semua Bidang Keahlian</option>
                        @foreach($fields as $f)
                            <option value="{{ $f }}" {{ request('field') == $f ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::limit($f, 32) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sorting -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Urutkan Berdasarkan</label>
                    <select name="sort" class="w-full text-xs font-semibold bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-200 text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                        <option value="quota_asc" {{ request('sort') == 'quota_asc' ? 'selected' : '' }}>Peluang Terbesar (Kuota Terbuka)</option>
                        <option value="quota_desc" {{ request('sort') == 'quota_desc' ? 'selected' : '' }}>Paling Banyak Peminat (Penuh)</option>
                        <option value="quota_count_desc" {{ request('sort') == 'quota_count_desc' ? 'selected' : '' }}>Kapasitas Kuota Terbesar</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Instansi (A - Z)</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Terapkan & Reset Filter -->
            <div class="flex items-center justify-between pt-2 text-xs">
                <span class="text-slate-500">
                    Menampilkan <strong class="text-slate-900 font-bold">{{ $companies->total() }}</strong> instansi mitra resmi
                </span>
                <div class="flex items-center gap-2">
                    @if(request()->anyFilled(['search', 'type', 'city', 'field', 'status', 'sort']))
                        <a href="{{ route('companies.index') }}" class="px-3.5 py-2 rounded-xl text-rose-600 hover:bg-rose-50 font-bold transition-colors">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm hover:shadow transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- GRID DAFTAR INSTANSI MITRA (RESPONSIF MOBILE & LAPTOP) -->
    @if($companies->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($companies as $company)
                @php
                    $status = $company->quota_status;
                    $pct = $company->quota_percentage;
                    $accepted = $company->accepted_members_count;
                    $pending = $company->pending_members_count;
                    $remaining = $company->remaining_quota;
                    
                    $typeColors = [
                        'BUMN' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'pemerintah' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'swasta' => 'bg-sky-50 text-sky-700 border-sky-200',
                        'NGO' => 'bg-teal-50 text-teal-700 border-teal-200',
                    ];
                    $typeClass = $typeColors[$company->type] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                @endphp

                <!-- KARTU INSTANSI MODERN -->
                <div class="group bg-white rounded-3xl border border-slate-200/80 hover:border-blue-400 hover:shadow-card transition-all duration-300 flex flex-col overflow-hidden">
                    
                    <!-- Header Kartu: Tipe & Status Pill -->
                    <div class="p-5 pb-4 border-b border-slate-100 flex items-start justify-between gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-extrabold uppercase tracking-wider border {{ $typeClass }}">
                            {{ $company->type ?? 'INSTANSI' }}
                        </span>

                        <!-- Traffic Light Pill Badge -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-2xs {{ $status['badge_bg'] }}">
                            <span class="w-2 h-2 rounded-full {{ $status['dot_color'] }} animate-pulse"></span>
                            <span>{{ $status['label'] }}</span>
                            <span class="text-[10px] font-medium opacity-80">({{ $pct }}%)</span>
                        </div>
                    </div>

                    <!-- Body Kartu -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                <a href="{{ route('companies.show', $company) }}">
                                    {{ $company->name }}
                                </a>
                            </h2>

                            <!-- Metadata: Kota & Bidang Kerja (Null-Safe) -->
                            <div class="mt-2.5 space-y-1.5 text-xs text-slate-600">
                                <div class="flex items-center gap-1.5 text-slate-500">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="font-medium text-slate-700 truncate">{{ $company->city ?? 'Indonesia' }}</span>
                                </div>

                                <div class="flex items-start gap-1.5 text-slate-500">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="line-clamp-1 font-medium text-slate-700">{{ $company->field ?? 'Teknologi Informasi & Sistem' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar Visual Kuota -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-600">Alokasi Kuota</span>
                                <span class="font-bold text-slate-900">
                                    {{ $accepted }} / {{ $company->quota }} <span class="text-[10px] font-normal text-slate-500">Mahasiswa</span>
                                </span>
                            </div>

                            <!-- Progress Track & Animated Fill -->
                            <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $status['bar_color'] }}" 
                                     style="width: {{ $pct }}%"></div>
                            </div>

                            <!-- Breakdown Grid -->
                            <div class="grid grid-cols-3 gap-1 pt-1.5 text-[11px] text-center border-t border-slate-200/60">
                                <div>
                                    <span class="block text-slate-400 text-[10px]">Diterima</span>
                                    <strong class="text-slate-800 font-bold">{{ $accepted }}</strong>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[10px]">Sisa Slot</span>
                                    <strong class="{{ $remaining > 0 ? 'text-emerald-600' : 'text-rose-600' }} font-bold">
                                        {{ $remaining }}
                                    </strong>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[10px]">Menunggu</span>
                                    <strong class="text-amber-600 font-bold">{{ $pending }}</strong>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Kartu: CTA -->
                    <div class="p-5 pt-0 mt-auto">
                        <a href="{{ route('companies.show', $company) }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm hover:shadow group/btn">
                            <span>Lihat Detail & Ajukan Magang</span>
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- ZERO-BUG POLISH: Pagination dengan Query String Preservation -->
        <div class="pt-6">
            {{ $companies->withQueryString()->links() }}
        </div>

    @else
        <!-- ZERO EMPTY STATE: Ramah & Call-to-Action -->
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-soft max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tidak Ada Instansi Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Filter pencarian Anda tidak cocok dengan instansi manapun dalam katalog aktif. Coba ubah kata kunci atau reset filter radar.
                </p>
            </div>
            <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Reset Semua Filter</span>
            </a>
        </div>
    @endif

</div>
@endsection
