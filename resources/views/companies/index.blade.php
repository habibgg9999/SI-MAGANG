@extends('layouts.app')

@section('title', 'Radar Kuota - Eksplorasi Mitra')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Eksplorasi Mitra
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Temukan tempat bertumbuh.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Cek kuota mitra dan pilih peluang yang tepat untuk kelompokmu.
            </p>
        </div>

        <div class="shrink-0">
            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span>Tersimpan</span>
            </button>
        </div>
    </div>

    <!-- 2. SEARCH & FILTER CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        <form method="GET" action="{{ route('companies.index') }}" class="space-y-4">
            
            <div class="flex flex-col lg:flex-row items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama instansi, perusahaan, atau bidang magang..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <!-- Lokasi Select -->
                <div class="w-full lg:w-48 shrink-0">
                    <select name="city" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua lokasi</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bidang Select -->
                <div class="w-full lg:w-48 shrink-0">
                    <select name="field" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua bidang</option>
                        @foreach($fields as $f)
                            <option value="{{ $f }}" {{ request('field') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit Filter -->
                <button type="submit" class="w-full lg:w-auto px-4 py-2.5 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Filter Status Pills & Info -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ !request('status') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                        Semua mitra {{ $totalCompanies ?? 0 }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'green']) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ request('status') === 'green' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                        Tersedia {{ $greenCount ?? 0 }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'yellow']) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ request('status') === 'yellow' ? 'bg-amber-50 text-amber-700 border border-amber-300' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                        Terbatas {{ $yellowCount ?? 0 }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'red']) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ request('status') === 'red' ? 'bg-rose-50 text-rose-700 border border-rose-300' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                        Penuh {{ $redCount ?? 0 }}
                    </a>
                </div>

                <span class="text-xs text-slate-400 font-normal">
                    Kuota diperbarui oleh koordinator
                </span>
            </div>

        </form>
    </div>

    <!-- 3. COMPANY CARDS CATALOG -->
    <div class="space-y-4">
        <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700">
                {{ $companies->total() }} mitra magang • <span class="font-normal text-slate-400">Periode Jan – Mar 2027</span>
            </span>

            <form method="GET" action="{{ route('companies.index') }}" class="flex items-center gap-1.5">
                <span class="text-slate-400">Urutkan:</span>
                <select name="sort" onchange="this.form.submit()" class="bg-transparent font-semibold text-slate-700 outline-none cursor-pointer">
                    <option value="quota_asc" {{ request('sort') === 'quota_asc' ? 'selected' : '' }}>Paling relevan</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="quota_count_desc" {{ request('sort') === 'quota_count_desc' ? 'selected' : '' }}>Kuota terbanyak</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($companies as $company)
                @php
                    $status = $company->quota_status;
                    $level = $status['level'] ?? 'green';
                    $percentage = $company->quota > 0 ? min(100, round(($company->accepted_members_count / $company->quota) * 100)) : 100;
                    $firstChar = strtoupper(substr($company->name, 0, 1));
                    if (str_starts_with($company->name, 'PT ')) {
                        $firstChar = strtoupper(substr(substr($company->name, 3), 0, 1));
                    }
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group">
                    <div class="space-y-3.5">
                        <!-- Top Icon & Status Pill -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $company->type === 'pemerintah' ? 'bg-blue-50 text-blue-600' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center font-extrabold text-sm shrink-0">
                                @if($company->type === 'pemerintah')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @else
                                    {{ $firstChar }}
                                @endif
                            </div>

                            @if($level === 'green')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Tersedia</span>
                                </span>
                            @elseif($level === 'yellow')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Terbatas</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span>Penuh</span>
                                </span>
                            @endif
                        </div>

                        <!-- Company Info -->
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                {{ $company->name }}
                            </h3>
                            <div class="flex items-center gap-1 text-[11px] text-slate-500 mt-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $company->city }} • {{ ucfirst($company->type) }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 line-clamp-1">
                                {{ $company->field }}
                            </p>
                        </div>

                        <!-- Quota Progress Bar -->
                        <div class="space-y-1.5 pt-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">{{ $company->remaining_quota }} kuota tersisa</span>
                                <span class="text-slate-400 text-[11px]">dari {{ $company->quota }} kuota</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $level === 'green' ? 'bg-indigo-600' : ($level === 'yellow' ? 'bg-amber-500' : 'bg-rose-500') }}"
                                     style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100 text-xs">
                        <span class="text-[11px] text-slate-400 font-medium">Jan – Mar 2027</span>
                        <a href="{{ route('companies.show', $company) }}" 
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 hover:border-slate-300 transition-colors">
                            <span>Lihat detail</span>
                            <span class="text-slate-400">↗</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500 text-xs">
                    Tidak ada mitra yang sesuai dengan pencarian atau filter Anda.
                </div>
            @endforelse
        </div>

        <!-- 4. PAGINATION & FOOTER -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200/80 text-xs text-slate-400">
            <div>
                Menampilkan {{ $companies->firstItem() ?? 0 }}–{{ $companies->lastItem() ?? 0 }} dari {{ $companies->total() }} mitra
                <p class="text-[11px] text-slate-400 mt-0.5">Data contoh • Ketersediaan kuota bersifat ilustratif.</p>
            </div>
            <div>
                {{ $companies->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
