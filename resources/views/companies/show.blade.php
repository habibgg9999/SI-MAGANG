@extends('layouts.app')

@section('title', 'Detail Mitra - ' . $company->name)

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    @php
        $status = $company->quota_status;
        $level = $status['level'] ?? 'green';
        $pct = $company->quota > 0 ? min(100, round(($company->accepted_members_count / $company->quota) * 100)) : 100;
        $remaining = $company->remaining_quota;
        $accepted = $company->accepted_members_count;
    @endphp

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Eksplorasi Mitra
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Kenali mitra magangmu.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Pelajari bidang, persyaratan, dan ketersediaan sebelum mengajukan magang.
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('companies.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <span>Kembali ke Radar Kuota</span>
            </a>
        </div>
    </div>

    <!-- 2. INSTANSI BANNER CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h2 class="font-extrabold text-lg sm:text-xl text-slate-900 leading-snug">
                    {{ $company->name }}
                </h2>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-1">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $company->city }}</span>
                    </span>
                    <span>•</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[11px]">{{ ucfirst($company->type) }}</span>
                    <span>•</span>
                    <span class="text-slate-400">Mitra magang terverifikasi</span>
                </div>
            </div>
        </div>

        <div>
            @if($level === 'green')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Tersedia</span>
                </span>
            @elseif($level === 'yellow')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Terbatas</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/80">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Penuh</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 3. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Detail & Prasyarat (7-8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Tentang Instansi -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Tentang instansi</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $company->description ?? 'Mendukung layanan pemerintahan digital melalui pengelolaan informasi, infrastruktur jaringan, dan statistik daerah. Mahasiswa terlibat dalam pengembangan layanan publik bersama tim teknis instansi.' }}
                </p>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $company->field ?? 'Sistem informasi' }}
                    </span>
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                        Jaringan & Cloud
                    </span>
                </div>

                <div class="pt-2 text-xs text-slate-500 space-y-1">
                    <p>Pengembangan aplikasi layanan publik, dokumentasi sistem, serta pemeliharaan jaringan.</p>
                    <div class="flex items-center gap-1.5 text-slate-500 pt-1">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>{{ $company->address ?? 'Kawasan perkantoran Pemerintah, Yogyakarta' }} • Luring</span>
                    </div>
                </div>
            </div>

            <!-- Prasyarat Pengajuan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Prasyarat pengajuan</h3>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Mahasiswa aktif program studi Informatika atau bidang terkait.</span>
                    </div>

                    <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Kelompok 3–4 mahasiswa dengan data anggota lengkap.</span>
                    </div>

                    <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Surat pengantar kampus dan proposal magang kelompok.</span>
                    </div>

                    <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-700">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>CV seluruh anggota dan portofolio yang relevan.</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    Berkas PDF • Maksimal 5 MB per dokumen • Ditinjau oleh koordinator.
                </p>
            </div>

        </div>

        <!-- Kanan: Kuota, Periode & CTA (4-5 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Kuota & Periode Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <h3 class="font-bold text-slate-900 text-base">Kuota & periode</h3>

                <div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-indigo-600 tracking-tight">{{ $remaining }}</span>
                        <span class="text-xs text-slate-500 font-medium">kuota tersisa dari {{ $company->quota }}</span>
                    </div>

                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mt-3">
                        <div class="h-full rounded-full {{ $level === 'green' ? 'bg-indigo-600' : ($level === 'yellow' ? 'bg-amber-500' : 'bg-rose-500') }}"
                             style="width: {{ $pct }}%;"></div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2">
                        <span>{{ $accepted }} terisi</span>
                        <span>{{ $remaining }} tersedia</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Periode penempatan</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">Januari – Maret 2027</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px]">Lokasi & pelaksanaan</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $company->city }} • Luring</span>
                    </div>
                </div>

                <!-- Form Submit Ajukan Magang -->
                <div class="pt-2">
                    @if($ledGroups->isNotEmpty())
                        <form action="{{ route('companies.apply', $company) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $ledGroups->first()->id }}">
                            <button type="submit" 
                                    class="w-full py-3 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all text-center block">
                                Ajukan magang
                            </button>
                        </form>
                    @else
                        <button type="button" disabled 
                                class="w-full py-3 rounded-xl bg-[#4338ca] opacity-90 text-white text-xs font-bold shadow-sm transition-all text-center block cursor-default">
                            Ajukan magang
                        </button>
                    @endif
                    <p class="text-[11px] text-slate-400 text-center mt-2">
                        Pengajuan dilakukan oleh ketua kelompok.
                    </p>
                </div>
            </div>

            <!-- Status Alert Box -->
            <div class="bg-indigo-50/70 border border-indigo-100/90 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-indigo-700 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pengajuanmu sedang ditinjau</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tim Cakrawala telah mengajukan pada 12 Nov 2026. Pantau status di detail kelompok; hindari pengajuan ganda.
                </p>
            </div>

        </div>

    </div>

    <!-- 4. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-4 text-xs text-slate-400 font-normal">
        <div>Data contoh • Nama, kuota, dan status bersifat ilustratif.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
