@extends('layouts.app')

@section('title', 'Detail Kelompok - ' . $group->name)

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    @php
        $activeMembersCount = $group->activeMembers->count();
        $isFull = $group->isFull();
        $isOpen = $group->is_open && !$isFull;
        $latestApp = $group->latestApplication;
    @endphp

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Detail Kelompok
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Bersama menuju satu tujuan.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Kelola anggota, pantau status pengajuan mitra, dan akses ruang diskusi kelompok.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('forum.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Buka Forum Tim</span>
            </a>

            @if($isLeader && !$isFull)
                <button type="button" 
                        onclick="document.getElementById('inviteMemberSection')?.scrollIntoView({ behavior: 'smooth' })"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                    <span>+ Undang Anggota</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 2. KELOMPOK HERO CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-700 font-extrabold text-xl flex items-center justify-center shrink-0">
                {{ strtoupper(substr($group->name, 0, 1) . substr(strstr($group->name, ' ') ?: ' ', 1, 1)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        {{ $group->name }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $isLeader ? 'Ketua Kelompok' : ($isMember ? 'Anggota Tim' : 'Kelompok Mahasiswa') }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-1">
                    <span>{{ $group->description ?? 'IF-07 • Informatika' }}</span>
                    <span>•</span>
                    <span>Ketua: <strong class="text-slate-800">{{ $group->leader->name }}</strong></span>
                    <span>•</span>
                    <span>Kapasitas: <strong class="text-slate-800">{{ $activeMembersCount }}/{{ $group->max_members }} Mahasiswa</strong></span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
            @if($latestApp)
                <div class="text-left lg:text-right">
                    <span class="text-[11px] text-slate-400 block font-medium">Status Pengajuan Mitra</span>
                    @if($latestApp->status === 'diterima')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Diterima Mitra</span>
                        </span>
                    @elseif($latestApp->status === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span>Pengajuan Ditolak</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span>Menunggu verifikasi</span>
                        </span>
                    @endif
                    <span class="text-[11px] text-slate-400 block mt-0.5">
                        Diajukan {{ $latestApp->applied_at ? $latestApp->applied_at->translatedFormat('d M Y') : '12 Nov 2026' }}
                    </span>
                </div>
            @else
                <div class="text-left lg:text-right">
                    <span class="text-[11px] text-slate-400 block font-medium">Status Pengajuan Mitra</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 mt-1">
                        Belum Mengajukan Mitra
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- 3. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KIRI: DAFTAR ANGGOTA & PENGAJUAN (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card 1: Anggota Kelompok -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Anggota kelompok</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $activeMembersCount }} dari {{ $group->max_members }} anggota terdaftar resmi
                        </p>
                    </div>

                    @if($isFull)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            Kuota kelompok penuh (4/4)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Tersedia {{ $group->max_members - $activeMembersCount }} slot anggota
                        </span>
                    @endif
                </div>

                <!-- Form Tambah Anggota (Jika Ketua dan Belum Penuh) -->
                @if($isLeader && !$isFull && $availableStudents->isNotEmpty())
                    <div id="inviteMemberSection" class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2">
                        <span class="text-xs font-bold text-slate-800 block">Tambah / Undang Anggota Baru</span>
                        <form action="{{ route('groups.members.add', $group) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2">
                            @csrf
                            <select name="user_id" required class="flex-1 w-full text-xs bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Mahasiswa yang Tersedia --</option>
                                @foreach($availableStudents as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->nim ?? 'NIM -' }} • {{ $student->prodi ?? 'Informatika' }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#4338ca] hover:bg-[#3730a3] text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0">
                                + Tambahkan
                            </button>
                        </form>
                    </div>
                @endif

                <!-- List Anggota Kelompok -->
                <div class="divide-y divide-slate-100 pt-1">
                    @foreach($group->activeMembers as $gm)
                        @php
                            $u = $gm->user;
                            $in = strtoupper(substr($u->name, 0, 1));
                            $parts = explode(' ', $u->name);
                            if (count($parts) > 1) {
                                $in .= strtoupper(substr($parts[1], 0, 1));
                            }
                            $isLeaderRow = $gm->user_id === $group->leader_id;
                        @endphp
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $isLeaderRow ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }} font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $in }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-xs text-slate-900">{{ $u->name }}</h4>
                                        @if($isLeaderRow)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Ketua
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                                                Anggota
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        NIM: {{ $u->nim ?? '22.11.4821' }} • {{ $u->prodi ?? 'Informatika' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terverifikasi</span>
                                </span>

                                @if(!$isLeaderRow && ($isLeader || ($currentUser && $currentUser->id === $gm->user_id)))
                                    <form action="{{ route('groups.members.remove', [$group, $gm->user]) }}" method="POST" onsubmit="return confirm('Keluarkan anggota ini dari kelompok?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-medium">
                                            {{ $currentUser && $currentUser->id === $gm->user_id ? 'Keluar' : 'Keluarkan' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    Setiap anggota kelompok memiliki hak akses ke forum diskusi internal dan berkas pengajuan.
                </p>
            </div>

            <!-- Card 2: Status Pengajuan Magang Mitra -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
                    <h3 class="font-bold text-slate-900 text-base">Pengajuan instansi mitra</h3>
                    @if($isLeader && (!$latestApp || $latestApp->status === 'ditolak'))
                        <a href="{{ route('companies.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            + Ajukan ke Mitra Baru ↗
                        </a>
                    @endif
                </div>

                @if($latestApp)
                    <!-- Detail Instansi Card -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">{{ $latestApp->company->name }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $latestApp->company->city }} • {{ ucfirst($latestApp->company->type) }} • Periode Jan – Mar 2027
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('companies.show', $latestApp->company) }}" class="px-3.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors">
                                Lihat Radar Mitra
                            </a>
                            @if(($isLeader || ($currentUser && $currentUser->isKoordinator())) && $latestApp->status === 'menunggu')
                                <button type="button" 
                                        onclick="openUpdateStatusModal({{ $latestApp->id }}, '{{ addslashes($latestApp->company->name) }}')"
                                        class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-indigo-600 text-white text-xs font-semibold transition-colors">
                                    Update Status
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Stepper Progress Pengajuan -->
                    <div class="pt-2">
                        <span class="text-xs font-bold text-slate-800 block mb-3">Alur Proses Verifikasi:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <!-- Step 1 -->
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200/80 space-y-1">
                                <div class="flex items-center gap-1.5 font-bold text-emerald-800 text-[11px]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>1. Pengajuan Dikirim</span>
                                </div>
                                <p class="text-[10px] text-emerald-700">12 Nov 2026</p>
                            </div>

                            <!-- Step 2 -->
                            <div class="p-3 rounded-xl {{ $latestApp->status === 'menunggu' ? 'bg-amber-50 border border-amber-300' : 'bg-emerald-50 border border-emerald-200/80' }} space-y-1">
                                <div class="flex items-center gap-1.5 font-bold {{ $latestApp->status === 'menunggu' ? 'text-amber-800' : 'text-emerald-800' }} text-[11px]">
                                    <span class="w-2 h-2 rounded-full {{ $latestApp->status === 'menunggu' ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                    <span>2. Tinjauan Koordinator</span>
                                </div>
                                <p class="text-[10px] {{ $latestApp->status === 'menunggu' ? 'text-amber-700' : 'text-emerald-700' }}">Sedang diproses</p>
                            </div>

                            <!-- Step 3 -->
                            <div class="p-3 rounded-xl {{ $latestApp->status === 'diterima' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200 text-slate-400' }} space-y-1">
                                <div class="font-bold text-[11px] {{ $latestApp->status === 'diterima' ? 'text-emerald-800' : 'text-slate-500' }}">
                                    3. Surat Pengantar
                                </div>
                                <p class="text-[10px]">Menunggu verifikasi</p>
                            </div>

                            <!-- Step 4 -->
                            <div class="p-3 rounded-xl {{ $latestApp->status === 'diterima' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200 text-slate-400' }} space-y-1">
                                <div class="font-bold text-[11px] {{ $latestApp->status === 'diterima' ? 'text-emerald-800' : 'text-slate-500' }}">
                                    4. Konfirmasi Mitra
                                </div>
                                <p class="text-[10px]">Penerimaan resmi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail Log -->
                    @if($latestApp->statusLogs->isNotEmpty())
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <span class="text-xs font-bold text-slate-700 block">Riwayat Audit Perubahan Status</span>
                            <div class="space-y-2">
                                @foreach($latestApp->statusLogs as $log)
                                    <div class="text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-slate-600">
                                        <div>
                                            <span class="font-bold text-slate-800 uppercase">{{ $log->status_after }}</span>
                                            <span class="text-slate-400 mx-1">•</span>
                                            <span>Oleh: <strong>{{ $log->changedBy->name ?? 'Sistem Koordinator' }}</strong></span>
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

                @else
                    <div class="p-8 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-3">
                        <p class="text-xs font-medium text-slate-600">
                            Kelompok ini belum memilih instansi mitra untuk pengajuan magang.
                        </p>
                        @if($isLeader)
                            <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4338ca] hover:bg-[#3730a3] text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                                Cari di Radar Kuota & Ajukan Magang
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        <!-- KANAN: FORUM TIM, DOKUMEN & PANDUAN (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card 1: Ruang Diskusi Forum Tim -->
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-4">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-base text-white">Forum Tim Cakrawala</h3>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                        Ruang komunikasi, koordinasi tugas, serta matchmaking pencarian peran bagi anggota kelompok.
                    </p>
                </div>

                <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs text-indigo-200">
                    <span>4 topik aktif</span>
                    <span>Aktif hari ini</span>
                </div>

                <a href="{{ route('forum.index') }}" class="w-full py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 text-xs font-bold text-center block transition-colors">
                    Buka Forum Diskusi
                </a>
            </div>

            <!-- Card 2: Dokumen Kelompok -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Dokumen pengajuan</h3>

                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-500 font-bold text-xs">PDF</span>
                            <div>
                                <h5 class="font-bold text-slate-900">Proposal Magang.pdf</h5>
                                <p class="text-[10px] text-slate-400">4.2 MB • Lengkap</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-600">Siap</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-rose-500 font-bold text-xs">PDF</span>
                            <div>
                                <h5 class="font-bold text-slate-900">Surat Pengantar.pdf</h5>
                                <p class="text-[10px] text-slate-400">1.1 MB • Terverifikasi</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-600">Siap</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-indigo-500 font-bold text-xs">ZIP</span>
                            <div>
                                <h5 class="font-bold text-slate-900">CV & Portofolio.zip</h5>
                                <p class="text-[10px] text-slate-400">8.7 MB • 4 Anggota</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-600">Siap</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    Berkas dikunci secara otomatis selama masa verifikasi koordinator.
                </p>
            </div>

            <!-- Card 3: Informasi Penting -->
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-indigo-700 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Ketentuan Verifikasi</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pengajuan magang hanya dapat disetujui jika seluruh anggota kelompok telah melengkapi biodata dan kuota mitra masih mencukupi.
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

<!-- MODAL UPDATE STATUS PENGAJUAN -->
<div id="updateStatusModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Status Pengajuan Magang</h3>
                <p id="modalCompanyName" class="text-xs text-slate-500 mt-0.5">Instansi</p>
            </div>
            <button type="button" onclick="closeUpdateStatusModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="updateStatusForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Status Baru *</label>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-emerald-500 has-checked:bg-emerald-50 transition-colors">
                        <input type="radio" name="status" value="diterima" required class="sr-only">
                        <span class="block font-bold text-emerald-700">DITERIMA</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Disetujui</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-rose-500 has-checked:bg-rose-50 transition-colors">
                        <input type="radio" name="status" value="ditolak" class="sr-only">
                        <span class="block font-bold text-rose-700">DITOLAK</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Penuh/batal</span>
                    </label>
                    <label class="cursor-pointer border border-slate-200 rounded-xl p-3 text-center has-checked:border-slate-500 has-checked:bg-slate-100 transition-colors">
                        <input type="radio" name="status" value="dibatalkan" class="sr-only">
                        <span class="block font-bold text-slate-700">BATAL</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Mandiri</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan / Catatan Perubahan *</label>
                <textarea name="reason" rows="3" required placeholder="Catatan perubahan status pengajuan..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-800 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeUpdateStatusModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4338ca] hover:bg-[#3730a3] text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                    Simpan Perubahan
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
