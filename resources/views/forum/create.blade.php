@extends('layouts.app')

@section('title', 'Buat Postingan Forum')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Forum Matchmaking
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Buat postingan baru.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Publikasikan pencarian rekan tim atau tawarkan keahlianmu ke sesama mahasiswa.
            </p>
        </div>

        <div class="shrink-0 text-xs text-slate-400 font-medium">
            Draf postingan baru
        </div>
    </div>

    <!-- 2. TWO COLUMNS LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Form Utama (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('forum.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                    
                    <!-- Pilihan Tipe Postingan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Jenis postingan *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Cari Anggota -->
                            <label class="p-4 rounded-xl border cursor-pointer transition-all {{ $isLeader ? 'has-checked:border-indigo-600 has-checked:bg-indigo-50/40 border-slate-200' : 'opacity-60 bg-slate-50 border-slate-200 cursor-not-allowed' }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900">
                                        Butuh Anggota
                                    </span>
                                    <input type="radio" 
                                           name="type" 
                                           value="cari_anggota" 
                                           {{ $defaultType === 'cari_anggota' && $isLeader ? 'checked' : '' }} 
                                           {{ !$isLeader ? 'disabled' : '' }}
                                           onchange="toggleTypeFields('cari_anggota')"
                                           class="text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Khusus ketua kelompok untuk menambah anggota tim magang.
                                </p>
                                @if($isLeader && $myActiveGroup)
                                    <span class="inline-block mt-2 text-[10px] font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                        {{ $myActiveGroup->name }} (Sisa {{ max(1, $myActiveGroup->max_members - $myActiveGroup->activeMembers->count()) }} slot)
                                    </span>
                                @endif
                            </label>

                            <!-- Cari Kelompok -->
                            <label class="p-4 rounded-xl border border-slate-200 cursor-pointer transition-all has-checked:border-indigo-600 has-checked:bg-indigo-50/40">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900">
                                        Cari Kelompok
                                    </span>
                                    <input type="radio" 
                                           name="type" 
                                           value="cari_kelompok" 
                                           {{ $defaultType === 'cari_kelompok' || !$isLeader ? 'checked' : '' }}
                                           onchange="toggleTypeFields('cari_kelompok')"
                                           class="text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    Untuk mahasiswa mandiri yang sedang mencari kelompok magang.
                                </p>
                                <span class="inline-block mt-2 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                    {{ $currentUser->name }} • {{ $currentUser->prodi ?? 'Informatika' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Judul Postingan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Judul postingan *
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required 
                               placeholder="Contoh: Butuh 1 Frontend Developer (React/Tailwind) untuk Tim Cakrawala" 
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Bidang Kerja & Target Instansi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Bidang keahlian
                            </label>
                            <input type="text" 
                                   name="field" 
                                   value="{{ old('field') }}" 
                                   list="fieldList"
                                   placeholder="Frontend, Backend, UI/UX..." 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                            <datalist id="fieldList">
                                <option value="Frontend Developer">
                                <option value="Backend Developer">
                                <option value="UI/UX Designer">
                                <option value="Data Science & AI">
                                <option value="Cyber Security">
                                <option value="Mobile App Developer">
                                <option value="System Analyst & SPBE">
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Target mitra yang diminati
                            </label>
                            <input type="text" 
                                   name="target_company" 
                                   value="{{ old('target_company') }}" 
                                   list="companyList"
                                   placeholder="Diskominfo, PT Telkom, dsb..." 
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                            <datalist id="companyList">
                                @foreach($companies as $comp)
                                    <option value="{{ $comp->name }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <!-- Jumlah Anggota (Khusus Butuh Anggota) -->
                    <div id="slotFieldWrapper" class="{{ $defaultType === 'cari_anggota' && $isLeader ? '' : 'hidden' }}">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jumlah slot anggota yang dicari
                        </label>
                        <input type="number" 
                               name="slots_needed" 
                               min="1" 
                               max="5" 
                               value="{{ old('slots_needed', $isLeader && $myActiveGroup ? max(1, $myActiveGroup->max_members - $myActiveGroup->activeMembers->count()) : 1) }}" 
                               class="w-32 text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Deskripsi & Kriteria -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Deskripsi kriteria / profil keahlian *
                        </label>
                        <textarea name="body" 
                                  rows="4" 
                                  required 
                                  placeholder="Jelaskan kebutuhan teknis, komitmen waktu kerja, atau perkenalkan portofolio keahlian Anda..." 
                                  class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-slate-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('body') }}</textarea>
                    </div>

                </div>

                <!-- Form Bottom Actions -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs text-slate-400">* Wajib diisi</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('forum.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                            Terbitkan postingan
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Kanan: WhatsApp & Etika Panduan (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- WhatsApp Notice Card -->
            <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-5 space-y-2">
                <div class="flex items-center gap-2 text-emerald-800 text-xs font-bold">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Konektivitas WhatsApp Otomatis</span>
                </div>
                <p class="text-xs text-emerald-900 leading-relaxed">
                    Nomor kontak Anda (<span class="font-mono font-semibold">{{ $currentUser->phone ?? '0812-3456-7890' }}</span>) akan diubah menjadi tautan direct chat WhatsApp dengan template pesan pembuka bagi rekan yang berminat.
                </p>
            </div>

            <!-- Panduan Etika Matchmaking -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Panduan postingan</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <h4 class="font-bold text-slate-900">Judul jelas & spesifik</h4>
                        <p class="text-slate-500 mt-0.5">Sebutkan peran atau target instansi yang jelas.</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <h4 class="font-bold text-slate-900">Cantumkan keterampilan</h4>
                        <p class="text-slate-500 mt-0.5">Jelaskan keahlian teknis (bahasa, framework, atau portofolio).</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <h4 class="font-bold text-slate-900">Perbarui status</h4>
                        <p class="text-slate-500 mt-0.5">Tandai postingan selesai setelah menemukan rekan tim.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- 3. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Nama dan kontak bersifat ilustratif.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>

@push('scripts')
<script>
    function toggleTypeFields(type) {
        const slotWrapper = document.getElementById('slotFieldWrapper');
        if (type === 'cari_anggota') {
            slotWrapper.classList.remove('hidden');
        } else {
            slotWrapper.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
