@extends('layouts.app')

@section('title', 'Tinjau Pengajuan Mitra - Panel Koordinator')

@section('content')
<div class="px-6 sm:px-8 pt-6 space-y-6 max-w-7xl">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600">
                Panel Koordinator
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Tinjau usulan mitra baru.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Verifikasi mitra yang diajukan mandiri oleh mahasiswa sebelum masuk ke katalog resmi.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-2xs transition-all">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.companies.index') }}" 
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold shadow-sm transition-all">
                <span>Kelola Mitra Resmi</span>
            </a>
        </div>
    </div>

    <!-- 2. STATUS FILTER TABS -->
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.requests.index', ['status' => 'pending']) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $status === 'pending' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
            <span>Menunggu review</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $status === 'pending' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countPending }}
            </span>
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'approved']) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
            <span>Disetujui</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countApproved }}
            </span>
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'rejected']) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $status === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countRejected }}
            </span>
        </a>
    </div>

    <!-- 3. REQUEST CARDS LIST -->
    <div class="space-y-4">
        @forelse($requests as $req)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="text-lg font-bold text-slate-900">{{ $req->name }}</h2>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                {{ $req->type ?? 'Swasta' }}
                            </span>
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Menunggu review</span>
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span>✓ Disetujui</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span>✕ Ditolak</span>
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mt-1">
                            <span>{{ $req->city ?? 'Kota belum diisi' }}</span>
                            <span>•</span>
                            <span>Bidang: {{ $req->field ?? 'Sistem informasi' }}</span>
                            <span>•</span>
                            <span>Estimasi kuota: <strong>{{ $req->quota_estimate ?? 2 }} mahasiswa</strong></span>
                            <span>•</span>
                            <span>Diajukan: {{ $req->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Pengusul Info -->
                    <div class="bg-slate-50 rounded-xl px-3.5 py-2.5 border border-slate-100 flex items-center gap-3 shrink-0">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($req->requestedBy->name ?? 'M', 0, 2)) }}
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-800">{{ $req->requestedBy->name ?? 'Mahasiswa' }}</p>
                            <p class="text-[11px] text-slate-400">NIM: {{ $req->requestedBy->nim ?? '22.11.4821' }} • {{ $req->requestedBy->prodi ?? 'Informatika' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detail & Urgensi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                        <span class="font-bold text-slate-800 block">Kontak & Alamat Instansi:</span>
                        <p class="text-slate-600">Alamat: <span class="text-slate-800 font-medium">{{ $req->address ?? '-' }}</span></p>
                        <p class="text-slate-600">PIC / Kontak: <span class="text-slate-800 font-medium">{{ $req->contact_person ?? '-' }}</span></p>
                    </div>

                    <div class="space-y-1 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                        <span class="font-bold text-slate-800 block">Urgensi & Alasan Pengusulan:</span>
                        <p class="text-slate-600 italic">"{{ $req->reason ?? 'Tidak mencantumkan catatan tambahan.' }}"</p>
                    </div>
                </div>

                @if($req->status === 'rejected' && $req->reject_reason)
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                        <span class="font-bold">Alasan Penolakan Koordinator:</span>
                        <p class="mt-0.5">{{ $req->reject_reason }}</p>
                    </div>
                @endif

                @if($req->status === 'approved')
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Usulan Telah Disetujui:</span>
                            <p class="mt-0.5">Mitra ini telah aktif tercatat di katalog Radar Kuota dengan kuota {{ $req->quota_estimate ?? 2 }} mahasiswa.</p>
                        </div>
                        <span class="text-[11px] text-emerald-700 shrink-0">Ditinjau: {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '-' }}</span>
                    </div>
                @endif

                <!-- Aksi Khusus Pending -->
                @if($req->status === 'pending')
                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" 
                                onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->name) }}')"
                                class="px-4 py-2 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-xl border border-rose-200 transition-colors">
                            Tolak usulan
                        </button>

                        <form action="{{ route('admin.requests.approve', $req) }}" method="POST" onsubmit="return confirm('Setujui usulan instansi ini ke katalog resmi?');">
                            @csrf
                            <button type="submit" 
                                    class="px-5 py-2 bg-[#4338ca] hover:bg-[#3730a3] text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                Setujui & Tambah ke Radar
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400">
                <p class="font-bold text-slate-700 text-sm">Tidak ada usulan mitra pada status ini</p>
                <p class="text-xs text-slate-400 mt-1">Gunakan filter tab di atas untuk memeriksa status lainnya.</p>
            </div>
        @endforelse
    </div>

    <!-- 4. PAGINATION -->
    <div class="pt-2">
        {{ $requests->links() }}
    </div>

    <!-- 5. FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 text-xs text-slate-400 font-normal">
        <div>Data contoh • Verifikasi dilakukan oleh koordinator magang.</div>
        <div>14 Nov 2026 • 09.00 WIB</div>
    </div>

</div>

<!-- REJECT MODAL -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 p-6 space-y-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Tolak Usulan Mitra</h3>
            <p class="text-xs text-slate-500 mt-1">
                Berikan alasan penolakan untuk: <span id="modalCompanyName" class="font-bold text-slate-800"></span>
            </p>
        </div>

        <form id="rejectForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="reject_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                    Alasan penolakan *
                </label>
                <textarea id="reject_reason" 
                          name="reject_reason" 
                          rows="4" 
                          required
                          minlength="3"
                          maxlength="500"
                          placeholder="Contoh: Belum memenuhi legalitas perusahaan atau tidak sesuai bidang prodi..."
                          class="w-full text-xs rounded-xl border border-slate-200 p-3 bg-slate-50 text-slate-800 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" 
                        onclick="closeRejectModal()" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                    Tolak usulan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(requestId, companyName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin/requests/${requestId}/reject`;
        document.getElementById('modalCompanyName').innerText = companyName;
        document.getElementById('reject_reason').value = '';
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
