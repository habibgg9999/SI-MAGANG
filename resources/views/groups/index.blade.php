@extends('layouts.app')

@section('title', 'Daftar Kelompok Magang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- HERO & STATUS PENGGUNA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-sky-50/60 border border-blue-100/80 p-6 sm:p-8 shadow-sm">
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-blue-100/90 text-blue-800 border border-blue-200/80 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 pulse-live"></span>
                    <span>Modul 3: Kolaborasi Kelompok Magang</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">
                    Manajemen Kelompok Mahasiswa
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Bentuk tim solid, kelola anggota, dan ajukan pendaftaran magang secara terkoordinasi. Setiap mahasiswa hanya dapat terdaftar pada <strong>satu kelompok aktif</strong>.
                </p>
            </div>

            <!-- Tombol Buat Kelompok / Status Kelompok Saya -->
            <div class="shrink-0">
                @if($currentUser && $myGroup)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-2">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="text-slate-500 font-medium">Kelompok Aktif Anda:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $myGroup->leader_id === $currentUser->id ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700' }}">
                                {{ $myGroup->leader_id === $currentUser->id ? '👑 Ketua' : '👤 Anggota' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">{{ $myGroup->name }}</h3>
                        <a href="{{ route('groups.show', $myGroup) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors w-full justify-center shadow-sm">
                            <span>Buka Dashboard Kelompok Saya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                @elseif($currentUser)
                    <a href="{{ route('groups.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Kelompok Baru</span>
                    </a>
                @else
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 text-xs text-slate-600">
                        🔒 Masuk akun untuk membuat atau bergabung ke kelompok.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PENCARIAN & FILTER KELOMPOK -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('groups.index') }}" class="flex-1 flex flex-col sm:flex-row gap-3 w-full">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama kelompok, ketua, atau deskripsi..." 
                       class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm bg-slate-50 text-slate-800 rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 focus:outline-hidden">
            </div>

            <div class="flex items-center gap-2">
                <select name="status" class="text-xs bg-slate-50 text-slate-800 rounded-xl px-3 py-2 border border-slate-200 focus:outline-hidden">
                    <option value="">Semua Status Rekrutmen</option>
                    <option value="open" {{ $filterStatus === 'open' ? 'selected' : '' }}>Terbuka untuk Anggota Baru</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filter
                </button>
                @if($search || $filterStatus)
                    <a href="{{ route('groups.index') }}" class="px-3 py-2 text-rose-600 hover:bg-rose-50 text-xs font-semibold rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <span class="text-xs text-slate-500 whitespace-nowrap">
            Total: <strong class="text-slate-800">{{ $groups->total() }}</strong> Kelompok
        </span>
    </div>

    <!-- GRID DAFTAR KELOMPOK -->
    @if($groups->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups as $group)
                @php
                    $activeCount = $group->activeMembers->count();
                    $isFull = $group->isFull();
                    $isOpen = $group->is_open && !$isFull;
                    $isMyGroup = $currentUser && $currentUser->id && $group->activeMembers->contains('user_id', $currentUser->id);
                @endphp

                <div class="bg-white rounded-2xl border {{ $isMyGroup ? 'border-indigo-400 ring-2 ring-indigo-500/20 shadow-md' : 'border-slate-200/90 shadow-xs' }} hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden">
                    
                    <div class="p-5 space-y-4">
                        <!-- Top status pill & group badge -->
                        <div class="flex items-center justify-between gap-2">
                            @if($isMyGroup)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    ⭐ Kelompok Saya
                                </span>
                            @else
                                <span class="text-xs font-semibold text-slate-500">Kelompok Magang</span>
                            @endif

                            @if($isOpen)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Terbuka (Open)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Ditutup / Penuh
                                </span>
                            @endif
                        </div>

                        <!-- Nama Kelompok & Deskripsi -->
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                <a href="{{ route('groups.show', $group) }}">
                                    {{ $group->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                {{ $group->description ?? 'Belum ada deskripsi khusus untuk kelompok ini.' }}
                            </p>
                        </div>

                        <!-- Info Ketua & Kuota Anggota -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Ketua Kelompok:</span>
                                <strong class="text-slate-800">{{ $group->leader->name }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Kapasitas Anggota:</span>
                                <span class="font-extrabold text-slate-900">
                                    {{ $activeCount }} / {{ $group->max_members }} <span class="font-normal text-slate-500">Mahasiswa</span>
                                </span>
                            </div>

                            <!-- Progress Bar Anggota -->
                            <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                @php
                                    $fillPct = min(100, round(($activeCount / $group->max_members) * 100));
                                @endphp
                                <div class="h-full rounded-full {{ $isFull ? 'bg-amber-500' : 'bg-indigo-600' }}" style="width: {{ $fillPct }}%"></div>
                            </div>
                        </div>

                        <!-- Status Pengajuan Magang Terkini -->
                        <div class="text-xs">
                            <span class="text-slate-500 block text-[11px] font-semibold mb-1">Target / Status Magang:</span>
                            @if($group->latestApplication)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                                    <span class="font-medium text-slate-800 truncate max-w-[150px]" title="{{ $group->latestApplication->company->name }}">
                                        🏢 {{ $group->latestApplication->company->name }}
                                    </span>
                                    @php
                                        $appStatus = $group->latestApplication->status;
                                        $badgeColor = match($appStatus) {
                                            'diterima' => 'bg-emerald-100 text-emerald-800',
                                            'menunggu' => 'bg-amber-100 text-amber-800',
                                            'ditolak' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $badgeColor }}">
                                        {{ $appStatus }}
                                    </span>
                                </div>
                            @else
                                <span class="text-slate-400 italic text-[11px]">Belum mengajukan pendaftaran ke instansi.</span>
                            @endif
                        </div>
                    </div>

                    <!-- CTA Bawah -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('groups.show', $group) }}" 
                           class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-indigo-600 hover:text-white transition-colors">
                            <span>Kelola & Lihat Anggota</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $groups->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 max-w-md mx-auto space-y-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Kelompok</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada kelompok yang cocok dengan kriteria pencarian Anda.</p>
            </div>
            <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
                Buat Kelompok Pertama
            </a>
        </div>
    @endif

</div>
@endsection
