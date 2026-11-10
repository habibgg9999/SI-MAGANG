@extends('layouts.app')

@section('title', 'Forum - Matchmaking Magang')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Forum Matchmaking
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Temukan rekan sevisi, wujudkan tim impian.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Cari anggota untuk melengkapi kelompok atau temukan tim magang yang sesuai minatmu.
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('forum.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0">
                <span>+ Buat postingan</span>
            </a>
        </div>
    </div>

    <!-- 2. SEARCH & FILTER CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        <form method="GET" action="{{ route('forum.index') }}" class="space-y-4">
            @if(!empty($filters['type']))
                <input type="hidden" name="type" value="{{ $filters['type'] }}">
            @endif

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <!-- Search Keyword -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Cari topik, keahlian, target mitra, atau nama mahasiswa..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Dropdown Bidang -->
                <div class="w-full sm:w-56 shrink-0">
                    <select name="field" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-700 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua bidang keahlian</option>
                        @foreach($recommendedFields as $f)
                            <option value="{{ $f }}" {{ ($filters['field'] ?? '') == $f ? 'selected' : '' }}>
                                {{ $f }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit Filter -->
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Filter Status & Kategori Pills -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ empty($filters['type']) ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                        Semua feed {{ $totalPosts ?? 0 }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'cari_anggota']) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ ($filters['type'] ?? '') === 'cari_anggota' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                        Butuh anggota {{ $totalCariAnggota ?? 0 }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'cari_kelompok']) }}" 
                       class="px-3.5 py-1 rounded-full text-xs font-semibold transition-all {{ ($filters['type'] ?? '') === 'cari_kelompok' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                        Cari kelompok {{ $totalCariKelompok ?? 0 }}
                    </a>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-400">Status:</span>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'open']) }}" 
                       class="font-semibold {{ ($filters['status'] ?? 'open') === 'open' ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        Terbuka ({{ $totalOpen ?? 0 }})
                    </a>
                    <span class="text-slate-300">•</span>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
                       class="font-semibold {{ ($filters['status'] ?? '') === 'all' ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        Semua
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 3. FEED POSTINGAN GRID -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($posts as $post)
                @php
                    $isAuthor = $currentUser && $currentUser->id === $post->user_id;
                    $isCariAnggota = $post->type === 'cari_anggota';
                    $waUrl = $post->whatsapp_url;
                    $uName = $post->user?->name ?? 'Mahasiswa';
                    $in = strtoupper(substr($uName, 0, 1));
                    $parts = explode(' ', $uName);
                    if (count($parts) > 1) {
                        $in .= strtoupper(substr($parts[1], 0, 1));
                    }
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group">
                    <div class="space-y-3.5">
                        <!-- Top Author & Badge Row -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $isCariAnggota ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }} font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ $in }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $uName }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $isCariAnggota && $post->group ? $post->group->name : ($post->user?->prodi ?? 'Informatika') }}
                                    </p>
                                </div>
                            </div>

                            @if($isCariAnggota)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">
                                    Butuh {{ $post->remaining_group_slots }} anggota
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                    Cari kelompok
                                </span>
                            @endif
                        </div>

                        <!-- Title & Time -->
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 leading-snug">
                                {{ $post->title }}
                            </h3>
                            <span class="text-[11px] text-slate-400 mt-1 block">
                                {{ $post->created_at ? $post->created_at->diffForHumans() : 'Baru saja' }}
                            </span>
                        </div>

                        <!-- Tags (Field & Target Company) -->
                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            @if($post->field)
                                <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $post->field }}
                                </span>
                            @endif
                            @if($post->target_company)
                                <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $post->target_company }}
                                </span>
                            @endif
                        </div>

                        <!-- Body Content -->
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            {{ $post->body }}
                        </p>
                    </div>

                    <!-- Footer & WhatsApp Button -->
                    <div class="pt-4 mt-4 border-t border-slate-100 space-y-2">
                        @if(!$post->is_fulfilled && $waUrl)
                            <a href="{{ $waUrl }}" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-bold text-white bg-[#059669] hover:bg-[#047857] shadow-xs transition-all">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Hubungi via WhatsApp</span>
                            </a>
                        @elseif($post->is_fulfilled)
                            <div class="py-2 px-3 rounded-xl bg-slate-100 text-center text-xs font-semibold text-slate-500">
                                Sudah terpenuhi
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                                Masuk untuk menghubungi
                            </a>
                        @endif

                        @if($isAuthor || ($currentUser && $currentUser->isKoordinator()))
                            <div class="flex items-center justify-between pt-1 text-[11px]">
                                <form action="{{ route('forum.toggle', $post) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="font-semibold text-slate-500 hover:text-slate-800">
                                        {{ $post->is_fulfilled ? 'Buka kembali' : 'Tandai selesai' }}
                                    </button>
                                </form>
                                <form action="{{ route('forum.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-rose-500 hover:text-rose-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200/80 text-xs text-slate-400">
            <div>
                Menampilkan {{ $posts->firstItem() ?? 0 }}–{{ $posts->lastItem() ?? 0 }} dari {{ $posts->total() }} postingan
            </div>
            <div>
                {{ $posts->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center max-w-md mx-auto space-y-3">
            <h3 class="font-bold text-slate-900 text-base">Belum ada postingan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Tidak ada postingan yang sesuai dengan filter. Jadilah yang pertama membuat postingan di forum!
            </p>
            <a href="{{ route('forum.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold transition-all">
                <span>+ Buat Postingan</span>
            </a>
        </div>
    @endif

    <!-- 4. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Nama dan kontak bersifat ilustratif.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>
@endsection
