<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    /**
     * Dashboard daftar kelompok magang.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $myGroup = $currentUser ? $currentUser->currentActiveGroup() : null;

        $search = $request->query('search');
        $filterStatus = $request->query('status'); // open, full

        $query = Group::query()
            ->active()
            ->with(['leader', 'activeMembers.user', 'latestApplication.company']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('leader', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterStatus === 'open') {
            $query->where('is_open', true);
        }

        $groups = $query->latest()->paginate(9)->withQueryString();

        return view('groups.index', compact('groups', 'myGroup', 'currentUser', 'search', 'filterStatus'));
    }

    /**
     * Form buat kelompok baru.
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('groups.index')
                ->with('error', 'Silakan masuk/login terlebih dahulu untuk membuat kelompok.');
        }

        // VALIDASI: Mahasiswa yang sudah tergabung dalam kelompok aktif TIDAK BISA membuat kelompok baru
        if ($user->hasActiveGroup()) {
            $activeGroup = $user->currentActiveGroup();
            return redirect()->route('groups.show', $activeGroup)
                ->with('error', "Anda sudah tergabung dalam kelompok aktif '{$activeGroup->name}'. Mahasiswa tidak dapat membuat kelompok baru sebelum keluar dari kelompok saat ini.");
        }

        return view('groups.create', compact('user'));
    }

    /**
     * Simpan kelompok baru dan jadikan pembuat sebagai Ketua Kelompok aktif.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('groups.index')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // VALIDASI: Mahasiswa yang sudah tergabung dalam kelompok aktif TIDAK BISA membuat kelompok
        if ($user->hasActiveGroup()) {
            return redirect()->route('groups.index')
                ->with('error', 'Mahasiswa yang sudah tergabung dalam kelompok aktif tidak dapat membuat kelompok baru.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:groups,name'],
            'max_members' => ['required', 'integer', 'min:2', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.unique' => 'Nama kelompok sudah digunakan, silakan pilih nama kelompok lain.',
            'max_members.min' => 'Batas minimal anggota adalah 2 mahasiswa.',
            'max_members.max' => 'Batas maksimal anggota adalah 10 mahasiswa.',
        ]);

        $group = DB::transaction(function () use ($validated, $user) {
            $group = Group::create([
                'name' => $validated['name'],
                'leader_id' => $user->id,
                'max_members' => $validated['max_members'] ?? 5,
                'is_open' => true,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            // DDD Pilar 3: Lewati Aggregate Root - controller dilarang langsung memanggil GroupMember::create()
            \App\Domain\GroupAggregate::for($group)->addMember($user);

            return $group;
        });

        return redirect()->route('groups.show', $group)
            ->with('success', "Kelompok '{$group->name}' berhasil dibuat! Anda otomatis terdaftar sebagai Ketua Kelompok.");
    }

    /**
     * Dashboard detail kelompok & manajemen anggota.
     */
    public function show(Group $group): View
    {
        $group->load([
            'leader',
            'activeMembers.user',
            'members.user',
            'applications' => function ($q) {
                $q->with([
                    'company',
                    'statusLogs.changedBy' => fn($q) => $q->select('id', 'name', 'role'),
                ])->latest('applied_at');
            },
        ]);

        $currentUser = Auth::user();
        $isLeader = $currentUser && $currentUser->id === $group->leader_id;
        $isMember = $currentUser && $group->activeMembers->contains('user_id', $currentUser->id);
        $canJoin = $currentUser && !$currentUser->hasActiveGroup() && $group->canAcceptMoreMembers();

        // Ambil daftar mahasiswa yang belum memiliki kelompok aktif untuk dropdown tambah anggota
        $availableStudents = collect();
        if ($isLeader && !$group->isFull()) {
            $assignedUserIds = GroupMember::where('status', 'aktif')->pluck('user_id');
            $availableStudents = User::where('role', 'mahasiswa')
                ->whereNotIn('id', $assignedUserIds)
                ->orderBy('name')
                ->get(['id', 'name', 'nim', 'prodi', 'email']);
        }

        // Cek pengajuan yang aktif
        $pendingApp = $group->pendingApplication();
        $acceptedApp = $group->acceptedApplication();

        return view('groups.show', compact(
            'group',
            'currentUser',
            'isLeader',
            'isMember',
            'canJoin',
            'availableStudents',
            'pendingApp',
            'acceptedApp'
        ));
    }

    /**
     * Tambah anggota ke dalam kelompok (Khusus Ketua Kelompok).
     * Dilindungi DDD Aggregate Root dari Ghost Quota & Phantom Member.
     */
    public function addMember(Request $request, Group $group): RedirectResponse
    {
        $currentUser = Auth::user();

        // Validasi otorisasi ketua
        if (!$currentUser || $currentUser->id !== $group->leader_id) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Hanya Ketua Kelompok yang berhak menambahkan anggota.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $targetUser = User::findOrFail($validated['user_id']);
            \App\Domain\GroupAggregate::for($group)->addMember($targetUser);

            return redirect()->route('groups.show', $group)
                ->with('success', "Mahasiswa '{$targetUser->name}' berhasil ditambahkan ke kelompok {$group->name}.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->route('groups.show', $group)
                ->withErrors($e->errors())
                ->with('error', $firstError);
        }
    }

    /**
     * Keluarkan anggota dari kelompok (Oleh Ketua atau Keluar Mandiri).
     * Dilindungi dari rusaknya kuota instansi yang sudah diterima (Ghost Quota) via Aggregate.
     */
    public function removeMember(Group $group, User $user): RedirectResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return redirect()->route('groups.show', $group)->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            \App\Domain\GroupAggregate::for($group)->removeMember($user, $currentUser);
            $isSelf = $currentUser->id === $user->id;
            $msg = $isSelf 
                ? "Anda telah berhasil keluar dari kelompok {$group->name}." 
                : "Anggota '{$user->name}' telah dikeluarkan dari kelompok.";

            return redirect()->route('groups.show', $group)->with('success', $msg);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->route('groups.show', $group)
                ->withErrors($e->errors())
                ->with('error', $firstError);
        }
    }

    /**
     * Gabung mandiri ke kelompok yang membuka pendaftaran (is_open = true).
     * Dilindungi Pessimistic Locking & Domain Invariant via GroupAggregate.
     */
    public function join(Group $group): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('groups.show', $group)->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            \App\Domain\GroupAggregate::for($group)->addMember($user, requireOpen: true);

            return redirect()->route('groups.show', $group)
                ->with('success', "Selamat! Anda berhasil bergabung ke kelompok {$group->name}.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->route('groups.show', $group)
                ->withErrors($e->errors())
                ->with('error', $firstError);
        }
    }

    /**
     * Buka / Tutup pendaftaran anggota baru (Khusus Ketua).
     */
    public function toggleOpen(Group $group): RedirectResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->id !== $group->leader_id) {
            return redirect()->route('groups.show', $group)
                ->with('error', 'Hanya Ketua Kelompok yang dapat mengatur status pendaftaran anggota.');
        }

        $group->update(['is_open' => !$group->is_open]);
        $statusStr = $group->is_open ? 'TERBUKA untuk anggota baru' : 'DITUTUP untuk anggota baru';

        return redirect()->route('groups.show', $group)
            ->with('success', "Status rekrutmen kelompok diubah menjadi: {$statusStr}.");
    }
}
