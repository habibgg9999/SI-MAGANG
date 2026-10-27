@extends('layouts.app')

@section('title', 'Tambah Instansi Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-6 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.companies.index') }}" class="p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Instansi Mitra Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daftarkan mitra magang dan tentukan alokasi kuota resmi mahasiswa.</p>
            </div>
        </div>
    </div>

    <!-- FORM -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form action="{{ route('admin.companies.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- NAMA & TIPE -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2 space-y-1.5">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nama Instansi / Perusahaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           placeholder="Contoh: PT Telkom Indonesia (Persero) Tbk"
                           class="w-full text-sm rounded-xl border @error('name') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Tipe Instansi <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            required 
                            class="w-full text-sm rounded-xl border @error('type') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden cursor-pointer">
                        <option value="">Pilih Tipe</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>
                                {{ strtoupper($t) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- KUOTA & BIDANG & KOTA -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label for="quota" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kuota Resmi (Slot Mhs) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           id="quota" 
                           name="quota" 
                           value="{{ old('quota', 5) }}" 
                           min="0" 
                           max="100" 
                           required 
                           class="w-full text-sm rounded-xl border @error('quota') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden font-bold">
                    <p class="text-[11px] text-slate-400">Dasar perhitungan Radar Kuota (Traffic Light).</p>
                    @error('quota')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="city" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kota Penempatan
                    </label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="{{ old('city') }}" 
                           placeholder="Contoh: Jakarta Selatan / Yogyakarta"
                           class="w-full text-sm rounded-xl border @error('city') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    @error('city')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="field" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Bidang Pekerjaan
                    </label>
                    <input type="text" 
                           id="field" 
                           name="field" 
                           value="{{ old('field') }}" 
                           placeholder="Contoh: Software Engineering / Cloud"
                           class="w-full text-sm rounded-xl border @error('field') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    @error('field')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ALAMAT LENGKAP -->
            <div class="space-y-1.5">
                <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Alamat Lengkap Instansi
                </label>
                <textarea id="address" 
                          name="address" 
                          rows="3" 
                          placeholder="Contoh: Jl. Gatot Subroto No. 52, Kuningan Barat, Mampang Prapatan, Jakarta Selatan"
                          class="w-full text-sm rounded-xl border @error('address') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- KONTAK PIC -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label for="contact_person" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nama PIC / HRD Instansi
                    </label>
                    <input type="text" 
                           id="contact_person" 
                           name="contact_person" 
                           value="{{ old('contact_person') }}" 
                           placeholder="Contoh: Bpk. Budi Santoso (HR Manager)"
                           class="w-full text-sm rounded-xl border @error('contact_person') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    @error('contact_person')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="contact_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nomor WhatsApp PIC
                    </label>
                    <input type="text" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="{{ old('contact_phone') }}" 
                           placeholder="Contoh: 081234567890"
                           class="w-full text-sm rounded-xl border @error('contact_phone') border-rose-500 @else border-slate-300 @enderror p-3 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    <p class="text-[11px] text-slate-400">Digunakan untuk integrasi hubungi PIC via WhatsApp.</p>
                    @error('contact_phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- STATUS TOGGLES -->
            <div class="pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded-sm focus:ring-purple-500 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-800">Status Aktif</span>
                        <p class="text-[11px] text-slate-500">Tampilkan instansi ini di Radar Kuota publik mahasiswa.</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', true) ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded-sm focus:ring-purple-500 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-800">Status Disetujui (Approved)</span>
                        <p class="text-[11px] text-slate-500">Tandai sebagai mitra resmi yang telah lolos verifikasi kampus.</p>
                    </div>
                </label>
            </div>

            <!-- BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.companies.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-sm transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Instansi</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
