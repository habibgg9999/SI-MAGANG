<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuotaExceededException extends Exception
{
    public function __construct(
        public int $companyId,
        public int $quota,
        public int $attemptedCount,
        string $message = "",
        int $code = 422
    ) {
        if (empty($message)) {
            $message = "Kuota instansi #{$companyId} telah penuh atau tidak mencukupi untuk menampung {$attemptedCount} mahasiswa (Kapasitas maksimal: {$quota} slot).";
        }

        Log::warning("CONCURRENCY RACE BLOCKED: Quota exceeded for Company #{$companyId}. Attempted: {$attemptedCount}, Quota: {$quota}");

        parent::__construct($message, $code);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Quota Exceeded',
                'message' => $this->getMessage(),
                'company_id' => $this->companyId,
                'quota' => $this->quota,
            ], 422);
        }

        return back()->withErrors(['status' => $this->getMessage()]);
    }
}
