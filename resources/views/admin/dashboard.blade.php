@extends('layouts.app')

@section('title', 'Dashboard Koordinator Magang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-purple-600 text-white shadow-md shadow-purple-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Koordinator Magang Kampus</h1>
                    <p class="text-sm text-slate-500">Monitoring distribusi kuota, penempatan mahasiswa, dan mitigasi pengajuan tertunda.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Tombol Export CSV -->
            <a href="{{ route('admin.export.csv') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Download Rekap CSV</span>
            </a>

            <!-- Kelola Instansi -->
            <a href="{{ route('admin.companies.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 shadow-2xs hover:shadow-xs transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Kelola Instansi</span>
            </a>

            <!-- Review Usulan -->
            <a href="{{ route('admin.requests.index') }}" 
               class="relative inline-flex items-center gap-2 px-4 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-semibold rounded-xl border border-purple-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Usulan Instansi</span>
                @if($pendingRequestsCount > 0)
                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-rose-500 rounded-full">
                        {{ $pendingRequestsCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- STATS COUNTERS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Mahasiswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Mahasiswa</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $totalStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="font-semibold text-slate-700">Role Mahasiswa</span> aktif di SIMAGANG
            </div>
        </div>

        <!-- Sudah Dapat Tempat Magang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Sudah Diterima</p>
                    <p class="text-3xl font-black text-emerald-700 mt-1">{{ $placedStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-emerald-700">{{ $placementPercentage }}% Tercapai</span>
                    <span class="text-slate-400">{{ $placedStudents }}/{{ $totalStudents }} Mhs</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $placementPercentage) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Belum Dapat Magang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Belum Penempatan</p>
                    <p class="text-3xl font-black text-amber-700 mt-1">{{ $unplacedStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="font-semibold text-amber-700">{{ $totalStudents > 0 ? round(($unplacedStudents / $totalStudents) * 100, 1) : 0 }}%</span> mahasiswa masih mencari/proses
            </div>
        </div>

        <!-- Kelompok Magang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-purple-600 uppercase tracking-wider">Total Kelompok</p>
                    <p class="text-3xl font-black text-purple-700 mt-1">{{ $totalGroups }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                    {{ $placedGroups }} Diterima
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold border border-amber-200">
                    {{ $pendingGroups }} Menunggu
                </span>
            </div>
        </div>
    </div>

    <!-- ALERT PENGAJUAN MENGGANTUNG (> 14 HARI) -->
    @if($hangingApplications->isNotEmpty())
        <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-500/20">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-lg font-bold text-rose-900 flex items-center gap-2">
                            Peringatan: {{ $hangingApplications->count() }} Pengajuan Magang Menggantung (> 14 Hari Tanpa Respon)
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold uppercase bg-rose-200 text-rose-900 border border-rose-300">
                            Perlu Tindakan Segera
                        </span>
                    </div>
                    <p class="text-sm text-rose-700 mt-1">
                        Kelompok di bawah ini telah mengajukan pendaftaran magang lebih dari 14 hari kerja namun instansi belum memberikan respon penerimaan/penolakan resmi.
                    </p>

                    <!-- TABEL PENGAJUAN MENGGANTUNG -->
                    <div class="mt-4 bg-white rounded-xl border border-rose-200 overflow-hidden shadow-xs">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-rose-100/60 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4 text-left">Kelompok & Ketua</th>
                                        <th class="py-3 px-4 text-left">Instansi Tujuan</th>
                                        <th class="py-3 px-4 text-left">Jumlah Mhs</th>
                                        <th class="py-3 px-4 text-left">Tgl Pengajuan</th>
                                        <th class="py-3 px-4 text-left">Lama Tertunda</th>
                                        <th class="py-3 px-4 text-right">Aksi Koordinator</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($hangingApplications as $app)
                                        @php
                                            $daysHanging = $app->applied_at ? (int) $app->applied_at->diffInDays(now()) : 0;
                                        @endphp
                                        <tr class="hover:bg-rose-50/50 transition-colors">
                                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('groups.show', $app->group) }}" class="font-bold text-purple-700 hover:underline">
                                                        {{ $app->group->name }}
                                                    </a>
                                                </div>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    Ketua: <span class="font-semibold text-slate-700">{{ $app->group->leader->name ?? '-' }}</span> ({{ $app->group->leader->phone ?? '-' }})
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-semibold text-slate-800">
                                                    <a href="{{ route('companies.show', $app->company) }}" class="hover:text-brand-600 hover:underline">
                                                        {{ $app->company->name }}
                                                    </a>
                                                </div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $app->company->city }} &bull; CP: {{ $app->company->contact_person ?? '-' }} ({{ $app->company->contact_phone ?? '-' }})
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-800">
                                                    {{ $app->group->activeMembers->count() }} Orang
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                                {{ $app->applied_at ? $app->applied_at->format('d M Y') : '-' }}
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                                                    {{ $daysHanging }} Hari Lalu
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('groups.show', $app->group) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                                        Detail Tim
                                                    </a>
                                                    @if($app->company->contact_phone)
                                                        @php
                                                            $cleanPhone = preg_replace('/[^0-9]/', '', $app->company->contact_phone);
                                                            if (str_starts_with($cleanPhone, '0')) {
                                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            }
                                                            $waText = urlencode("Halo Bpk/Ibu PIC {$app->company->name}, saya Koordinator Magang Kampus menanyakan tindak lanjut pengajuan magang kelompok {$app->group->name} yang telah diajukan sejak {$app->applied_at->format('d/m/Y')}. Terima kasih.");
                                                        @endphp
                                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-2xs transition-colors flex items-center gap-1">
                                                            <span>Hubungi PIC</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-emerald-800">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">Semua pengajuan magang berjalan lancar. Tidak ada kelompok yang status pengajuannya menggantung lebih dari 14 hari.</p>
        </div>
    @endif

    <!-- PERSEBARAN MAHASISWA PER INSTANSI (TABEL & VISUALISASI TRAFFIC LIGHT) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Persebaran Mahasiswa per Instansi Mitra</h2>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring kuota resmi, jumlah mahasiswa diterima, dan tingkat utilisasi tempat magang.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                    Total {{ $companyDistribution->count() }} Instansi Aktif
                </span>
                <a href="{{ route('admin.companies.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg shadow-2xs transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Instansi</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-5 text-left">Nama Instansi & Lokasi</th>
                        <th class="py-3 px-5 text-left">Tipe</th>
                        <th class="py-3 px-5 text-center">Status Kuota (Traffic Light)</th>
                        <th class="py-3 px-5 text-center">Kuota Resmi</th>
                        <th class="py-3 px-5 text-center">Mhs Diterima</th>
                        <th class="py-3 px-5 text-center">Sisa Slot</th>
                        <th class="py-3 px-5 text-left w-48">Utilisasi Kuota</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($companyDistribution as $comp)
                        @php
                            $quotaPercentage = $comp->quota_percentage;
                            $badge = $comp->traffic_badge;
                            $remaining = max(0, $comp->quota - $comp->accepted_members_count);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('companies.show', $comp) }}" class="font-bold text-slate-900 hover:text-brand-600">
                                    {{ $comp->name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                    <span>📍 {{ $comp->city ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $comp->field ?? 'Umum' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $comp->type }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['class'] }}">
                                    <span>{{ $badge['indicator'] }}</span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center font-bold text-slate-900">
                                {{ $comp->quota }} Slot
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $comp->accepted_members_count }} Mhs
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $remaining > 0 ? 'bg-slate-100 text-slate-700' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $remaining }} Slot
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center justify-between text-xs font-semibold mb-1 text-slate-600">
                                    <span>{{ $quotaPercentage }}%</span>
                                    <span>{{ $comp->accepted_members_count }}/{{ $comp->quota }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $quotaPercentage >= 90 ? 'bg-rose-500' : ($quotaPercentage >= 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                         style="width: {{ min(100, $quotaPercentage) }}%"></div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.companies.edit', $comp) }}" class="p-1.5 text-slate-500 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors" title="Edit Kuota & Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('companies.show', $comp) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Lihat Radar Kuota">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Belum ada instansi mitra yang aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
