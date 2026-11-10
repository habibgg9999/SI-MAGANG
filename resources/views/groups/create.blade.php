@extends('layouts.app')

@section('title', 'Buat Kelompok Baru')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Kelompok Baru
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Satukan tujuan, bentuk timmu.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Lengkapi identitas, undang anggota, lalu tentukan preferensi magang kelompok.
            </p>
        </div>

        <div class="shrink-0 text-xs text-slate-400 font-medium">
            Draf kelompok baru
        </div>
    </div>

    <!-- 2. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Form Utama (7-8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('groups.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Card 1: Identitas & Preferensi -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-base">Identitas & preferensi magang</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama kelompok *
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', 'Tim Lentera') }}" 
                                   required 
                                   placeholder="Masukkan nama kelompok..." 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Program studi *
                            </label>
                            <select name="prodi" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 cursor-pointer focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                                <option value="Informatika">Informatika</option>
                                <option value="Sistem Informasi">Sistem Informasi</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Deskripsi kelompok
                        </label>
                        <textarea name="description" 
                                  rows="3" 
                                  placeholder="Jelaskan fokus atau preferensi proyek magang tim..." 
                                  class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">Tim yang berfokus pada pengembangan sistem informasi dan pengalaman pengguna untuk layanan publik.</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Bidang magang *
                            </label>
                            <select name="field" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 cursor-pointer focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                                <option value="Sistem informasi">Sistem informasi</option>
                                <option value="Jaringan">Jaringan</option>
                                <option value="Web Development">Web Development</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Periode magang *
                            </label>
                            <select name="period" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 cursor-pointer focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                                <option value="Januari – Maret 2027">Januari – Maret 2027</option>
                                <option value="Juli – September 2027">Juli – September 2027</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Undang Anggota -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 text-base">Undang anggota melalui NIM atau email</h3>

                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   placeholder="NIM atau email mahasiswa..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <button type="button" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors shrink-0">
                            + Tambah
                        </button>
                    </div>

                    <!-- Member List -->
                    <div class="space-y-3 pt-2">
                        <!-- Member 1: Leader (Anda) -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                    AN
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900">Aditya Nugraha (Anda)</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">22.11.4821 • Ketua kelompok</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-200 text-slate-600">
                                Otomatis
                            </span>
                        </div>

                        <!-- Member 2: Bima Santoso -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200/80">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center">
                                    BS
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900">Bima Santoso</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">22.11.4871 • Informatika</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Siap diundang
                                </span>
                                <button type="button" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Hapus</button>
                            </div>
                        </div>

                        <!-- Member 3: Citra Amelia -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200/80">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center">
                                    CA
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900">Citra Amelia</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">22.11.4878 • Informatika</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Siap diundang
                                </span>
                                <button type="button" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                        3 dari maksimal 4 anggota • Undangan dikirim setelah kelompok dibuat.
                    </p>
                </div>

                <!-- Form Bottom Actions -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs text-slate-400">* Wajib diisi</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('groups.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                            Buat kelompok
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Kanan: Guidance & Preview (4-5 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Panduan Sebelum Membuat Kelompok -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Sebelum membuat kelompok</h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Pilih rekan satu tujuan</h4>
                            <p class="text-slate-500 mt-0.5">Kelompok berisi 3–4 mahasiswa dari program studi yang sesuai.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Konfirmasi keanggotaan</h4>
                            <p class="text-slate-500 mt-0.5">Anggota perlu menerima undangan sebelum pengajuan dikirim.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            3
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Lanjutkan ke Radar Kuota</h4>
                            <p class="text-slate-500 mt-0.5">Pilih mitra dan lengkapi dokumen setelah kelompok siap.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Satu Pengajuan Aktif Alert -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Satu pengajuan aktif</span>
                </div>
                <p class="text-xs text-amber-900/80 leading-relaxed">
                    Anda masih tergabung di Tim Cakrawala. Kelompok baru menjadi draf dan tidak menggantikan pengajuan yang sedang ditinjau.
                </p>
            </div>

            <!-- Ringkasan Draf Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3 text-xs">
                <h3 class="font-bold text-slate-900 text-base">Ringkasan draf</h3>
                
                <div>
                    <span class="text-slate-400 block text-[11px]">Ketua</span>
                    <span class="font-bold text-slate-900 mt-0.5 block">Aditya Nugraha</span>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400 block text-[11px]">Preferensi</span>
                    <span class="font-semibold text-slate-800 mt-0.5 block">Sistem informasi • Jan – Mar 2027</span>
                </div>

                <p class="text-[11px] text-slate-400 pt-1">
                    Data contoh • Belum ada undangan terkirim.
                </p>
            </div>

        </div>

    </div>

    <!-- 3. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-4 text-xs text-slate-400 font-normal">
        <div>Data contoh • Nama, kuota, dan status bersifat ilustratif.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
