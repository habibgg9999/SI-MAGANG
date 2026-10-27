@extends('layouts.app')

@section('title', 'Dashboard Progres Magang Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-8">

    <!-- HERO HEADER & WELCOME BANNER (THE "CLEAN OXYGEN & KINETIC BLUE" ARCHITECTURE) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-50/90 via-indigo-50/40 to-white ring-1 ring-blue-100/80 p-6 sm:p-8 shadow-oxygen">
        <!-- Dot-matrix Micro Background (24px size) -->
        <div class="absolute inset-0 opacity-35 bg-[radial-gradient(#93c5fd_0.8px,transparent_0.8px)] [background-size:20px_20px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2.5">
                <!-- Real-time Student Status & Portal Super-Title -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-100/90 text-blue-800 border border-blue-200/80 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-blue-600 pulse-live"></span>
                        <span class="uppercase tracking-wider text-[11px]">Portal Magang Mahasiswa</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white ring-1 ring-slate-200/80 text-slate-600 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="tabular-nums">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Akun Aktif</span>
                    </span>
                </div>

                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 leading-tight">
                    Halo, {{ $user?->name ?? 'Mahasiswa' }}! 👋
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600 max-w-xl leading-relaxed">
                    Pantau progres tahapan magang, koordinasi kelompok, serta status pengajuan berkas ke instansi tujuan Anda secara realtime dan transparan.
                </p>
            </div>

            <!-- Quick Action Header (Tactile Pill Buttons) -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('companies.index') }}" class="tactile-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-300/90 shadow-oxygen transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Radar Kuota</span>
                </a>
                <a href="{{ route('forum.index') }}" class="tactile-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all duration-150 ease-out hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Cari Rekan Tim</span>
                </a>
            </div>
        </div>
    </div>

    <!-- STEPPER TIMELINE STATUS MAGANG (MODERN LINEAR PIPELINE) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-oxygen border border-slate-200/70 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 flex-wrap gap-2">
            <div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Tahapan Progres Magang</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5">Pipeline status pendaftaran dari pembentukan tim hingga penerimaan resmi</p>
            </div>
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 ring-1 ring-blue-200/80 shadow-2xs tabular-nums">
                Tahap {{ $currentStep }} dari 4
            </span>
        </div>

        <!-- Horizontal Stepper Pipeline (Responsive with scroll on mobile) -->
        <div class="overflow-x-auto pb-2">
            <div class="min-w-[680px] grid grid-cols-4 gap-4 relative">
                
                <!-- Connector Pipeline Track Behind Steps -->
                <div class="absolute top-5 left-12 right-12 h-1.5 bg-slate-100 rounded-full -z-0">
                    <div class="h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 rounded-full transition-all duration-700 shadow-sm" 
                         style="width: {{ $currentStep === 1 ? '12%' : ($currentStep === 2 ? '42%' : ($currentStep === 3 ? '75%' : '100%')) }};"></div>
                </div>

                <!-- Step 1: Bentuk Kelompok -->
                <div class="relative z-10 flex flex-col items-center text-center space-y-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all {{ $myGroup ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md shadow-blue-500/20' : ($currentStep === 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-lg shadow-blue-500/25 animate-pulse' : 'bg-white text-slate-400 border-2 border-slate-200') }}">
                        @if($myGroup)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            1
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">1. Bentuk Kelompok</p>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate max-w-[140px]">
                            {{ $myGroup ? $myGroup->name : 'Belum bergabung' }}
                        </p>
                    </div>
                </div>

                <!-- Step 2: Pilih Instansi -->
                <div class="relative z-10 flex flex-col items-center text-center space-y-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all {{ $latestApp ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md shadow-blue-500/20' : ($currentStep === 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-lg shadow-blue-500/25 animate-pulse' : 'bg-white text-slate-400 border-2 border-slate-200') }}">
                        @if($latestApp)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            2
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">2. Pilih Instansi</p>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate max-w-[140px]">
                            {{ $latestApp ? $latestApp->company?->name : 'Pilih di Radar' }}
                        </p>
                    </div>
                </div>

                <!-- Step 3: Verifikasi Instansi -->
                <div class="relative z-10 flex flex-col items-center text-center space-y-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all {{ ($latestApp && $latestApp->status === 'diterima') ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md shadow-blue-500/20' : ($currentStep === 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-lg shadow-blue-500/25' : 'bg-white text-slate-400 border-2 border-slate-200') }}">
                        @if($latestApp && $latestApp->status === 'diterima')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($currentStep === 3)
                            <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                        @else
                            3
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">3. Verifikasi Instansi</p>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">
                            @if($latestApp && $latestApp->status === 'menunggu')
                                Sedang Diproses
                            @elseif($latestApp && $latestApp->status === 'diterima')
                                Selesai Diverifikasi
                            @else
                                Menunggu Pengajuan
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Step 4: Siap Berangkat -->
                <div class="relative z-10 flex flex-col items-center text-center space-y-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-xs transition-all {{ $currentStep === 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 shadow-md shadow-emerald-500/20' : 'bg-white text-slate-400 border-2 border-slate-200' }}">
                        @if($currentStep === 4)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            4
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">4. Siap Berangkat</p>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">
                            {{ $currentStep === 4 ? 'Resmi Diterima 🎉' : 'Tahap Akhir' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- BENTO-GRID DASHBOARD STATS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CARD 1: STATUS PERMOHONAN MAGANG AKTIF -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Permohonan</span>
                    
                    @if($latestApp)
                        @php
                            $statusConfig = [
                                'menunggu' => ['label' => 'Menunggu Balasan', 'class' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'],
                                'diterima' => ['label' => 'Diterima Resmi', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                'ditolak'  => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-800 border-rose-200', 'dot' => 'bg-rose-500'],
                                'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
                            ];
                            $badge = $statusConfig[$latestApp->status] ?? $statusConfig['menunggu'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['class'] }}">
                            <span class="w-2 h-2 rounded-full {{ $badge['dot'] }} animate-pulse"></span>
                            <span>{{ $badge['label'] }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            Draft / Belum Mengajukan
                        </span>
                    @endif
                </div>

                @if($latestApp && $latestApp->company)
                    <div class="mt-4 space-y-3">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                {{ $latestApp->company->name }}
                            </h3>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5 mt-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>{{ $latestApp->company->city ?? 'Lokasi belum diatur' }} &bull; {{ $latestApp->company->type }}</span>
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-800">
                                    {{ $latestApp->applied_at ? \Carbon\Carbon::parse($latestApp->applied_at)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Tenggat Reminder:</span>
                                <span class="font-bold text-slate-800">
                                    {{ $latestApp->deadline_reminder ? \Carbon\Carbon::parse($latestApp->deadline_reminder)->translatedFormat('d M Y') : '14 Hari' }}
                                </span>
                            </div>
                            @if($latestApp->notes)
                                <div class="pt-2 border-t border-slate-200/60">
                                    <span class="block text-slate-400 text-[10px] uppercase font-bold">Catatan Proposal:</span>
                                    <p class="text-slate-700 italic mt-0.5 line-clamp-2">{{ $latestApp->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Belum Ada Pengajuan Magang</p>
                        <p class="text-[11px] text-slate-500">Pilih instansi mitra yang memiliki kuota longgar di Radar Kuota.</p>
                    </div>
                @endif
            </div>

            @if($latestApp && $latestApp->company)
                <a href="{{ route('companies.show', $latestApp->company) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm">
                    <span>Lihat Rincian Instansi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <a href="{{ route('companies.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm">
                    <span>Eksplor Radar Instansi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endif
        </div>

        <!-- CARD 2 & 3: INFORMASI KELOMPOK AKTIF & DAFTAR ANGGOTA (AVATAR STACK + WA) -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 lg:col-span-2 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Kelompok Saya</span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">
                            {{ $myGroup ? $myGroup->name : 'Belum Memiliki Kelompok' }}
                        </h3>
                    </div>

                    @if($myGroup)
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $myGroup->is_open ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $myGroup->is_open ? '🔓 Open Recruitment' : '🔒 Tertutup' }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $myGroup->activeMembers->count() }} / {{ $myGroup->max_members }} Anggota
                            </span>
                        </div>
                    @endif
                </div>

                @if($myGroup)
                    <!-- Daftar Anggota Kelompok dengan Avatar Stack & Kontak WA -->
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                            <span>Susunan Mahasiswa Aktif:</span>
                            <span>Ketua: <strong class="text-slate-800">{{ $myGroup->leader?->name ?? 'Belum Ditentukan' }}</strong></span>
                        </div>

                        <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                            @foreach($myGroup->activeMembers as $member)
                                @php
                                    $mUser = $member->user;
                                    $isLeader = $mUser && $mUser->id === $myGroup->leader_id;
                                    $cleanWaUrl = ($mUser && $mUser->phone) 
                                        ? \App\Helpers\WhatsappHelper::generateLink($mUser->phone, $mUser->name, "Koordinasi Magang Kelompok {$myGroup->name}")
                                        : null;
                                @endphp
                                <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar with Initials -->
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs {{ $isLeader ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-200 text-slate-700' }}">
                                            {{ substr($mUser?->name ?? 'M', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-slate-800">{{ $mUser?->name ?? 'Mahasiswa' }}</span>
                                                @if($isLeader)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-800">Ketua</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-500">
                                                NIM: {{ $mUser?->nim ?? '-' }} &bull; {{ $mUser?->prodi ?? 'Teknik Informatika' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Tombol Kontak WhatsApp Anggota -->
                                    <div>
                                        @if($cleanWaUrl)
                                            <a href="{{ $cleanWaUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-2xs">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>WhatsApp</span>
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">Kontak Privasi</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- ZERO EMPTY STATE: Belum punya kelompok -->
                    <div class="py-8 text-center space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Anda Belum Tergabung dalam Kelompok</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Pendaftaran magang ke instansi mitra memerlukan kelompok aktif. Buat kelompok baru atau cari rekan tim di forum matchmaking.
                            </p>
                        </div>
                        <div class="pt-2 flex items-center justify-center gap-3">
                            <a href="{{ route('groups.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                                + Buat Kelompok Sekarang
                            </a>
                            <a href="{{ route('forum.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                Cari Tim di Forum
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($myGroup)
                <div class="pt-2 flex items-center justify-between border-t border-slate-100 text-xs">
                    <span class="text-slate-500">Kelola anggota & pengajuan proposal tim</span>
                    <a href="{{ route('groups.show', $myGroup) }}" class="font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <span>Buka Manajemen Kelompok</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endif
        </div>

    </div>

    <!-- REKOMENDASI INSTANSI RADAR KUOTA AMAN -->
    @if(isset($recommendedCompanies) && $recommendedCompanies->count() > 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Rekomendasi Instansi (Radar Hijau)</h3>
                    <p class="text-xs text-slate-500">Instansi mitra terverifikasi dengan ketersediaan kuota masih sangat longgar</p>
                </div>
                <a href="{{ route('companies.index', ['status' => 'green']) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($recommendedCompanies as $comp)
                    @php
                        $st = $comp->quota_status;
                    @endphp
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 hover:shadow-card transition-all flex flex-col justify-between space-y-3">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $comp->type }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $st['badge_bg'] }}">
                                    {{ $st['icon'] }} {{ $st['label'] }} ({{ $comp->quota_percentage }}%)
                                </span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 line-clamp-1">
                                <a href="{{ route('companies.show', $comp) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $comp->name }}
                                </a>
                            </h4>
                            <p class="text-xs text-slate-500 truncate">{{ $comp->city ?? 'Indonesia' }} &bull; {{ $comp->field ?? 'TI' }}</p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Sisa Slot: <strong class="text-emerald-600">{{ $comp->remaining_quota }} Mahasiswa</strong></span>
                            <a href="{{ route('companies.show', $comp) }}" class="font-bold text-blue-600 hover:text-blue-800">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
