@extends('layouts.app')

@section('title', 'Panel Koordinator - Monitoring Magang')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Panel Koordinator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Pantau penempatan dan kelola mitra.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Statistik keseluruhan mahasiswa, status kelompok, dan kuota mitra magang.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.export.csv') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                <span>Unduh Rekap CSV</span>
            </a>

            <a href="{{ route('admin.requests.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <span>Tinjau Usulan</span>
                @if($pendingRequestsCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-500 text-white">
                        {{ $pendingRequestsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.companies.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                <span>Kelola Mitra</span>
            </a>
        </div>
    </div>

    <!-- 2. STATS COUNTER CARDS (4 COLS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Mahasiswa -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Mahasiswa</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalStudents }}</span>
                <span class="text-xs text-slate-500 font-semibold">Mahasiswa</span>
            </div>
            <p class="text-[11px] text-slate-400 pt-1 border-t border-slate-100">
                Informatika aktif terdaftar magang
            </p>
        </div>

        <!-- Sudah Diterima -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block">Sudah Diterima</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $placedStudents }}</span>
                <span class="text-xs text-emerald-700 font-bold">{{ $placementPercentage }}%</span>
            </div>
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, $placementPercentage) }}%"></div>
            </div>
        </div>

        <!-- Belum Penempatan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block">Belum Penempatan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $unplacedStudents }}</span>
                <span class="text-xs text-amber-700 font-bold">{{ $totalStudents > 0 ? round(($unplacedStudents / $totalStudents) * 100, 1) : 0 }}%</span>
            </div>
            <p class="text-[11px] text-slate-400 pt-1 border-t border-slate-100">
                Proses pengajuan / pencarian tim
            </p>
        </div>

        <!-- Kelompok Magang -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 block">Total Kelompok</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalGroups }}</span>
                <span class="text-xs text-slate-500 font-semibold">Kelompok</span>
            </div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100 text-[11px]">
                <span class="text-emerald-600 font-semibold">{{ $placedGroups }} diterima</span>
                <span class="text-slate-300">•</span>
                <span class="text-amber-600 font-semibold">{{ $pendingGroups }} menunggu</span>
            </div>
        </div>

    </div>

    <!-- 3. ALERT PENGAJUAN TERTUNDA (> 14 HARI) -->
    @if($hangingApplications->isNotEmpty())
        <div class="bg-rose-50/80 border border-rose-200/90 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-rose-800 text-xs font-bold">
                    <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Perhatian: {{ $hangingApplications->count() }} Pengajuan Tertunda > 14 Hari</span>
                </div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 bg-rose-100/90 px-2 py-0.5 rounded">
                    Perlu Tindak Lanjut
                </span>
            </div>
            <p class="text-xs text-rose-700">
                Kelompok berikut telah mengajukan magang lebih dari 14 hari tanpa kepastian respon dari instansi terkait.
            </p>

            <div class="divide-y divide-rose-200/60 bg-white/70 rounded-xl p-2 border border-rose-200/60 text-xs">
                @foreach($hangingApplications as $app)
                    <div class="py-2.5 px-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-900">{{ $app->group->name }}</span>
                            <span class="text-slate-400 mx-1">•</span>
                            <span class="text-slate-600">{{ $app->company->name }}</span>
                            <span class="text-rose-600 font-semibold text-[11px] ml-2">({{ (int) $app->applied_at?->diffInDays(now()) }} hari tertunda)</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('groups.show', $app->group) }}" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                                Detail Tim
                            </a>
                            @if($app->company->contact_phone)
                                @php
                                    $cp = preg_replace('/[^0-9]/', '', $app->company->contact_phone);
                                    if (str_starts_with($cp, '0')) { $cp = '62' . substr($cp, 1); }
                                @endphp
                                <a href="https://wa.me/{{ $cp }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-semibold hover:bg-emerald-700">
                                    Hubungi PIC
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. PERSEBARAN MAHASISWA PER INSTANSI TABEL -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Persebaran mahasiswa per mitra</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring kuota resmi, mahasiswa diterima, dan tingkat utilisasi mitra.</p>
            </div>
            <span class="text-xs text-slate-400 font-medium">
                {{ $companyDistribution->count() }} mitra aktif
            </span>
        </div>

        <div class="overflow-x-auto pt-1">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pl-2">Mitra / Instansi</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3 text-center">Status Kuota</th>
                        <th class="pb-3 text-center">Kuota Resmi</th>
                        <th class="pb-3 text-center">Diterima</th>
                        <th class="pb-3 text-center">Sisa Slot</th>
                        <th class="pb-3 w-40">Utilisasi</th>
                        <th class="pb-3 text-right pr-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($companyDistribution as $comp)
                        @php
                            $quotaPercentage = $comp->quota_percentage;
                            $status = $comp->quota_status;
                            $level = $status['level'] ?? 'green';
                            $remaining = max(0, $comp->quota - $comp->accepted_members_count);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 pl-2">
                                <a href="{{ route('companies.show', $comp) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                    {{ $comp->name }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $comp->city ?? 'Yogyakarta' }} • {{ $comp->field ?? 'Sistem informasi' }}
                                </p>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ ucfirst($comp->type) }}
                                </span>
                            </td>
                            <td class="py-3.5 text-center">
                                @if($level === 'green')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Tersedia</span>
                                    </span>
                                @elseif($level === 'yellow')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Terbatas</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Penuh</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-center font-bold text-slate-800">
                                {{ $comp->quota }}
                            </td>
                            <td class="py-3.5 text-center font-bold text-emerald-600">
                                {{ $comp->accepted_members_count }}
                            </td>
                            <td class="py-3.5 text-center font-bold text-slate-600">
                                {{ $remaining }}
                            </td>
                            <td class="py-3.5">
                                <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                    <span>{{ $quotaPercentage }}%</span>
                                    <span>{{ $comp->accepted_members_count }}/{{ $comp->quota }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $level === 'green' ? 'bg-indigo-600' : ($level === 'yellow' ? 'bg-amber-500' : 'bg-rose-500') }}"
                                         style="width: {{ min(100, $quotaPercentage) }}%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 text-right pr-2">
                                <a href="{{ route('admin.companies.edit', $comp) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                                    Edit kuota
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">Belum ada data mitra aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Statistik diperbarui secara otomatis.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
