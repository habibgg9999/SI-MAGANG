@extends('layouts.app')

@section('title', 'Buat Kelompok Magang Baru')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-6">

    <!-- BREADCRUMBS -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('groups.index') }}" class="hover:text-indigo-600 transition-colors">Kelompok Magang</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold">Buat Kelompok Baru</span>
    </nav>

    <!-- FORM CARD -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm space-y-8">
        
        <div class="border-b border-slate-100 pb-6 space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Pendaftaran Kelompok Baru</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Inisialisasi Kelompok Magang
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Sebagai pembuat kelompok, Anda otomatis didaftarkan sebagai <strong>Ketua Kelompok</strong> dan anggota aktif pertama.
            </p>
        </div>

        <!-- ATURAN VALIDASI PENTING -->
        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs space-y-1.5">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aturan Penting Keanggotaan:
            </div>
            <ul class="list-disc list-inside space-y-1 text-amber-800">
                <li>Mahasiswa yang sudah tergabung dalam suatu kelompok aktif <strong>TIDAK DAPAT</strong> membuat kelompok lain atau bergabung ke kelompok lain.</li>
                <li>Hanya Ketua Kelompok yang berhak mengajukan pendaftaran magang ke instansi mitra.</li>
                <li>Kelompok hanya bisa mengajukan 1 instansi jika belum ada pengajuan berstatus 'menunggu'.</li>
            </ul>
        </div>

        <form action="{{ route('groups.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Kelompok -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Nama Kelompok <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       placeholder="Contoh: Tim Inovasi Cloud Dev..." 
                       class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-3 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden transition-all @error('name') border-rose-400 bg-rose-50 @enderror">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kapasitas Maksimal Anggota -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Kapasitas Maksimal Anggota (Termasuk Ketua) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" 
                           name="max_members" 
                           value="{{ old('max_members', 5) }}" 
                           min="2" 
                           max="10" 
                           required 
                           class="w-32 text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-3 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden @error('max_members') border-rose-400 @enderror">
                    <span class="text-xs text-slate-500">Mahasiswa (Rekomendasi pedoman magang: 2 s/d 5 orang, maksimal 10 orang).</span>
                </div>
                @error('max_members')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi Kelompok -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Deskripsi / Rencana Fokus Magang (Opsional)
                </label>
                <textarea name="description" 
                          rows="4" 
                          placeholder="Jelaskan fokus keahlian kelompok, instansi target (misal: BUMN/Pemerintah), atau kriteria anggota yang dicari..." 
                          class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-3 border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden placeholder:text-slate-400">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('groups.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition-all hover:scale-102 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan & Buat Kelompok</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
