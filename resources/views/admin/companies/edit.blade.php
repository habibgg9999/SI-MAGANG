@extends('layouts.app')

@section('title', 'Edit Mitra & Kuota: ' . $company->name)

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Panel Koordinator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Edit data & kuota mitra.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Perbarui informasi kemitraan atau sesuaikan kuota resmi penerimaan mahasiswa.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('companies.show', $company) }}" target="_blank" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-indigo-600 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <span>Lihat Radar Publik</span>
                <span>↗</span>
            </a>
            <a href="{{ route('admin.companies.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- 2. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Form Edit Utama (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('admin.companies.update', $company) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                    
                    <!-- Nama & Tipe -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama instansi / mitra *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $company->name) }}" 
                                   required 
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
                                @foreach($types as $t)
                                    <option value="{{ $t }}" {{ old('type', $company->type) === $t ? 'selected' : '' }}>
                                        {{ ucfirst($t) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Kuota Box & Kota & Bidang -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-indigo-50/50 border border-indigo-100 space-y-1">
                            <label for="quota" class="block text-xs font-bold text-indigo-900">
                                Kuota resmi (Slot) *
                            </label>
                            <input type="number" 
                                   id="quota" 
                                   name="quota" 
                                   value="{{ old('quota', $company->quota) }}" 
                                   min="0" 
                                   max="100" 
                                   required 
                                   class="w-full text-base font-extrabold bg-white border border-indigo-200 rounded-xl px-3.5 py-2 text-indigo-950 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                            <p class="text-[10px] text-indigo-700">Diterima saat ini: <strong>{{ $company->accepted_members_count }}</strong> mahasiswa.</p>
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Kota penempatan
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $company->city) }}" 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="field" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Bidang magang
                            </label>
                            <input type="text" 
                                   id="field" 
                                   name="field" 
                                   value="{{ old('field', $company->field) }}" 
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
                                  class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('address', $company->address) }}</textarea>
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
                                   value="{{ old('contact_person', $company->contact_person) }}" 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor telepon / WhatsApp PIC
                            </label>
                            <input type="text" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone', $company->contact_phone) }}" 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Status Toggles -->
                    <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Status aktif</span>
                                <p class="text-[10px] text-slate-500">Tampilkan di Radar Kuota mahasiswa</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 cursor-pointer">
                            <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $company->is_approved) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300">
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
                            Simpan perubahan
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Kanan: Preview & Dampak (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card Dampak Kuota -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-base">Dampak perubahan kuota</h3>
                
                <p class="text-xs text-slate-500 leading-relaxed">
                    Setiap penyesuaian kuota akan langsung mengkalkulasi ulang persentase ketersediaan pada Radar Kuota mahasiswa:
                </p>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Kondisi saat ini:</span>
                    <div class="font-bold text-slate-900 text-sm">
                        {{ $company->accepted_members_count }} mahasiswa diterima / {{ $company->quota }} kuota
                    </div>
                    <span class="text-[11px] text-indigo-600 font-semibold block">
                        {{ max(0, $company->quota - $company->accepted_members_count) }} slot tersisa
                    </span>
                </div>
            </div>

            <!-- Notice Keamanan -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                    <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>Penetapan Kuota Minimal</span>
                </div>
                <p class="text-xs text-amber-900/80 leading-relaxed">
                    Pastikan kuota tidak lebih kecil dari jumlah mahasiswa yang telah disetujui ({{ $company->accepted_members_count }} mahasiswa) untuk mencegah ketidakkonsistenan data.
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
