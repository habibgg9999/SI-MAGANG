@extends('layouts.app')

@section('title', 'Buat Postingan Matchmaking Forum')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-6">

    <!-- BREADCRUMBS -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('forum.index') }}" class="hover:text-emerald-700 transition-colors">Forum Matchmaking</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold">Buat Postingan Baru</span>
    </nav>

    <!-- FORM CARD -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm space-y-8">
        
        <div class="border-b border-slate-100 pb-6 space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span>💬 Matchmaking Hub</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Publikasikan Postingan Pencarian Tim
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Koneksikan kebutuhan tim atau tawarkan keahlian Anda ke sesama mahasiswa SIMAGANG.
            </p>
        </div>

        <!-- WHATSAPP INFO NOTICE -->
        <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-emerald-900 text-xs space-y-1.5 flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
            </div>
            <div>
                <strong class="block font-bold">Konektivitas WhatsApp Otomatis:</strong>
                <p class="text-emerald-800 mt-0.5">
                    Nomor kontak Anda (<span class="font-mono font-semibold">{{ $currentUser->phone ?? 'Belum diisi' }}</span>) akan diubah otomatis menjadi tombol direct chat dengan pesan pembuka khusus bagi calon rekan tim yang tertarik.
                </p>
            </div>
        </div>

        <form action="{{ route('forum.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Pilihan Jenis Postingan -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-2">Pilih Jenis Postingan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <!-- Opsi: Cari Anggota -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition-all {{ $isLeader ? 'has-checked:border-indigo-600 has-checked:bg-indigo-50/50' : 'opacity-60 bg-slate-50 cursor-not-allowed' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                                <span>👥</span> Butuh Anggota
                            </span>
                            <input type="radio" 
                                   name="type" 
                                   value="cari_anggota" 
                                   {{ $defaultType === 'cari_anggota' && $isLeader ? 'checked' : '' }} 
                                   {{ !$isLeader ? 'disabled' : '' }}
                                   onchange="toggleTypeFields('cari_anggota')"
                                   class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">
                            Khusus Ketua Kelompok yang masih memiliki slot kuota kosong.
                        </p>
                        @if($isLeader)
                            <div class="mt-2 text-[11px] font-semibold text-indigo-700 bg-white p-2 rounded-lg border border-indigo-200/60">
                                Tim: {{ $myActiveGroup->name }} (Sisa {{ max(1, $myActiveGroup->max_members - $myActiveGroup->activeMembers->count()) }} slot)
                            </div>
                        @else
                            <div class="mt-2 text-[11px] text-slate-400 italic">
                                *Hanya untuk akun Ketua Kelompok.
                            </div>
                        @endif
                    </label>

                    <!-- Opsi: Cari Kelompok -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition-all has-checked:border-emerald-600 has-checked:bg-emerald-50/50">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                                <span>🙋</span> Cari Kelompok
                            </span>
                            <input type="radio" 
                                   name="type" 
                                   value="cari_kelompok" 
                                   {{ $defaultType === 'cari_kelompok' || !$isLeader ? 'checked' : '' }}
                                   onchange="toggleTypeFields('cari_kelompok')"
                                   class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">
                            Untuk mahasiswa mandiri yang sedang mencari kelompok magang.
                        </p>
                        <div class="mt-2 text-[11px] font-semibold text-emerald-800 bg-white p-2 rounded-lg border border-emerald-200/60">
                            Profil: {{ $currentUser->name }} ({{ $currentUser->prodi ?? 'Mahasiswa' }})
                        </div>
                    </label>

                </div>
                @error('type')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Judul Postingan -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Judul Postingan <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required 
                       placeholder="Contoh: Butuh 1 Frontend Engineer (React/Tailwind) untuk Magang di Telkom" 
                       class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-3 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-hidden @error('title') border-rose-400 @enderror">
                @error('title')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Bidang Kerja & Target Instansi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Bidang Kerja / Keahlian
                    </label>
                    <input type="text" 
                           name="field" 
                           value="{{ old('field') }}" 
                           list="fieldList"
                           placeholder="Contoh: Frontend Developer, Data Science, UI/UX..." 
                           class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-2.5 border border-slate-200 focus:border-emerald-500 focus:outline-hidden">
                    <datalist id="fieldList">
                        <option value="Frontend Developer">
                        <option value="Backend Developer">
                        <option value="Fullstack Web Developer">
                        <option value="UI/UX Designer">
                        <option value="Data Science & AI">
                        <option value="Cyber Security">
                        <option value="Mobile App Developer (Flutter/React Native)">
                        <option value="System Analyst & SPBE">
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Target Instansi yang Diminati
                    </label>
                    <input type="text" 
                           name="target_company" 
                           value="{{ old('target_company') }}" 
                           list="companyList"
                           placeholder="Contoh: PT Telkom Indonesia, Diskominfo, dll..." 
                           class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-2.5 border border-slate-200 focus:border-emerald-500 focus:outline-hidden">
                    <datalist id="companyList">
                        @foreach($companies as $comp)
                            <option value="{{ $comp->name }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- 4. Slot yang Dibutuhkan (Khusus cari_anggota) -->
            <div id="slotFieldWrapper" class="{{ $defaultType === 'cari_anggota' && $isLeader ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Jumlah Anggota yang Dicari
                </label>
                <input type="number" 
                       name="slots_needed" 
                       min="1" 
                       max="5" 
                       value="{{ old('slots_needed', $isLeader && $myActiveGroup ? max(1, $myActiveGroup->max_members - $myActiveGroup->activeMembers->count()) : 1) }}" 
                       class="w-32 text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl px-4 py-2.5 border border-slate-200 focus:border-emerald-500 focus:outline-hidden">
                <span class="text-xs text-slate-500 ml-2">Mahasiswa</span>
            </div>

            <!-- 5. Isi / Deskripsi Kriteria & Keahlian -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Deskripsi Kriteria / Profil Keahlian <span class="text-rose-500">*</span>
                </label>
                <textarea name="body" 
                          rows="5" 
                          required 
                          placeholder="Jelaskan secara detail: kriteria anggota yang dicari (kemampuan teknis, komitmen waktu), atau perkenalkan diri Anda (prodi, portofolio, minat projek)..." 
                          class="w-full text-sm bg-slate-50 focus:bg-white text-slate-900 rounded-xl p-4 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-hidden placeholder:text-slate-400 @error('body') border-rose-400 @enderror">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('forum.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/25 transition-all hover:scale-102 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span>Terbitkan Postingan Forum</span>
                </button>
            </div>

        </form>

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
