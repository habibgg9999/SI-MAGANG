<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCoordinatorController extends Controller
{
    /**
     * Dashboard Utama Koordinator Magang Kampus.
     */
    public function dashboard(): View
    {
        // 1. STATISTIK UTAMA: Total mahasiswa vs mahasiswa yang sudah dapat tempat magang
        $totalStudents = User::where('role', 'mahasiswa')->count();

        // Mahasiswa yang tergabung dalam kelompok berstatus 'diterima' pada suatu instansi
        $placedStudents = GroupMember::where('status', 'aktif')
            ->whereIn('group_id', function ($query) {
                $query->select('group_id')
                      ->from('internship_applications')
                      ->where('status', 'diterima');
            })
            ->distinct('user_id')
            ->count('user_id');

        $unplacedStudents = max(0, $totalStudents - $placedStudents);
        $placementPercentage = $totalStudents > 0 ? round(($placedStudents / $totalStudents) * 100, 1) : 0;

        // Total kelompok
        $totalGroups = Group::active()->count();
        $placedGroups = Group::active()->whereHas('applications', fn($q) => $q->where('status', 'diterima'))->count();
        $pendingGroups = Group::active()->whereHas('applications', fn($q) => $q->where('status', 'menunggu'))->count();

        // 2. PERSEBARAN MAHASISWA PER INSTANSI
        $companyDistribution = Company::active()
            ->approved()
            ->withQuotaCounts()
            ->orderByDesc('accepted_members_count')
            ->get();

        // 3. DAFTAR KELOMPOK DENGAN PENGAJUAN MENGGANTUNG (> 14 hari berstatus 'menunggu')
        $hangingDeadline = Carbon::now()->subDays(14);
        $hangingApplications = InternshipApplication::where('status', 'menunggu')
            ->where('applied_at', '<', $hangingDeadline)
            ->with(['group.leader', 'group.activeMembers.user', 'company'])
            ->orderBy('applied_at', 'asc')
            ->get();

        // Jumlah usulan instansi yang pending review
        $pendingRequestsCount = CompanyRequest::pending()->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'placedStudents',
            'unplacedStudents',
            'placementPercentage',
            'totalGroups',
            'placedGroups',
            'pendingGroups',
            'companyDistribution',
            'hangingApplications',
            'pendingRequestsCount'
        ));
    }

    /**
     * Halaman review usulan instansi baru dari mahasiswa (company_requests).
     */
    public function reviewRequests(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $query = CompanyRequest::query()->with(['requestedBy', 'reviewedBy'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(10)->withQueryString();

        $countPending = CompanyRequest::pending()->count();
        $countApproved = CompanyRequest::approved()->count();
        $countRejected = CompanyRequest::rejected()->count();

        return view('admin.requests', compact(
            'requests',
            'status',
            'countPending',
            'countApproved',
            'countRejected'
        ));
    }

    /**
     * Setujui usulan instansi mahasiswa & otomatis jadikan Instansi resmi di katalog.
     */
    public function approveRequest(CompanyRequest $companyRequest): RedirectResponse
    {
        if ($companyRequest->status !== 'pending') {
            return back()->with('error', 'Usulan instansi ini sudah pernah ditinjau sebelumnya.');
        }

        DB::transaction(function () use ($companyRequest) {
            $companyRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Otomatis masukkan ke katalog companies aktif
            Company::create([
                'name' => $companyRequest->name,
                'address' => $companyRequest->address,
                'type' => $companyRequest->type ?? 'swasta',
                'field' => $companyRequest->field,
                'city' => $companyRequest->city,
                'quota' => $companyRequest->quota_estimate ?? 2,
                'contact_person' => $companyRequest->contact_person,
                'contact_phone' => null,
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => $companyRequest->requested_by,
            ]);
        });

        return back()->with('success', "Usulan instansi '{$companyRequest->name}' berhasil disetujui dan telah masuk ke katalog instansi aktif!");
    }

    /**
     * Tolak usulan instansi mahasiswa dengan input alasan penolakan.
     */
    public function rejectRequest(Request $request, CompanyRequest $companyRequest): RedirectResponse
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi.',
            'reject_reason.min' => 'Alasan penolakan minimal 3 karakter.',
        ]);

        $companyRequest->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Usulan instansi '{$companyRequest->name}' ditolak. Alasan telah dicatat dan dapat dilihat mahasiswa.");
    }

    /**
     * Ekspor rekap distribusi mahasiswa ke format CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $filename = 'rekap_distribusi_magang_' . date('Y-m-d_His') . '.csv';

        // Ambil seluruh mahasiswa beserta kelompok dan status pengajuan magang mereka
        $students = User::where('role', 'mahasiswa')
            ->with([
                'activeGroups.latestApplication.company',
                'activeGroups.leader',
            ])
            ->orderBy('nim')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM untuk kompatibilitas Microsoft Excel Windows
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Baris Header CSV
            fputcsv($handle, [
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Email',
                'Nomor Telepon',
                'Program Studi',
                'Angkatan',
                'Status Kelompok',
                'Nama Kelompok',
                'Peran Kelompok',
                'Nama Instansi Magang',
                'Tipe Instansi',
                'Kota Instansi',
                'Status Magang',
                'Tanggal Diajukan/Diterima',
            ], ';');

            $no = 1;
            foreach ($students as $mhs) {
                $group = $mhs->activeGroups->first();
                $application = $group ? $group->latestApplication : null;
                $company = $application ? $application->company : null;

                $statusMagang = 'Belum Mendaftar';
                $tglMagang = '-';

                if ($application) {
                    $statusMagang = strtoupper($application->status);
                    $tglMagang = $application->responded_at 
                        ? $application->responded_at->format('d/m/Y') 
                        : ($application->applied_at ? $application->applied_at->format('d/m/Y') : '-');
                }

                $peranKelompok = '-';
                if ($group) {
                    $peranKelompok = $group->leader_id === $mhs->id ? 'Ketua Kelompok' : 'Anggota';
                }

                fputcsv($handle, [
                    $no++,
                    $mhs->nim ?? '-',
                    $mhs->name,
                    $mhs->email,
                    $mhs->phone ?? '-',
                    $mhs->prodi ?? '-',
                    $mhs->angkatan ?? '-',
                    $group ? 'Tergabung' : 'Belum Ada Kelompok',
                    $group ? $group->name : '-',
                    $peranKelompok,
                    $company ? $company->name : '-',
                    $company ? strtoupper($company->type) : '-',
                    $company ? $company->city : '-',
                    $statusMagang,
                    $tglMagang,
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
