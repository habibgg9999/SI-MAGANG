@extends('layouts.app')

@section('title', 'Tambah Mitra Baru - Panel Koordinator')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Panel Koordinator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Tambah mitra baru.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Daftarkan instansi mitra dan tetapkan alokasi kuota resmi penerimaan mahasiswa.
            </p>
        </div>

        <div class="shrink-0 text-xs text-slate-400 font-medium">
            Entri mitra baru
        </div>
    </div>

    <!-- 2. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Form Utama (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('admin.companies.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                    
                    <!-- Nama & Tipe -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama instansi / perusahaan *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="Contoh: PT Telkom Indonesia (Persero) Tbk"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                            @error('name')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Tipe instansi *
                            </label>
                            <select id="type" 
                                    name="type" 
                                    required 
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 cursor-pointer focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                                <option value="">Pilih tipe</option>
                                @foreach($types as $t)
                                    <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>
                                        {{ ucfirst($t) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kuota, Kota, Bidang -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="quota" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Kuota resmi (Slot) *
                            </label>
                            <input type="number" 
                                   id="quota" 
                                   name="quota" 
                                   value="{{ old('quota', 5) }}" 
                                   min="0" 
                                   max="100" 
                                   required 
                                   class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                            <p class="text-[10px] text-slate-400 mt-1">Dasar perhitungan radar.</p>
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Kota penempatan
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}" 
                                   placeholder="Yogyakarta / Jakarta"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="field" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Bidang magang
                            </label>
                            <input type="text" 
                                   id="field" 
                                   name="field" 
                                   value="{{ old('field') }}" 
                                   placeholder="Sistem informasi / IT"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alamat kantor instansi
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="2" 
                                  placeholder="Alamat kantor atau lokasi pelaksanaan magang..."
                                  class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                    </div>

                    <!-- Kontak PIC -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_person" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama PIC / HRD
                            </label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   value="{{ old('contact_person') }}" 
                                   placeholder="Contoh: Bpk. Budi Santoso"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor telepon / WhatsApp PIC
                            </label>
                            <input type="text" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone') }}" 
                                   placeholder="081234567890"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Status Toggles -->
                    <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Status aktif</span>
                                <p class="text-[10px] text-slate-500">Tampilkan di Radar Kuota mahasiswa</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 cursor-pointer">
                            <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', true) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Status terverifikasi</span>
                                <p class="text-[10px] text-slate-500">Mitra resmi yang telah disetujui kampus</p>
                            </div>
                        </label>
                    </div>

                </div>

                <!-- Form Bottom Actions -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs text-slate-400">* Wajib diisi</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.companies.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                            Simpan instansi
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Kanan: Panduan Kuota (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Panduan Penetapan Kuota -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Aturan kuota radar</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                        <div>
                            <h4 class="font-bold text-slate-900">Hijau (Tersedia)</h4>
                            <p class="text-slate-500 mt-0.5">Terisi kurang dari 50% dari total kuota resmi.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 pt-2 border-t border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                        <div>
                            <h4 class="font-bold text-slate-900">Kuning (Terbatas)</h4>
                            <p class="text-slate-500 mt-0.5">Terisi antara 50% hingga 89% dari total kuota.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 pt-2 border-t border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></span>
                        <div>
                            <h4 class="font-bold text-slate-900">Merah (Penuh)</h4>
                            <p class="text-slate-500 mt-0.5">Terisi 90% ke atas atau kuota telah habis.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notice Instansi Baru -->
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-indigo-700 text-xs font-bold">
                    <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Penyelarasan Radar</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Data yang disimpan akan langsung muncul di katalog Radar Kuota mahasiswa dan dapat dipilih oleh kelompok magang.
                </p>
            </div>

        </div>

    </div>

    <!-- 3. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Manajemen mitra koordinator magang.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
