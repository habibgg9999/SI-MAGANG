@extends('layouts.app')

@section('title', $company->name . ' - Detail Radar Instansi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- BREADCRUMBS -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('companies.index') }}" class="hover:text-brand-600 transition-colors">Radar Instansi</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $company->name }}</span>
    </nav>

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

        // Cek apakah ada kelompok yang dipimpin yang sedang punya pengajuan menunggu di instansi lain
        $groupWithPending = $ledGroups->first(fn($g) => $g->hasPendingApplication());
    @endphp

    <!-- HEADER DETAIL INSTANSI & TRAFFIC LIGHT SUMMARY -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $typeClass }}">
                        {{ $company->type }}
                    </span>
                    
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shadow-2xs {{ $status['badge_bg'] }}">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $status['dot_color'] }}"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $status['dot_color'] }}"></span>
                        </span>
                        <span>{{ $status['icon'] }} Radar: {{ $status['label'] }} ({{ $pct }}%)</span>
                    </span>

                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mitra Terverifikasi Koordinator
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $company->name }}
                </h1>

                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-xs sm:text-sm text-slate-600">
                    @if($company->city)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="font-medium text-slate-800">{{ $company->city }}</span>
                        </div>
                    @endif

                    @if($company->field)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium text-slate-800">{{ $company->field }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TOMBOL AJUKAN MAGANG (HEADER CTA) -->
            <div class="shrink-0">
                @if($currentUser && $ledGroups->isNotEmpty())
                    @if($existingApplication)
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sudah Diajukan
                            </div>
                            <p>Kelompok Anda telah mendaftar dengan status <strong class="uppercase underline">{{ $existingApplication->status }}</strong>.</p>
                        </div>
                    @elseif($groupWithPending)
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-1 max-w-xs">
                            <div class="font-bold flex items-center gap-1.5">
                                ⏳ Pengajuan Tertunda
                            </div>
                            <p>Kelompok <strong>{{ $groupWithPending->name }}</strong> masih memiliki pengajuan berstatus 'MENUNGGU' di <strong>{{ $groupWithPending->pendingApplication()->company->name }}</strong>.</p>
                        </div>
                    @else
                        <a href="#form-ajukan-magang" 
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition-all hover:scale-102">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Ajukan Magang Kelompok</span>
                        </a>
                    @endif
                @elseif($currentUser && $currentUser->isMahasiswa())
                    <div class="p-3 rounded-xl bg-slate-100 text-slate-600 text-xs max-w-xs">
                        ℹ️ Anda terdaftar sebagai anggota. Pengajuan magang hanya dapat dikirim oleh <strong>Ketua Kelompok</strong>.
                    </div>
                @elseif(!$currentUser)
                    <div class="p-3 rounded-xl bg-slate-100 text-slate-600 text-xs max-w-xs">
                        🔒 Masuk sebagai <strong>Ketua Kelompok</strong> (gunakan simulator di pojok kanan atas) untuk mengajukan magang.
                    </div>
                @endif
            </div>
        </div>

        <!-- RADAR KUOTA METRICS & PROGRESS BAR -->
        <div class="p-5 rounded-2xl {{ $status['badge_bg'] }} border {{ $status['badge_pill'] }} space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $status['icon'] }}</span>
                        <h2 class="text-base font-bold text-slate-900">
                            Status Radar: {{ $status['label'] }} ({{ $status['sublabel'] }})
                        </h2>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        {{ $status['description'] }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $pct }}%</span>
                    <span class="block text-xs font-medium text-slate-500">Persentase Terisi</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full h-3.5 bg-slate-200/80 rounded-full overflow-hidden p-0.5">
                <div class="h-full rounded-full transition-all duration-700 {{ $status['bar_color'] }}" style="width: {{ $pct }}%"></div>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div class="bg-white/80 rounded-xl p-3 border border-slate-200/60 text-center">
                    <span class="text-xs text-slate-500 font-medium">Total Kuota Instansi</span>
                    <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $company->quota }} <span class="text-xs font-normal">Slot</span></p>
                </div>
                <div class="bg-white/80 rounded-xl p-3 border border-slate-200/60 text-center">
                    <span class="text-xs text-emerald-600 font-medium">Kuota Terisi (Diterima)</span>
                    <p class="text-xl font-bold text-emerald-700 mt-0.5">{{ $accepted }} <span class="text-xs font-normal">Mahasiswa</span></p>
                </div>
                <div class="bg-white/80 rounded-xl p-3 border border-slate-200/60 text-center">
                    <span class="text-xs font-medium {{ $remaining > 0 ? 'text-blue-600' : 'text-rose-600' }}">Sisa Kuota Terbuka</span>
                    <p class="text-xl font-bold {{ $remaining > 0 ? 'text-blue-700' : 'text-rose-700' }} mt-0.5">{{ $remaining }} <span class="text-xs font-normal">Slot</span></p>
                </div>
                <div class="bg-white/80 rounded-xl p-3 border border-slate-200/60 text-center">
                    <span class="text-xs text-amber-600 font-medium">Sedang Menunggu</span>
                    <p class="text-xl font-bold text-amber-700 mt-0.5">{{ $pending }} <span class="text-xs font-normal">Mahasiswa</span></p>
                </div>
            </div>
        </div>

        <!-- INFORMASI KONTAK & ALAMAT -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-700 pt-2">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Alamat Lengkap
                </h3>
                <p class="text-slate-600 leading-relaxed">
                    {{ $company->address ?? 'Alamat belum diatur secara spesifik.' }}
                </p>
                @if($company->city)
                    <p class="text-xs text-slate-500 font-semibold">Kota: {{ $company->city }}</p>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-2">
                <h3 class="font-bold text-slate-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Narahubung / Contact Person
                </h3>
                <p class="text-slate-800 font-medium">
                    {{ $company->contact_person ?? 'Belum ada kontak personal' }}
                </p>
                @if($company->contact_phone)
                    <p class="text-xs text-slate-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Telp/WA: <span class="font-semibold text-slate-800">{{ $company->contact_phone }}</span>
                    </p>
                @endif
            </div>
        </div>

    </div>

    <!-- DAFTAR KELOMPOK YANG MENDAFTAR / DITERIMA -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Daftar Kelompok Mahasiswa Pelamar
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Transparansi data kelompok pendaftar di instansi ini beserta status dan anggotanya.
                </p>
            </div>
            
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">
                    Diterima: {{ $acceptedApplications->count() }} Kelompok
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold">
                    Menunggu: {{ $pendingApplications->count() }} Kelompok
                </span>
            </div>
        </div>

        @if($company->applications->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-500 text-xs">
                Belum ada kelompok yang mengajukan magang ke instansi ini. Jadilah kelompok pertama yang mendaftar!
            </div>
        @else
            <!-- SEKSI 1: KELOMPOK DITERIMA (KUOTA TERHITUNG) -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Kelompok yang Telah Diterima (Slot Terhitung)
                </h3>

                @if($acceptedApplications->isEmpty())
                    <p class="text-xs text-slate-400 italic bg-white p-4 rounded-xl border border-slate-100">
                        Belum ada kelompok yang berstatus diterima pada instansi ini.
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($acceptedApplications as $app)
                            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-2xs space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-base text-slate-900">{{ $app->group->name }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                                DITERIMA
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Ketua: <strong class="text-slate-800">{{ $app->group->leader->name }}</strong> ({{ $app->group->leader->nim ?? 'NIM -' }})
                                        </p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                        {{ $app->group->activeMembers->count() }} Anggota
                                    </span>
                                </div>

                                <div class="pt-2 border-t border-slate-100">
                                    <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Anggota Aktif:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($app->group->activeMembers as $gm)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs bg-slate-50 text-slate-700 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                {{ $gm->user->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-2 text-xs text-slate-500 flex items-center justify-between">
                                    <span>Tgl Diterima: {{ $app->responded_at ? $app->responded_at->translatedFormat('d M Y') : '-' }}</span>
                                    @if($app->notes)
                                        <span class="italic text-slate-600 line-clamp-1" title="{{ $app->notes }}">"{{ $app->notes }}"</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- SEKSI 2: KELOMPOK MENUNGGU KONFIRMASI -->
            <div class="space-y-3 pt-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Kelompok dalam Proses Seleksi (Menunggu Konfirmasi)
                </h3>

                @if($pendingApplications->isEmpty())
                    <p class="text-xs text-slate-400 italic bg-white p-4 rounded-xl border border-slate-100">
                        Tidak ada kelompok yang sedang menunggu konfirmasi saat ini.
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($pendingApplications as $app)
                            <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-2xs space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-base text-slate-900">{{ $app->group->name }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                                MENUNGGU
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Ketua: <strong class="text-slate-800">{{ $app->group->leader->name }}</strong> ({{ $app->group->leader->nim ?? 'NIM -' }})
                                        </p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                        {{ $app->group->activeMembers->count() }} Anggota
                                    </span>
                                </div>

                                <div class="pt-2 border-t border-slate-100">
                                    <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Anggota:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($app->group->activeMembers as $gm)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs bg-slate-50 text-slate-700 border border-slate-200">
                                                {{ $gm->user->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-2 text-xs text-slate-500 flex items-center justify-between">
                                    <span>Tgl Pengajuan: {{ $app->applied_at ? $app->applied_at->translatedFormat('d M Y') : '-' }}</span>
                                    @if($app->notes)
                                        <span class="italic text-slate-600 line-clamp-1" title="{{ $app->notes }}">"{{ $app->notes }}"</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- FORM PENGAJUAN MAGANG (KHUSUS KETUA KELOMPOK) -->
    <div id="form-ajukan-magang" class="bg-gradient-to-br from-slate-900 to-brand-950 rounded-3xl p-6 sm:p-10 text-white shadow-xl space-y-6">
        <div class="max-w-2xl space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-300 border border-brand-400/30">
                Portal Pengajuan Magang
            </span>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                Ajukan Pendaftaran Magang ke {{ $company->name }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300">
                Pastikan kelompok Anda telah menyelesaikan berkas dan menyepakati bidang kerja yang dituju.
            </p>
        </div>

        @if($currentUser && $ledGroups->isNotEmpty())
            @if($existingApplication)
                <div class="p-4 rounded-2xl bg-white/10 border border-white/20 text-slate-200 text-xs sm:text-sm">
                    ⚠️ Kelompok Anda (<strong>{{ $existingApplication->group->name }}</strong>) sudah tercatat mendaftar ke instansi ini dengan status <span class="uppercase font-bold text-amber-300">{{ $existingApplication->status }}</span> pada tanggal {{ $existingApplication->applied_at ? $existingApplication->applied_at->format('d M Y') : '-' }}.
                </div>
            @elseif($groupWithPending)
                <div class="p-4 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-200 text-xs sm:text-sm space-y-2">
                    <p class="font-bold flex items-center gap-2 text-amber-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Validasi Pengajuan Magang:
                    </p>
                    <p>
                        Kelompok Anda (<strong>{{ $groupWithPending->name }}</strong>) masih memiliki pengajuan magang yang sedang berstatus <span class="font-extrabold uppercase text-amber-400">MENUNGGU</span> di instansi <strong>{{ $groupWithPending->pendingApplication()->company->name }}</strong>.
                    </p>
                    <p class="text-[11px] text-amber-200/80">
                        Sesuai aturan SIMAGANG, kelompok <em>hanya bisa mengajukan ke 1 instansi</em> sampai ada keputusan atau permohonan dibatalkan.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('groups.show', $groupWithPending) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors">
                            Buka Dashboard Kelompok Saya &rarr;
                        </a>
                    </div>
                </div>
            @else
                <!-- FORM AJUKAN MAGANG DENGAN PENGECEKAN STATUS MERAH -->
                <form id="applyForm" action="{{ route('applications.store') }}" method="POST" class="space-y-4 max-w-xl">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    <input type="hidden" name="confirmed_red_warning" id="confirmedRedWarning" value="0">
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-1.5">Pilih Kelompok yang Anda Pimpin</label>
                        <select name="group_id" required class="w-full text-sm bg-slate-800 text-white rounded-xl px-4 py-2.5 border border-slate-700 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20 focus:outline-hidden">
                            @foreach($ledGroups as $grp)
                                <option value="{{ $grp->id }}">
                                    {{ $grp->name }} ({{ $grp->activeMembers->count() }} Anggota Aktif)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-1.5">Catatan / Posisi / Keterangan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Contoh: Pengajuan posisi Backend Engineer & Mobile Dev untuk 3 mahasiswa..." class="w-full text-sm bg-slate-800 text-white rounded-xl px-4 py-2.5 border border-slate-700 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20 focus:outline-hidden placeholder:text-slate-500"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="button" 
                                onclick="handleApplySubmit()" 
                                class="w-full sm:w-auto px-6 py-3 bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-brand-500/30 transition-all hover:scale-102 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Kirim Berkas Pengajuan Magang</span>
                        </button>
                    </div>
                </form>
            @endif
        @else
            <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                <p class="text-xs sm:text-sm text-slate-300">
                    Fitur pendaftaran magang ini hanya terbuka bagi akun dengan peran <strong>Ketua Kelompok</strong>.
                </p>
                <div class="text-xs text-slate-400">
                    💡 <em>Tip Evaluasi: Gunakan tombol simulator akun di navbar atas, lalu pilih salah satu Ketua Kelompok seperti <strong>Muhammad Habib Hanafi [Ketua: DevForce Alpha]</strong> atau <strong>Budi Pratama [Ketua: CyberShield Squad]</strong> untuk mencoba fitur pengajuan secara langsung.</em>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- MODAL PERINGATAN KUOTA MERAH (TRAFFIC LIGHT RED WARNING) -->
<div id="redQuotaModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-rose-200 space-y-5 text-slate-800">
        <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div class="text-center space-y-2">
            <h3 class="text-lg font-extrabold text-slate-900">
                Peringatan: Kuota Instansi Berstatus 🔴 MERAH!
            </h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Instansi <strong>{{ $company->name }}</strong> saat ini memiliki keterisian kuota sebesar <strong class="text-rose-600">{{ $pct }}% (Kritis/Penuh)</strong>. Sisa kuota slot kosong hanya tersisa <strong class="text-rose-600">{{ $remaining }} mahasiswa</strong>.
            </p>
            <div class="p-3 bg-rose-50 rounded-xl text-rose-800 text-[11px] text-left">
                ⚠️ Kemungkinan penerimaan kelompok Anda sangat kecil atau memerlukan proses seleksi yang sangat ketat. Anda disarankan memprioritaskan instansi berindikator 🟢 Hijau (&lt; 50%).
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="button" 
                    onclick="closeRedModal()" 
                    class="flex-1 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal & Cari Lain
            </button>
            <button type="button" 
                    onclick="confirmAndSubmitRed()" 
                    class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition-colors">
                Tetap Ajukan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const isCompanyRed = {{ $status['level'] === 'red' ? 'true' : 'false' }};

    function handleApplySubmit() {
        if (isCompanyRed) {
            document.getElementById('redQuotaModal').classList.remove('hidden');
        } else {
            document.getElementById('applyForm').submit();
        }
    }

    function closeRedModal() {
        document.getElementById('redQuotaModal').classList.add('hidden');
    }

    function confirmAndSubmitRed() {
        document.getElementById('confirmedRedWarning').value = '1';
        document.getElementById('applyForm').submit();
    }
</script>
@endpush
@endsection
