<?php

namespace App\Http\Controllers;

use App\Exceptions\IllegalStateTransitionException;
use App\Exceptions\QuotaExceededException;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use App\Services\ApplicationSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationSubmissionService $submissionService
    ) {
    }

    /**
     * Ajukan pendaftaran magang kelompok ke suatu instansi.
     * Terlindungi dari Concurrency, Double Submission via Distributed Cache Lock, DB Transaction Retry, dan Idempotency Engine.
     */
    public function store(StoreApplicationRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        $idempotencyKey = $request->header('X-Idempotency-Key');

        $application = $this->submissionService->submitApplication(
            groupId: $validated['group_id'],
            companyId: $validated['company_id'],
            notes: $validated['notes'] ?? null,
            actor: $user,
            idempotencyKey: $idempotencyKey
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Permohonan magang berhasil diajukan.',
                'data' => $application,
            ], 201);
        }

        $company = Company::find($validated['company_id']);
        $group = Group::find($validated['group_id']);

        $fallbackRedirect = $request->route('company') 
            ? redirect()->route('companies.show', $validated['company_id']) 
            : redirect()->route('groups.show', $validated['group_id']);

        $successMsg = "Permohonan magang kelompok '{$group->name}' ke {$company->name} berhasil dikirim! Status: MENUNGGU.";
        if ($company->quota_status['level'] === 'red') {
            $successMsg .= " (Catatan: Instansi berstatus Merah, konfirmasi seleksi mungkin memerlukan waktu ekstra).";
        }

        return $fallbackRedirect->with('success', $successMsg);
    }

    /**
     * Update status pengajuan magang ('menunggu' -> 'diterima' / 'ditolak' / 'dibatalkan').
     * Dilindungi Finite State Machine (FSM), Pessimistic Quota Guard, Cryptographic Audit Trail, dan Idempotency Engine.
     */
    public function updateStatus(UpdateApplicationStatusRequest $request, InternshipApplication $application): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        $newStatus = $validated['status'];
        $reason = $validated['reason'];
        $idempotencyKey = $request->header('X-Idempotency-Key');

        $updatedApp = $this->submissionService->updateApplicationStatus(
            application: $application,
            targetStatus: $newStatus,
            reason: $reason,
            actor: $user,
            idempotencyKey: $idempotencyKey
        );

        $statusLabels = [
            'diterima' => 'DITERIMA',
            'ditolak' => 'DITOLAK',
            'dibatalkan' => 'DIBATALKAN',
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Status pengajuan berhasil diubah menjadi: {$statusLabels[$newStatus]}.",
                'data' => $updatedApp,
            ]);
        }

        return back()->with('success', "Status pengajuan magang ke {$application->company->name} berhasil diubah menjadi: {$statusLabels[$newStatus]}. Riwayat status telah dicatat secara tamper-evident.");
    }
}
