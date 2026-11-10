@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER WELCOME SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Dashboard Mahasiswa
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Selamat pagi, {{ Str::before($user?->name ?? 'Aditya', ' ') }} 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Satu langkah lebih dekat ke pengalaman profesional pertamamu.
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('companies.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" />
                </svg>
                <span>Jelajahi tempat magang</span>
            </a>
        </div>
    </div>

    <!-- 2. PERJALANAN MAGANGMU (STEPPER TIMELINE PIPELINE) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-2">
            <div class="flex items-center gap-3">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Perjalanan magangmu</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                    Tahap {{ $currentStep }} dari 4
                </span>
            </div>
            <span class="text-xs text-slate-400 font-medium">
                Periode Januari - Maret 2027
            </span>
        </div>

        <!-- Horizontal Stepper Bar -->
        <div class="relative py-2">
            <div class="grid grid-cols-4 gap-2 relative">
                
                <!-- Connector Pipeline Track Behind Steps -->
                <div class="absolute top-4 left-[12%] right-[12%] h-[2px] bg-slate-200 -z-0">
                    <div class="h-full bg-emerald-500 transition-all duration-500" 
                         style="width: {{ $currentStep === 1 ? '0%' : ($currentStep === 2 ? '33%' : ($currentStep === 3 ? '66%' : '100%')) }};"></div>
                </div>

                <!-- Step 1: Bentuk Kelompok -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-emerald-50 text-emerald-600 border border-emerald-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-900 mt-2">Bentuk kelompok</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Selesai • 10 Nov 2026</p>
                </div>

                <!-- Step 2: Ajukan Magang -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs bg-indigo-600 text-white shadow-sm shadow-indigo-500/30">
                        2
                    </div>
                    <p class="text-xs font-bold text-slate-900 mt-2">Ajukan magang</p>
                    <p class="text-[11px] text-indigo-600 font-medium mt-0.5">Menunggu verifikasi</p>
                </div>

                <!-- Step 3: Pelaksanaan -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs bg-white text-slate-400 border border-slate-200">
                        3
                    </div>
                    <p class="text-xs font-medium text-slate-600 mt-2">Pelaksanaan</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Setelah diterima mitra</p>
                </div>

                <!-- Step 4: Laporan & Penilaian -->
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs bg-white text-slate-400 border border-slate-200">
                        4
                    </div>
                    <p class="text-xs font-medium text-slate-600 mt-2">Laporan & penilaian</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Belum dimulai</p>
                </div>

            </div>
        </div>
    </div>

    <!-- 3. MIDDLE DUAL CARDS (PENGAJUAN TERBARU + KELOMPOK SAYA) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Pengajuan Terbaru (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengajuan terbaru</h3>
                    <a href="{{ route('companies.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 group">
                        <span>Lihat detail</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Instansi Info -->
                <div class="flex items-start gap-3.5 pt-1">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 leading-snug">
                            {{ $latestApp?->company?->name ?? 'Dinas Komunikasi Informatika dan Statistik Provinsi' }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $latestApp?->company?->field ?? 'Pengembangan aplikasi' }} • {{ $latestApp?->company?->city ?? 'Daerah Istimewa Yogyakarta' }}
                        </p>
                    </div>
                </div>

                <!-- Status Metadata Row -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-500 font-medium">
                        Diajukan {{ $latestApp ? $latestApp->applied_at?->translatedFormat('d M Y') : '12 Nov 2026' }} • {{ $myGroup ? $myGroup->activeMembers->count() : 4 }} mahasiswa
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Menunggu verifikasi</span>
                    </span>
                </div>
            </div>

            <!-- Alert Notice Koordinator -->
            <div class="mt-4 bg-slate-50/90 border border-slate-100 rounded-xl p-3.5 flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs">
                    <p class="font-bold text-slate-800">Sedang ditinjau koordinator</p>
                    <p class="text-slate-500 mt-0.5">Dokumen lengkap. Pantau pembaruan status melalui halaman ini.</p>
                </div>
            </div>
        </div>

        <!-- Kanan: Kelompok Saya (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Kelompok saya</h3>
                    <a href="{{ route('groups.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 group">
                        <span>Kelola</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Group Name & Tag -->
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-extrabold text-base sm:text-lg text-slate-900">
                            {{ $myGroup?->name ?? 'Tim Cakrawala' }}
                        </h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            Lengkap
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $myGroup ? ($myGroup->description ?? 'IF-07 • Informatika') : 'IF-07 • Informatika' }}
                    </p>
                </div>

                <!-- Member Avatars Row -->
                <div class="flex items-center gap-2 pt-1">
                    <div class="flex -space-x-1.5 overflow-hidden">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-50 border-2 border-white text-[10px] font-bold text-indigo-700">AN</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-50 border-2 border-white text-[10px] font-bold text-blue-700">NA</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-sky-50 border-2 border-white text-[10px] font-bold text-sky-700">FR</span>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-purple-50 border-2 border-white text-[10px] font-bold text-purple-700">DP</span>
                    </div>
                    <span class="text-xs font-medium text-slate-600 ml-1">
                        4/4 anggota
                    </span>
                </div>

                <!-- Forum Notice -->
                <div class="flex items-center gap-2 pt-1 text-xs text-slate-600">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>3 pesan baru di Forum Tim</span>
                </div>
            </div>

            <!-- Buka Forum Tim Button -->
            <a href="{{ route('forum.index') }}" 
               class="mt-4 w-full py-2.5 rounded-xl border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-bold text-center transition-colors block">
                Buka Forum Tim
            </a>
        </div>

    </div>

    <!-- 4. PILIHAN MITRA UNTUKMU (3 COMPANY CARDS GRID) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pilihan mitra untukmu</h3>
                <p class="text-xs text-slate-500 mt-0.5">Sesuai bidang Informatika dan periode magang kelompokmu.</p>
            </div>
            <a href="{{ route('companies.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 group">
                <span>Lihat Radar Kuota</span>
                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </a>
        </div>

        <!-- 3 Mitra Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Card 1: PT Gamatechno Indonesia -->
            <a href="{{ route('companies.index') }}" class="p-4 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold text-sm flex items-center justify-center shrink-0">
                            G
                        </div>
                        <span class="text-slate-400 group-hover:text-indigo-600 transition-colors text-sm font-semibold">↗</span>
                    </div>
                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 mt-3 group-hover:text-indigo-600 transition-colors">
                        PT Gamatechno Indonesia
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Teknologi • Sleman</p>
                </div>
                <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>8 kuota tersedia</span>
                </div>
            </a>

            <!-- Card 2: Bappeda -->
            <a href="{{ route('companies.index') }}" class="p-4 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-slate-400 group-hover:text-indigo-600 transition-colors text-sm font-semibold">↗</span>
                    </div>
                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 mt-3 group-hover:text-indigo-600 transition-colors">
                        Bappeda
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pemerintahan • Yogyakarta</p>
                </div>
                <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>4 kuota tersedia</span>
                </div>
            </a>

            <!-- Card 3: PT Qiscus Tekno Indonesia -->
            <a href="{{ route('companies.index') }}" class="p-4 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold text-sm flex items-center justify-center shrink-0">
                            Q
                        </div>
                        <span class="text-slate-400 group-hover:text-indigo-600 transition-colors text-sm font-semibold">↗</span>
                    </div>
                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 mt-3 group-hover:text-indigo-600 transition-colors">
                        PT Qiscus Tekno Indonesia
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Teknologi • Yogyakarta</p>
                </div>
                <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>6 kuota tersedia</span>
                </div>
            </a>

        </div>
    </div>

    <!-- 5. FOOTER KETERANGAN & WAKTU PEMBARUAN -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>
            Data contoh • Seluruh nama, kuota, dan status bersifat ilustratif.
        </div>
        <div>
            Terakhir diperbarui: 14 Nov 2026, 09.00 WIB
        </div>
    </div>

</div>
@endsection
