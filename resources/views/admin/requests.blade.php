@extends('layouts.app')

@section('title', 'Review Usulan Instansi Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Review Usulan Instansi Magang</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Verifikasi usulan mitra magang baru yang diajukan secara mandiri oleh mahasiswa.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 shadow-2xs transition-all">
                Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.companies.index') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl shadow-2xs transition-all">
                Daftar Instansi Resmi
            </a>
        </div>
    </div>

    <!-- STATUS TABS -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
        <a href="{{ route('admin.requests.index', ['status' => 'pending']) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-colors {{ $status === 'pending' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Menunggu Review</span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full {{ $status === 'pending' ? 'bg-purple-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countPending }}
            </span>
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'approved']) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-colors {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Disetujui</span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full {{ $status === 'approved' ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countApproved }}
            </span>
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'rejected']) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-colors {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>Ditolak</span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full {{ $status === 'rejected' ? 'bg-rose-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countRejected }}
            </span>
        </a>
    </div>

    <!-- REQUESTS LIST -->
    <div class="space-y-4">
        @forelse($requests as $req)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs hover:shadow-xs transition-all p-6 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="text-lg font-bold text-slate-900">{{ $req->name }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $req->type ?? 'Swasta' }}
                            </span>
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                    Menunggu Review
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    ✕ Ditolak
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1.5">
                            <span>📍 {{ $req->city ?? 'Kota belum diisi' }}</span>
                            <span>&bull;</span>
                            <span>Bidang: {{ $req->field ?? 'Umum' }}</span>
                            <span>&bull;</span>
                            <span>Estimasi Kuota: <strong>{{ $req->quota_estimate ?? 2 }} Mahasiswa</strong></span>
                            <span>&bull;</span>
                            <span>Diajukan: {{ $req->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <!-- PENGUSUL INFO -->
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs uppercase">
                            {{ substr($req->requestedBy->name ?? 'M', 0, 2) }}
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-800">{{ $req->requestedBy->name ?? 'Mahasiswa' }}</p>
                            <p class="text-slate-500">NIM: {{ $req->requestedBy->nim ?? '-' }} &bull; {{ $req->requestedBy->prodi ?? 'Informatika' }}</p>
                        </div>
                    </div>
                </div>

                <!-- DETAILS & REASON -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1.5 bg-slate-50/60 p-3.5 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700">Kontak & Alamat Instansi:</p>
                        <p class="text-slate-600">Alamat: <span class="text-slate-800">{{ $req->address ?? '-' }}</span></p>
                        <p class="text-slate-600">Person in Charge (PIC): <span class="font-semibold text-slate-800">{{ $req->contact_person ?? '-' }}</span></p>
                    </div>

                    <div class="space-y-1.5 bg-slate-50/60 p-3.5 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700">Alasan / Urgensi Usulan Mahasiswa:</p>
                        <p class="text-slate-600 italic">"{{ $req->reason ?? 'Tidak mencantumkan catatan tambahan.' }}"</p>
                    </div>
                </div>

                @if($req->status === 'rejected' && $req->reject_reason)
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                        <span class="font-bold">Alasan Penolakan Koordinator:</span>
                        <p class="mt-0.5">{{ $req->reject_reason }}</p>
                        <p class="text-[11px] text-rose-600 mt-1">Ditinjau oleh: {{ $req->reviewedBy->name ?? 'Koordinator' }} pada {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                @endif

                @if($req->status === 'approved')
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Usulan Telah Disetujui:</span>
                            <p class="mt-0.5">Instansi ini telah resmi tercatat di katalog Radar Instansi dengan kuota {{ $req->quota_estimate ?? 2 }} mahasiswa.</p>
                        </div>
                        <span class="text-[11px] text-emerald-700 shrink-0 font-medium">Ditinjau: {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '-' }}</span>
                    </div>
                @endif

                <!-- ACTIONS FOR PENDING -->
                @if($req->status === 'pending')
                    <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                        <!-- Tombol Buka Modal Reject -->
                        <button type="button" 
                                onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->name) }}')"
                                class="px-4 py-2 bg-white hover:bg-rose-50 text-rose-700 hover:text-rose-800 text-xs font-bold rounded-xl border border-rose-300 shadow-2xs transition-all">
                            ✕ Tolak Usulan
                        </button>

                        <!-- Tombol Approve Form -->
                        <form action="{{ route('admin.requests.approve', $req) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui usulan instansi \'{{ addslashes($req->name) }}\' dan memasukkannya ke katalog resmi?');">
                            @csrf
                            <button type="submit" 
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Setujui & Publikasikan ke Radar</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="font-bold text-slate-700 text-base">Tidak ada usulan instansi dengan status ini</p>
                <p class="text-xs text-slate-500 mt-1">Gunakan filter tab di atas untuk memeriksa status permohonan lainnya.</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $requests->links() }}
    </div>

</div>

<!-- REJECT MODAL WITH REASON INPUT -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-fade-in">
        <div class="p-6">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Tolak Usulan Instansi</h3>
            <p class="text-xs text-slate-500 mt-1">
                Berikan alasan penolakan untuk instansi: <span id="modalCompanyName" class="font-bold text-slate-800"></span>. Alasan ini akan dibaca oleh mahasiswa pengusul.
            </p>

            <form id="rejectForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="reject_reason" class="block text-xs font-bold text-slate-700 mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="reject_reason" 
                              name="reject_reason" 
                              rows="4" 
                              required
                              minlength="3"
                              maxlength="500"
                              placeholder="Contoh: Instansi belum memiliki legalitas resmi atau tidak sesuai dengan kurikulum program studi."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-rose-500 focus:outline-hidden"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 3 karakter, maksimal 500 karakter.</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            onclick="closeRejectModal()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        Konfirmasi Tolak Usulan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(requestId, companyName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const nameSpan = document.getElementById('modalCompanyName');
        const textarea = document.getElementById('reject_reason');

        form.action = `/admin/requests/${requestId}/reject`;
        nameSpan.innerText = companyName;
        textarea.value = '';

        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
    }
</script>
@endpush

@endsection
