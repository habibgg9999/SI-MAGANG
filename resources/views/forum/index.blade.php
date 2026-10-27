@extends('layouts.app')

@section('title', 'Forum Matchmaking - Cari Tim & Rekan Magang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- HERO & MATCHMAKING HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-sky-50/60 border border-blue-100/80 p-6 sm:p-10 shadow-sm">
        <!-- Subtle Pattern Background -->
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100/90 text-blue-800 border border-blue-200/80 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 pulse-live"></span>
                    <span>Modul 4: Matchmaking & Direct WhatsApp</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">
                    Forum Pencarian Tim Magang
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Temukan rekan tim yang sevisi atau bergabunglah dengan kelompok impian. Diskusi berlangsung instan via tombol langsung <strong class="text-emerald-700 font-bold">WhatsApp</strong> terintegrasi anti-scraping.
                </p>
            </div>

            <!-- CTA BUAT POSTINGAN -->
            <div class="shrink-0">
                <a href="{{ route('forum.create') }}" 
                   class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Buat Postingan Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- TABS NAVIGASI UTAMA [CARI ANGGOTA] VS [CARI KELOMPOK] & FILTER -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80 space-y-4">
        
        <!-- Tab Pemisah Kategori -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <!-- Tab Semua -->
                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" 
                   class="px-4 py-2.5 rounded-full text-xs font-bold transition-all whitespace-nowrap {{ empty($filters['type']) ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Feed ({{ $totalPosts ?? 0 }})
                </a>

                <!-- Tab Butuh Anggota -->
                <a href="{{ request()->fullUrlWithQuery(['type' => 'cari_anggota']) }}" 
                   class="px-4 py-2.5 rounded-full text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ ($filters['type'] ?? '') === 'cari_anggota' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200' }}">
                    <span>👥</span> Butuh Anggota ({{ $totalCariAnggota ?? 0 }})
                </a>

                <!-- Tab Cari Kelompok -->
                <a href="{{ request()->fullUrlWithQuery(['type' => 'cari_kelompok']) }}" 
                   class="px-4 py-2.5 rounded-full text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ ($filters['type'] ?? '') === 'cari_kelompok' ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                    <span>🙋</span> Cari Kelompok ({{ $totalCariKelompok ?? 0 }})
                </a>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="text-slate-500 hidden sm:inline">Status:</span>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'open']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ ($filters['status'] ?? '') === 'open' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'text-slate-600 hover:bg-slate-100' }}">
                    Terbuka ({{ $totalOpen ?? 0 }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ ($filters['status'] ?? '') === 'all' ? 'bg-slate-200 text-slate-800' : 'text-slate-500 hover:bg-slate-100' }}">
                    Semua
                </a>
            </div>
        </div>

        <!-- Filter Baris 2: Search Input & Dropdown Bidang Kerja -->
        <form method="GET" action="{{ route('forum.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @if(!empty($filters['type']))
                <input type="hidden" name="type" value="{{ $filters['type'] }}">
            @endif
            @if(!empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif

            <!-- Search Keyword Input -->
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}" 
                       placeholder="Cari judul kriteria, target instansi, keahlian, atau nama mahasiswa..." 
                       class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 text-slate-800 rounded-2xl border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-hidden transition-all shadow-2xs">
            </div>

            <!-- Dropdown Filter Bidang Kerja -->
            <div class="flex items-center gap-2">
                <select name="field" class="w-full text-xs font-semibold bg-slate-50 text-slate-700 rounded-2xl px-3.5 py-2.5 border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Bidang Keahlian</option>
                    @foreach($recommendedFields as $f)
                        <option value="{{ $f }}" {{ ($filters['field'] ?? '') == $f ? 'selected' : '' }}>
                            {{ $f }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-2xl shadow-xs transition-colors shrink-0">
                    Filter
                </button>
            </div>
        </form>

    </div>

    <!-- FEED POSTINGAN FORUM -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
                @php
                    $isAuthor = $currentUser && $currentUser->id === $post->user_id;
                    $isCariAnggota = $post->type === 'cari_anggota';
                    $waUrl = $post->whatsapp_url;
                @endphp

                <!-- KARTU POSTINGAN MODERN -->
                <div class="bg-white rounded-3xl border {{ $post->is_fulfilled ? 'border-slate-200 opacity-80' : ($isCariAnggota ? 'border-indigo-200/90 hover:border-indigo-400' : 'border-emerald-200/90 hover:border-emerald-400') }} shadow-soft hover:shadow-card transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    
                    <div class="p-6 space-y-4">
                        
                        <!-- Header Kartu: Tipe Badge & Sisa Slot / Profil Solo -->
                        <div class="flex items-start justify-between gap-2">
                            @if($isCariAnggota)
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        👥 Butuh Anggota
                                    </span>
                                    @if($post->group)
                                        <p class="text-[11px] text-slate-500 font-semibold truncate max-w-[170px]" title="{{ $post->group->name }}">
                                            Tim: {{ $post->group->name }}
                                        </p>
                                    @endif
                                </div>

                                <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                    Sisa {{ $post->remaining_group_slots }} Slot
                                </span>
                            @else
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wide bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        🙋 Mahasiswa Solo
                                    </span>
                                    <p class="text-[11px] text-slate-500 font-semibold truncate max-w-[180px]">
                                        {{ $post->user?->prodi ?? 'Teknik Informatika' }}
                                    </p>
                                </div>

                                <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    Cari Tim
                                </span>
                            @endif
                        </div>

                        <!-- Judul Postingan -->
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 line-clamp-2 hover:text-emerald-700 transition-colors">
                                {{ $post->title }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1.5">
                                <span>Oleh <strong class="text-slate-800 font-semibold">{{ $post->user?->name ?? 'Mahasiswa' }}</strong></span>
                                <span>&bull;</span>
                                <span>{{ $post->created_at ? $post->created_at->diffForHumans() : 'Baru saja' }}</span>
                            </div>
                        </div>

                        <!-- Tag Bidang Kerja & Target Instansi -->
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            @if($post->field)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    💻 {{ $post->field }}
                                </span>
                            @endif

                            @if($post->target_company)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    🏢 {{ $post->target_company }}
                                </span>
                            @endif
                        </div>

                        <!-- Deskripsi Kriteria -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line line-clamp-4">
                            {{ $post->body }}
                        </div>

                        <!-- Status Terpenuhi Badge jika Closed -->
                        @if($post->is_fulfilled)
                            <div class="p-2.5 rounded-2xl bg-slate-100 text-center text-xs font-bold text-slate-500 border border-slate-200">
                                🔒 Matchmaking Telah Terpenuhi / Ditutup
                            </div>
                        @endif

                    </div>

                    <!-- FOOTER KARTU: TOMBOL WHATSAPP HIJAU GRADASI & KONTROL AUTHOR -->
                    <div class="p-6 pt-0 space-y-2.5">
                        
                        <!-- TOMBOL WHATSAPP HIJAU BERSIH -->
                        @if(!$post->is_fulfilled && $waUrl)
                            <a href="{{ $waUrl }}" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full inline-flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm hover:shadow transition-all group/wa">
                                <!-- WhatsApp Icon SVG -->
                                <svg class="w-4 h-4 fill-current transform group-hover/wa:rotate-12 transition-transform" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Hubungi via WhatsApp</span>
                            </a>
                        @elseif(!$post->is_fulfilled)
                            <div class="p-3 rounded-2xl bg-slate-100 text-center text-xs text-slate-500 font-medium">
                                📱 Login terlebih dahulu untuk menghubungi via WhatsApp
                            </div>
                        @endif

                        <!-- Kontrol Pemilik Postingan -->
                        @if($isAuthor || ($currentUser && $currentUser->isKoordinator()))
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                <form action="{{ route('forum.toggle', $post) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="font-bold text-slate-600 hover:text-slate-900 transition-colors">
                                        {{ $post->is_fulfilled ? '🔄 Buka Kembali' : '✓ Tandai Selesai' }}
                                    </button>
                                </form>

                                <form action="{{ route('forum.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus postingan ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-bold text-rose-600 hover:text-rose-800 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        <!-- ZERO-BUG POLISH: Pagination dengan Query String Preservation -->
        <div class="pt-6">
            {{ $posts->withQueryString()->links() }}
        </div>
    @else
        <!-- ZERO EMPTY FEED: Ramah & Call-to-Action -->
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-soft max-w-md mx-auto space-y-4">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Postingan</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Tidak ada postingan yang sesuai dengan filter yang dipilih. Jadilah yang pertama membuat postingan matchmaking pencarian tim!
                </p>
            </div>
            <a href="{{ route('forum.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Postingan Pertama</span>
            </a>
        </div>
    @endif

</div>
@endsection
