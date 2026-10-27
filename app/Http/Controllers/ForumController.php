<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ForumPost;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Tampilkan feed forum matchmaking pencarian tim magang.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search'),
            'type'   => $request->query('type'), // cari_anggota, cari_kelompok, or null (semua)
            'field'  => $request->query('field'),
            'status' => $request->query('status', 'open'), // default tampilkan yang masih open
        ];

        $query = ForumPost::query()
            ->with(['user', 'group.activeMembers', 'group.leader'])
            ->filter($filters)
            ->latest();

        $posts = $query->paginate(9)->withQueryString();

        // Statistik Counter Forum
        $allPosts = ForumPost::all();
        $totalPosts = $allPosts->count();
        $totalCariAnggota = $allPosts->where('type', 'cari_anggota')->count();
        $totalCariKelompok = $allPosts->where('type', 'cari_kelompok')->count();
        $totalOpen = $allPosts->where('is_fulfilled', false)->count();

        // Daftar bidang kerja untuk dropdown filter
        $availableFields = ForumPost::whereNotNull('field')
            ->distinct()
            ->pluck('field')
            ->filter()
            ->sort()
            ->values();

        // Tambahkan opsi rekomendasi bidang jika data masih sedikit
        $recommendedFields = collect([
            'Frontend Developer',
            'Backend Developer',
            'UI/UX Designer',
            'Data Science & AI',
            'Cyber Security',
            'Mobile App Developer',
            'Fullstack Web Developer',
            'System Analyst & SPBE',
        ])->merge($availableFields)->unique()->values();

        // Data pengguna saat ini & status kelompok
        $currentUser = Auth::user();
        $myActiveGroup = $currentUser ? $currentUser->currentActiveGroup() : null;
        $isLeader = $currentUser && $myActiveGroup && $myActiveGroup->leader_id === $currentUser->id;

        // Daftar instansi aktif untuk rekomendasi target instansi
        $companies = Company::active()->approved()->orderBy('name')->get(['id', 'name', 'field', 'city']);

        return view('forum.index', compact(
            'posts',
            'filters',
            'totalPosts',
            'totalCariAnggota',
            'totalCariKelompok',
            'totalOpen',
            'recommendedFields',
            'currentUser',
            'myActiveGroup',
            'isLeader',
            'companies'
        ));
    }

    /**
     * Halaman form pembuatan postingan baru.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return redirect()->route('forum.index')
                ->with('error', 'Silakan masuk/login terlebih dahulu untuk membuat postingan forum.');
        }

        $myActiveGroup = $currentUser->currentActiveGroup();
        $isLeader = $myActiveGroup && $myActiveGroup->leader_id === $currentUser->id;

        // Tentukan default type
        $defaultType = $request->query('type');
        if (!$defaultType) {
            $defaultType = $isLeader ? 'cari_anggota' : 'cari_kelompok';
        }

        $companies = Company::active()->approved()->orderBy('name')->get(['id', 'name', 'field', 'city']);

        return view('forum.create', compact('currentUser', 'myActiveGroup', 'isLeader', 'defaultType', 'companies'));
    }

    /**
     * Simpan postingan baru ke forum.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('forum.index')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:cari_anggota,cari_kelompok'],
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:3000'],
            'field' => ['nullable', 'string', 'max:100'],
            'target_company' => ['nullable', 'string', 'max:150'],
            'slots_needed' => ['nullable', 'integer', 'min:1', 'max:10'],
        ], [
            'type.in' => 'Jenis postingan harus cari_anggota atau cari_kelompok.',
            'title.required' => 'Judul postingan wajib diisi.',
            'body.required' => 'Deskripsi kriteria / profil keahlian wajib diisi.',
            'body.min' => 'Deskripsi minimal 10 karakter.',
        ]);

        $groupId = null;
        $slots = $validated['slots_needed'] ?? 1;

        if ($validated['type'] === 'cari_anggota') {
            // VALIDASI: Tipe cari_anggota dibuat oleh ketua kelompok yang slot anggotanya belum penuh
            $myGroup = $user->currentActiveGroup();
            if (!$myGroup || $myGroup->leader_id !== $user->id) {
                return back()->withInput()->with('error', 'Postingan "Cari Anggota" hanya dapat dibuat oleh Ketua Kelompok yang aktif.');
            }

            if ($myGroup->isFull()) {
                return back()->withInput()->with('error', "Kelompok Anda ({$myGroup->name}) sudah mencapai kapasitas maksimal ({$myGroup->max_members} anggota).");
            }

            $groupId = $myGroup->id;
            $remainingSlots = $myGroup->max_members - $myGroup->activeMembers()->count();
            $slots = min($slots, max(1, $remainingSlots));
        } else {
            // Tipe cari_kelompok dibuat oleh mahasiswa solo/mandiri
            $groupId = null;
            $slots = 1;
        }

        ForumPost::create([
            'user_id' => $user->id,
            'group_id' => $groupId,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'target_company' => $validated['target_company'] ?? null,
            'field' => $validated['field'] ?? null,
            'slots_needed' => $slots,
            'is_fulfilled' => false,
        ]);

        $label = $validated['type'] === 'cari_anggota' ? 'Pencarian Anggota' : 'Pencarian Kelompok';
        return redirect()->route('forum.index')
            ->with('success', "Postingan {$label} \"{$validated['title']}\" berhasil dipublikasikan ke Forum Matchmaking!");
    }

    /**
     * Tandai postingan sebagai terpenuhi / buka kembali.
     */
    public function toggleFulfilled(ForumPost $post): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->id !== $post->user_id && !$user->isKoordinator())) {
            return back()->with('error', 'Anda tidak memiliki hak untuk mengubah status postingan ini.');
        }

        $post->update(['is_fulfilled' => !$post->is_fulfilled]);
        $statusStr = $post->is_fulfilled ? 'TERPENUHI (Selesai)' : 'DIBUKA KEMBALI (Aktif)';

        return back()->with('success', "Status postingan berhasil diubah menjadi: {$statusStr}.");
    }

    /**
     * Hapus postingan forum.
     */
    public function destroy(ForumPost $post): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->id !== $post->user_id && !$user->isKoordinator())) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menghapus postingan ini.');
        }

        $post->delete();
        return back()->with('success', 'Postingan forum berhasil dihapus.');
    }
}
