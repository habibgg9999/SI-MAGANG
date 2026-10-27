@extends('layouts.app')

@section('title', 'Kelola Instansi & Kuota Resmi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Katalog Instansi & Kuota</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Tambah instansi mitra, kelola kuota resmi penerimaan mahasiswa, dan aktivasi mitra.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 shadow-2xs transition-all">
                Dashboard
            </a>
            <a href="{{ route('admin.companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Instansi Baru</span>
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama instansi, kota, bidang kerja, atau PIC..." 
                       class="w-full text-xs pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
            </div>

            <div class="w-full sm:w-48">
                <select name="type" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 focus:outline-hidden cursor-pointer">
                    <option value="">Semua Tipe Instansi</option>
                    <option value="pemerintah" {{ $type === 'pemerintah' ? 'selected' : '' }}>🏛️ Pemerintah</option>
                    <option value="BUMN" {{ $type === 'BUMN' ? 'selected' : '' }}>🏢 BUMN</option>
                    <option value="swasta" {{ $type === 'swasta' ? 'selected' : '' }}>💼 Swasta</option>
                    <option value="NGO" {{ $type === 'NGO' ? 'selected' : '' }}>🤝 NGO</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors">
                    Filter
                </button>
                @if($search || $type)
                    <a href="{{ route('admin.companies.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5 text-left">Nama Instansi</th>
                        <th class="py-3.5 px-5 text-left">Tipe & Kota</th>
                        <th class="py-3.5 px-5 text-center">Status Kuota</th>
                        <th class="py-3.5 px-5 text-center">Kuota Resmi</th>
                        <th class="py-3.5 px-5 text-center">Mhs Diterima</th>
                        <th class="py-3.5 px-5 text-center">Status Publik</th>
                        <th class="py-3.5 px-5 text-left">Kontak PIC</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($companies as $company)
                        @php
                            $badge = $company->traffic_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('companies.show', $company) }}" class="font-bold text-slate-900 hover:text-purple-700">
                                    {{ $company->name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $company->field ?? 'Umum' }}
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $company->type }}
                                </span>
                                <div class="text-xs text-slate-500 mt-1">
                                    📍 {{ $company->city ?? '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['class'] }}">
                                    <span>{{ $badge['indicator'] }}</span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center font-bold text-slate-900">
                                <span class="text-base text-purple-700">{{ $company->quota }}</span> Slot
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $company->accepted_members_count }} Mhs
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <form action="{{ route('admin.companies.toggle-active', $company) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors {{ $company->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                            title="Klik untuk ubah status aktif">
                                        <span class="w-2 h-2 rounded-full {{ $company->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $company->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-5 text-xs text-slate-600">
                                <div class="font-medium text-slate-800">{{ $company->contact_person ?? '-' }}</div>
                                <div class="text-slate-500">{{ $company->contact_phone ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit -->
                                    <a href="{{ route('admin.companies.edit', $company) }}" 
                                       class="px-2.5 py-1.5 bg-slate-100 hover:bg-purple-100 text-slate-700 hover:text-purple-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus instansi {{ addslashes($company->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                title="Hapus Instansi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data instansi yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
