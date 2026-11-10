@extends('layouts.app')

@section('title', 'Kelola Mitra & Kuota - Panel Koordinator')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Panel Koordinator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Kelola mitra & kuota magang.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Atur instansi resmi, pantau status kuota, dan tentukan ketersediaan kuota mahasiswa.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.companies.create') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0">
                <span>+ Tambah Mitra Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. FILTER & SEARCH BAR -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama instansi, kota, bidang kerja, atau PIC..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="w-full sm:w-52 shrink-0">
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua tipe instansi</option>
                    <option value="pemerintah" {{ $type === 'pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                    <option value="BUMN" {{ $type === 'BUMN' ? 'selected' : '' }}>BUMN</option>
                    <option value="swasta" {{ $type === 'swasta' ? 'selected' : '' }}>Swasta</option>
                    <option value="NGO" {{ $type === 'NGO' ? 'selected' : '' }}>NGO</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                <span>Filter</span>
            </button>
        </form>
    </div>

    <!-- 3. TABEL DATA MITRA -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-1">
            <h3 class="font-bold text-slate-900 text-base">Daftar mitra terdaftar</h3>
            <span class="text-xs text-slate-400 font-medium">Total {{ $companies->total() }} mitra di sistem</span>
        </div>

        <div class="overflow-x-auto pt-1">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pl-2">Nama Instansi / Mitra</th>
                        <th class="pb-3">Tipe & Lokasi</th>
                        <th class="pb-3 text-center">Status Kuota</th>
                        <th class="pb-3 text-center">Kuota Resmi</th>
                        <th class="pb-3 text-center">Diterima</th>
                        <th class="pb-3 text-center">Publikasi</th>
                        <th class="pb-3">Kontak PIC</th>
                        <th class="pb-3 text-right pr-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($companies as $company)
                        @php
                            $status = $company->quota_status;
                            $level = $status['level'] ?? 'green';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 pl-2">
                                <a href="{{ route('companies.show', $company) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                    {{ $company->name }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $company->field ?? 'Sistem informasi' }}
                                </p>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ ucfirst($company->type) }}
                                </span>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $company->city ?? 'Yogyakarta' }}
                                </p>
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
                            <td class="py-3.5 text-center font-bold text-slate-900">
                                {{ $company->quota }} slot
                            </td>
                            <td class="py-3.5 text-center font-bold text-emerald-600">
                                {{ $company->accepted_members_count }} mhs
                            </td>
                            <td class="py-3.5 text-center">
                                <form action="{{ route('admin.companies.toggle-active', $company) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold cursor-pointer transition-colors {{ $company->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $company->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $company->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $company->contact_person ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $company->contact_phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 text-right pr-2">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.companies.edit', $company) }}" 
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Hapus instansi ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">Tidak ada mitra yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 text-xs text-slate-400">
            <div>
                Menampilkan {{ $companies->firstItem() ?? 0 }}–{{ $companies->lastItem() ?? 0 }} dari {{ $companies->total() }} mitra
            </div>
            <div>
                {{ $companies->links() }}
            </div>
        </div>
    </div>

    <!-- 4. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Perubahan langsung berdampak pada Radar Kuota mahasiswa.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
