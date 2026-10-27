<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Tampilkan radar kuota instansi (katalog & pencarian).
     */
    public function index(Request $request): View
    {
        // 1. Ambil data instansi aktif dan sudah disetujui koordinator
        $query = Company::query()
            ->active()
            ->approved()
            ->withQuotaCounts();

        // 2. Filter & Pencarian
        $filters = [
            'search' => $request->query('search'),
            'type'   => $request->query('type'),
            'city'   => $request->query('city'),
            'field'  => $request->query('field'),
            'status' => $request->query('status'),
            'sort'   => $request->query('sort', 'quota_asc'),
        ];

        $query->filter($filters);

        // Subquery SQL untuk kalkulasi anggota diterima (kompatibel dengan paginate count query)
        $acceptedSub = '(SELECT COUNT(gm.id) FROM group_members gm JOIN internship_applications ia ON ia.group_id = gm.group_id WHERE ia.company_id = companies.id AND ia.status = \'diterima\' AND gm.status = \'aktif\')';

        // Filter Traffic Light status
        if (!empty($filters['status']) && in_array($filters['status'], ['green', 'yellow', 'red'])) {
            if ($filters['status'] === 'green') {
                $query->whereRaw("({$acceptedSub} * 1.0 / NULLIF(quota, 0)) < 0.5");
            } elseif ($filters['status'] === 'yellow') {
                $query->whereRaw("({$acceptedSub} * 1.0 / NULLIF(quota, 0)) >= 0.5 AND ({$acceptedSub} * 1.0 / NULLIF(quota, 0)) < 0.9");
            } elseif ($filters['status'] === 'red') {
                $query->whereRaw("({$acceptedSub} * 1.0 / NULLIF(quota, 0)) >= 0.9 OR quota <= 0");
            }
        }

        // Sorting
        switch ($filters['sort']) {
            case 'quota_desc':
                // Persentase terisi tertinggi (paling penuh)
                $query->orderByRaw("({$acceptedSub} * 1.0 / NULLIF(quota, 0)) DESC");
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'quota_count_desc':
                $query->orderBy('quota', 'desc');
                break;
            case 'quota_asc':
            default:
                // Persentase terisi terendah (paling banyak peluang)
                $query->orderByRaw("({$acceptedSub} * 1.0 / NULLIF(quota, 0)) ASC")
                      ->orderBy('quota', 'desc');
                break;
        }

        $companies = $query->paginate(9)->withQueryString();

        // 3. Statistik Ringkasan Radar untuk Header Dashboard
        $allActiveCompanies = Company::active()->approved()->withQuotaCounts()->get();
        $totalCompanies = $allActiveCompanies->count();
        $totalQuota = $allActiveCompanies->sum('quota');
        $totalAcceptedMembers = $allActiveCompanies->sum('accepted_members_count');
        $totalPendingMembers = $allActiveCompanies->sum('pending_members_count');

        $greenCount = $allActiveCompanies->filter(fn($c) => $c->quota_status['level'] === 'green')->count();
        $yellowCount = $allActiveCompanies->filter(fn($c) => $c->quota_status['level'] === 'yellow')->count();
        $redCount = $allActiveCompanies->filter(fn($c) => $c->quota_status['level'] === 'red')->count();

        // 4. Opsi filter dropdown
        $cities = Company::active()->approved()->whereNotNull('city')->distinct()->pluck('city')->sort()->values();
        $fields = Company::active()->approved()->whereNotNull('field')->distinct()->pluck('field')->sort()->values();
        $types = ['pemerintah', 'swasta', 'BUMN', 'NGO'];

        // 5. Data user yang sedang login & kelompok yang dipimpin (untuk opsi pendaftaran cepat)
        $currentUser = Auth::user();
        $ledGroups = $currentUser ? $currentUser->ledGroups()->where('is_active', true)->with('activeMembers')->get() : collect();

        return view('companies.index', compact(
            'companies',
            'filters',
            'totalCompanies',
            'totalQuota',
            'totalAcceptedMembers',
            'totalPendingMembers',
            'greenCount',
            'yellowCount',
            'redCount',
            'cities',
            'fields',
            'types',
            'currentUser',
            'ledGroups'
        ));
    }

    /**
     * Tampilkan detail instansi dan daftar kelompok pelamar.
     */
    public function show(Company $company): View
    {
        abort_if(!$company->is_active || !$company->is_approved, 404, 'Instansi tidak ditemukan atau belum aktif.');

        // Eager load riwayat pengajuan beserta kelompok, anggota kelompok, dan status logs
        $company->load([
            'applications' => function ($q) {
                $q->with([
                    'group.leader',
                    'group.activeMembers.user',
                    'statusLogs.changedBy' => fn($q) => $q->select('id', 'name', 'role'),
                ])->latest('applied_at');
            },
            'requestedBy',
        ]);

        // Pisahkan daftar kelompok berdasarkan status
        $acceptedApplications = $company->applications->where('status', 'diterima');
        $pendingApplications = $company->applications->where('status', 'menunggu');
        $otherApplications = $company->applications->whereIn('status', ['ditolak', 'dibatalkan']);

        // Data ketua kelompok & izin pengajuan
        $currentUser = Auth::user();
        $ledGroups = $currentUser ? $currentUser->ledGroups()->where('is_active', true)->with('activeMembers')->get() : collect();

        // Cek apakah kelompok yang dipimpin sudah pernah mendaftar di instansi ini
        $existingApplication = null;
        if ($currentUser && $ledGroups->isNotEmpty()) {
            $existingApplication = InternshipApplication::whereIn('group_id', $ledGroups->pluck('id'))
                ->where('company_id', $company->id)
                ->whereIn('status', ['menunggu', 'diterima'])
                ->first();
        }

        return view('companies.show', compact(
            'company',
            'acceptedApplications',
            'pendingApplications',
            'otherApplications',
            'currentUser',
            'ledGroups',
            'existingApplication'
        ));
    }

    /**
     * Proses pengajuan pendaftaran magang bagi ketua kelompok.
     */
    public function apply(Request $request, Company $company): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('companies.show', $company)
                ->with('error', 'Anda harus masuk/login terlebih dahulu untuk mengajukan pendaftaran magang.');
        }

        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $group = Group::with('activeMembers')->findOrFail($validated['group_id']);

        // Validasi: Hanya ketua kelompok yang boleh mengajukan
        if ($group->leader_id !== $user->id) {
            return redirect()->route('companies.show', $company)
                ->with('error', 'Hanya ketua kelompok yang berhak mengajukan pendaftaran magang.');
        }

        // Validasi: Pastikan kelompok memiliki anggota aktif
        if ($group->activeMembers->isEmpty()) {
            return redirect()->route('companies.show', $company)
                ->with('error', 'Kelompok Anda belum memiliki anggota aktif. Tambahkan anggota terlebih dahulu.');
        }

        // Validasi: Cek apakah kelompok sudah memiliki pengajuan aktif di instansi ini
        $existing = InternshipApplication::where('group_id', $group->id)
            ->where('company_id', $company->id)
            ->whereIn('status', ['menunggu', 'diterima'])
            ->first();

        if ($existing) {
            $statusLabel = $existing->status === 'diterima' ? 'DITERIMA' : 'MENUNGGU KONFIRMASI';
            return redirect()->route('companies.show', $company)
                ->with('error', "Kelompok Anda sudah memiliki permohonan magang dengan status '{$statusLabel}' di instansi ini.");
        }

        // Transaksi pendaftaran magang & log histori
        DB::transaction(function () use ($company, $group, $validated, $user) {
            $application = InternshipApplication::create([
                'group_id' => $group->id,
                'company_id' => $company->id,
                'status' => 'menunggu',
                'applied_at' => now(),
                'responded_at' => null,
                'notes' => $validated['notes'] ?? null,
                'deadline_reminder' => now()->addDays(14),
            ]);

            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'status_before' => null,
                'status_after' => 'menunggu',
                'changed_by' => $user->id,
                'reason' => 'Pengajuan proposal pendaftaran magang awal oleh ketua kelompok ' . $group->name,
                'changed_at' => now(),
            ]);
        });

        return redirect()->route('companies.show', $company)
            ->with('success', "Permohonan magang untuk kelompok '{$group->name}' berhasil dikirimkan ke {$company->name}. Status: Menunggu Konfirmasi.");
    }

    /**
     * Switch User Simulator untuk mempermudah testing & demonstrasi berbagai role.
     */
    public function switchUser(Request $request): RedirectResponse
    {
        $userId = $request->input('user_id');

        if ($userId === 'guest' || empty($userId)) {
            Auth::logout();
            return back()->with('info', 'Anda sekarang berada dalam mode Tamu (Guest/Belum Login).');
        }

        $user = User::find($userId);
        if ($user) {
            Auth::login($user);
            $roleLabel = $user->role === 'koordinator' ? 'Koordinator Magang' : 'Mahasiswa (' . ($user->ledGroups()->exists() ? 'Ketua Kelompok' : 'Anggota') . ')';
            return back()->with('success', "Berhasil beralih akun sebagai: {$user->name} [{$roleLabel}].");
        }

        return back()->with('error', 'User tidak ditemukan.');
    }
}
