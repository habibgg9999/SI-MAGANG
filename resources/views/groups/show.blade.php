@extends('layouts.app')

@section('title', 'Dashboard Kelompok: ' . $group->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- BREADCRUMBS -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('groups.index') }}" class="hover:text-indigo-600 transition-colors">Kelompok Magang</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $group->name }}</span>
    </nav>

    @php
        $activeMembersCount = $group->activeMembers->count();
        $isFull = $group->isFull();
        $isOpen = $group->is_open && !$isFull;
    @endphp

    <!-- HEADER KELOMPOK & RINGKASAN KUOTA ANGGOTA -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $isLeader ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                        {{ $isLeader ? '👑 Anda adalah Ketua' : ($isMember ? '👤 Anda adalah Anggota' : 'Kelompok Mahasiswa') }}
                    </span>

                    @if($isOpen)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Penerimaan Anggota: TERBUKA
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            Penerimaan Anggota: DITUTUP
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ $group->name }}
                </h1>

                <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                    {{ $group->description ?? 'Kelompok magang mahasiswa. Belum ada deskripsi khusus.' }}
                </p>

                <div class="flex items-center gap-2 text-xs text-slate-500 pt-1">
                    <span>Ketua: <strong class="text-slate-800">{{ $group->leader->name }}</strong> (NIM: {{ $group->leader->nim ?? '-' }})</span>
                    <span>&bull;</span>
                    <span>Prodi: <strong class="text-slate-800">{{ $group->leader->prodi ?? '-' }}</strong></span>
                </div>
            </div>

            <!-- Tombol Aksi Ketua / Gabung Mandiri -->
            <div class="shrink-0 flex flex-wrap items-center gap-2">
                @if($isLeader)
                    <form action="{{ route('groups.toggle-open', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold border transition-colors flex items-center gap-1.5 {{ $group->is_open ? 'border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>{{ $group->is_open ? 'Tutup Pendaftaran Anggota' : 'Buka Pendaftaran Anggota' }}</span>
                        </button>
                    </form>
                @elseif($canJoin)
                    <form action="{{ route('groups.join', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-500/20 transition-all hover:scale-102 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            <span>Gabung ke Kelompok Ini</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- STATS KUOTA ANGGOTA -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 text-center">
                <span class="text-xs text-slate-500 font-medium">Kapasitas Maksimal</span>
                <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $group->max_members }} <span class="text-xs font-normal">Mahasiswa</span></p>
            </div>
            <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200/60 text-center">
                <span class="text-xs text-indigo-700 font-medium">Anggota Aktif Terdaftar</span>
                <p class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $activeMembersCount }} <span class="text-xs font-normal">Mahasiswa</span></p>
            </div>
            <div class="p-4 rounded-2xl {{ $group->max_members - $activeMembersCount > 0 ? 'bg-emerald-50/60 border-emerald-200/60 text-emerald-800' : 'bg-rose-50/60 border-rose-200/60 text-rose-800' }} border text-center">
                <span class="text-xs font-medium">Sisa Slot Anggota</span>
                <p class="text-2xl font-extrabold mt-1">{{ max(0, $group->max_members - $activeMembersCount) }} <span class="text-xs font-normal">Slot</span></p>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: MANAJEMEN ANGGOTA KELOMPOK -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Daftar Anggota Kelompok ({{ $activeMembersCount }}/{{ $group->max_members }})
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Anggota aktif yang akan tercatat secara resmi dalam berkas pengajuan magang.
                </p>
            </div>

            <!-- FORM TAMBAH ANGGOTA (KHUSUS KETUA) -->
            @if($isLeader && !$isFull && $availableStudents->isNotEmpty())
                <form action="{{ route('groups.members.add', $group) }}" method="POST" class="flex items-center gap-2 w-full sm:w-auto">
                    @csrf
                    <select name="user_id" required class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Mahasiswa Tersedia --</option>
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name }} ({{ $student->nim ?? 'NIM -' }} &bull; {{ $student->prodi ?? 'TI' }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0">
                        + Tambah Anggota
                    </button>
                </form>
            @endif
        </div>

        <!-- TABEL ANGGOTA -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs sm:text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Mahasiswa</th>
                        <th class="px-5 py-3.5">NIM</th>
                        <th class="px-5 py-3.5">Program Studi</th>
                        <th class="px-5 py-3.5">Peran</th>
                        <th class="px-5 py-3.5">Tgl Bergabung</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($group->activeMembers as $gm)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $gm->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $gm->user->email }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs">
                                {{ $gm->user->nim ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $gm->user->prodi ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($gm->user_id === $group->leader_id)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                                        👑 Ketua Kelompok
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        👤 Anggota
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $gm->joined_at ? $gm->joined_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($gm->user_id !== $group->leader_id)
                                    @if($isLeader || ($currentUser && $currentUser->id === $gm->user_id))
                                        <form action="{{ route('groups.members.remove', [$group, $gm->user]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan anggota ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition-colors">
                                                {{ $currentUser && $currentUser->id === $gm->user_id ? 'Keluar Kelompok' : 'Keluarkan' }}
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Ketua Utama</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- SEKSI 2: STATUS & PENGAJUAN MAGANG KELOMPOK -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Status Pengajuan Magang Kelompok
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelompok <strong>HANYA BISA mengajukan 1 instansi</strong> jika belum ada pengajuan berstatus 'menunggu'.
                </p>
            </div>

            <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs rounded-xl border border-brand-200 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Eksplorasi Radar Kuota Instansi</span>
            </a>
        </div>

        @if($group->applications->isEmpty())
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-3">
                <p class="text-sm font-semibold text-slate-700">Kelompok ini belum pernah mengajukan pendaftaran magang.</p>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Kunjungi halaman Radar Kuota Instansi untuk mencari instansi mitra yang kuotanya masih aman (🟢 Hijau), lalu ajukan magang secara resmi.
                </p>
                @if($isLeader)
                    <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                        Pilih Instansi & Ajukan Magang
                    </a>
                @endif
            </div>
        @else
            <!-- DAFTAR PENGAJUAN MAGANG -->
            <div class="space-y-4">
                @foreach($group->applications as $app)
                    @php
                        $badgeStyle = match($app->status) {
                            'diterima' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'menunggu' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
                            'dibatalkan' => 'bg-slate-100 text-slate-700 border-slate-300',
                            default => 'bg-slate-100 text-slate-800 border-slate-200',
                        };
                    @endphp

                    <div class="p-6 rounded-2xl border border-slate-200/90 bg-white shadow-2xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h3 class="text-lg font-bold text-slate-900">
                                        <a href="{{ route('companies.show', $app->company) }}" class="hover:text-brand-600 transition-colors">
                                            🏢 {{ $app->company->name }}
                                        </a>
                                    </h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase border {{ $badgeStyle }}">
                                        {{ $app->status }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-x-3 gap-y-1">
                                    <span>Tipe: <strong class="text-slate-700">{{ strtoupper($app->company->type) }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Kota: <strong class="text-slate-700">{{ $app->company->city ?? '-' }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Tgl Pengajuan: <strong class="text-slate-700">{{ $app->applied_at ? $app->applied_at->translatedFormat('d M Y') : '-' }}</strong></span>
                                </div>
                            </div>

                            <!-- TOMBOL UPDATE STATUS (UNTUK KETUA KELOMPOK / KOORDINATOR) -->
                            @if(($isLeader || ($currentUser && $currentUser->isKoordinator())) && $app->status === 'menunggu')
                                <div class="shrink-0">
                                    <button type="button" 
                                            onclick="openUpdateStatusModal({{ $app->id }}, '{{ addslashes($app->company->name) }}')"
                                            class="px-4 py-2 bg-slate-900 hover:bg-brand-600 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Update Status Pengajuan</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Catatan Pengajuan -->
                        @if($app->notes)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600">
                                <span class="font-bold text-slate-700">Catatan Pengajuan:</span> {{ $app->notes }}
                            </div>
                        @endif

                        <!-- AUDIT TRAIL LOG HISTORI STATUS -->
                        @if($app->statusLogs->isNotEmpty())
                            <div class="pt-3 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-700 block mb-2 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Riwayat Audit Perubahan Status (Application Status Logs):
                                </span>
                                <div class="space-y-2">
                                    @foreach($app->statusLogs as $log)
                                        <div class="text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-slate-600">
                                            <div>
                                                <span class="font-bold text-slate-800">
                                                    {{ $log->status_before ? strtoupper($log->status_before) : 'AWAL' }} &rarr; <span class="uppercase text-brand-600 font-extrabold">{{ $log->status_after }}</span>
                                                </span>
                                                <span class="text-slate-500 mx-1">&bull;</span>
                                                <span class="text-slate-700">Oleh: <strong>{{ $log->changedBy->name ?? 'Sistem' }}</strong> ({{ $log->changedBy->role ?? 'User' }})</span>
                                                @if($log->reason)
                                                    <span class="block sm:inline text-slate-500 sm:before:content-['—_'] italic">
                                                        "{{ $log->reason }}"
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-slate-400 shrink-0">
                                                {{ $log->changed_at ? $log->changed_at->translatedFormat('d M Y H:i') : $log->created_at->translatedFormat('d M Y H:i') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- MODAL POP-UP: UPDATE STATUS PENGAJUAN MAGANG -->
<div id="updateStatusModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Perbarui Status Pengajuan Magang</h3>
                <p id="modalCompanyName" class="text-xs text-slate-500 mt-0.5">Instansi</p>
            </div>
            <button type="button" onclick="closeUpdateStatusModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="updateStatusForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Pilih Status Baru <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 transition-colors">
                        <input type="radio" name="status" value="diterima" required class="sr-only">
                        <span class="block text-xs font-bold text-emerald-700">DITERIMA</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Surat resmi OK</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-rose-500 has-checked:bg-rose-50 transition-colors">
                        <input type="radio" name="status" value="ditolak" class="sr-only">
                        <span class="block text-xs font-bold text-rose-700">DITOLAK</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Kuota penuh/dll</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-slate-500 has-checked:bg-slate-100 transition-colors">
                        <input type="radio" name="status" value="dibatalkan" class="sr-only">
                        <span class="block text-xs font-bold text-slate-700">BATAL</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Batal mandiri</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Alasan / Keterangan Perubahan Status <span class="text-rose-500">*</span>
                </label>
                <textarea name="reason" 
                          rows="3" 
                          required 
                          placeholder="Contoh: Surat balasan penerimaan magang resmi No. 12/HR/2026 telah diterima dan disahkan..." 
                          class="w-full text-xs bg-slate-50 focus:bg-white text-slate-800 rounded-xl p-3 border border-slate-200 focus:border-indigo-500 focus:outline-hidden"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">
                    *Alasan ini wajib diisi dan akan dicatat secara permanen di tabel <code>application_status_logs</code>.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeUpdateStatusModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                    Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openUpdateStatusModal(appId, companyName) {
        const form = document.getElementById('updateStatusForm');
        form.action = `/applications/${appId}/status`;
        document.getElementById('modalCompanyName').textContent = 'Instansi: ' + companyName;
        document.getElementById('updateStatusModal').classList.remove('hidden');
    }

    function closeUpdateStatusModal() {
        document.getElementById('updateStatusModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
