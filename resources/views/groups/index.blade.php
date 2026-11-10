@extends('layouts.app')

@section('title', 'Kelompok - Kolaborasi Mahasiswa')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Kolaborasi Mahasiswa
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Langkah besar dimulai bersama.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Kelola kelompokmu dan temukan tim magang di program studi Informatika.
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('groups.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0">
                <span>+ Buat kelompok</span>
            </a>
        </div>
    </div>

    <!-- 2. KELOMPOK SAYA BANNER CARD -->
    @if($myGroup)
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 font-extrabold text-base flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($myGroup->name, 0, 1) . substr(strstr($myGroup->name, ' ') ?: ' ', 1, 1)) }}
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Kelompok Saya</span>
                        <h2 class="font-extrabold text-lg text-slate-900 mt-0.5 leading-snug">{{ $myGroup->name }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $myGroup->description ?? 'IF-07 • Informatika' }} • Ketua: {{ $myGroup->leader?->name ?? 'Aditya Nugraha' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <!-- Member Avatars -->
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-1.5 overflow-hidden">
                            @foreach($myGroup->activeMembers->take(4) as $m)
                                @php
                                    $in = strtoupper(substr($m->user->name, 0, 1));
                                    $parts = explode(' ', $m->user->name);
                                    if (count($parts) > 1) {
                                        $in .= strtoupper(substr($parts[1], 0, 1));
                                    }
                                @endphp
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-50 border-2 border-white text-[10px] font-bold text-indigo-700">
                                    {{ $in }}
                                </span>
                            @endforeach
                        </div>
                        <span class="text-xs font-semibold text-slate-600">
                            {{ $myGroup->activeMembers->count() }}/{{ $myGroup->max_members }} anggota
                        </span>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span>Menunggu verifikasi</span>
                        </span>
                        <span class="text-[11px] text-slate-400 block text-right mt-0.5">Diajukan 12 Nov 2026</span>
                    </div>

                    <!-- Tombol Lihat Detail -->
                    <a href="{{ route('groups.show', $myGroup) }}" 
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all">
                        <span>Lihat detail</span>
                        <span class="text-slate-400">↗</span>
                    </a>
                </div>

            </div>

            <!-- Subrow Info -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-slate-100 text-xs text-slate-500">
                <span class="font-medium text-slate-700">
                    {{ $myGroup->latestApplication?->company?->name ?? 'Dinas Komunikasi Informatika dan Statistik Provinsi' }}
                </span>
                <span class="text-slate-400">
                    {{ $myGroup->latestApplication?->company?->field ?? 'Sistem informasi' }} • Jan – Mar 2027
                </span>
            </div>
        </div>
    @endif

    <!-- 3. DIREKTORI KELOMPOK TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
        
        <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
            <h3 class="font-bold text-slate-900 text-base">Direktori kelompok</h3>
            <span class="text-xs text-slate-400 font-medium">32 kelompok • Data contoh</span>
        </div>

        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('groups.index') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama kelompok, ketua, atau mitra..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="w-full sm:w-44 shrink-0">
                    <select name="prodi" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer">
                        <option value="">Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                    </select>
                </div>

                <div class="w-full sm:w-44 shrink-0">
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer">
                        <option value="">Semua status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Mencari Anggota</option>
                    </select>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                    Semua kelompok 32
                </a>
                <a href="#" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200">
                    Kelompok saya 1
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'open']) }}" class="px-3 py-1 rounded-full text-xs font-semibold {{ request('status') === 'open' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                    Mencari anggota 6
                </a>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto pt-2">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pl-2">Kelompok / Ketua</th>
                        <th class="pb-3">Mitra / Bidang</th>
                        <th class="pb-3 text-center">Anggota</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right pr-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groups as $g)
                        @php
                            $tc = strtoupper(substr($g->name, 0, 1) . substr(strstr($g->name, ' ') ?: ' ', 1, 1));
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 pl-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $tc }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900">{{ $g->name }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">IF-07 • {{ $g->leader?->name ?? 'Ketua' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5">
                                <p class="font-semibold text-slate-800">{{ $g->latestApplication?->company?->name ?? 'Belum ada instansi' }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $g->latestApplication?->company?->field ?? 'Sistem informasi' }}</p>
                            </td>
                            <td class="py-3.5 text-center font-medium text-slate-700">
                                {{ $g->activeMembers->count() }}/{{ $g->max_members }} orang
                            </td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Menunggu verifikasi</span>
                                </span>
                            </td>
                            <td class="py-3.5 text-right pr-2">
                                <a href="{{ route('groups.show', $g) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-white hover:border-slate-300 transition-colors">
                                    Lihat detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada kelompok yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 text-xs text-slate-400">
            <div>
                Menampilkan 1–{{ $groups->count() }} dari {{ $groups->total() }} kelompok
            </div>
            <div>
                {{ $groups->links() }}
            </div>
        </div>

    </div>

    <!-- 4. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Nama, kuota, dan status bersifat ilustratif.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
