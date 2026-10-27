<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IllegalStateTransitionException extends Exception
{
    public function __construct(
        public string $fromState,
        public string $toState,
        public int $applicationId,
        public ?int $actorId = null,
        string $message = "",
        int $code = 422
    ) {
        if (empty($message)) {
            $message = "Transisi status ilegal! Tidak diperbolehkan mengubah status dari '{$fromState}' ke '{$toState}' pada permohonan magang #{$applicationId}.";
        }

        // Catat Security Alert saat transisi ilegal terdeteksi
        Log::alert("SECURITY ALERT [TAMPER_ATTEMPT]: Percobaan manipulasi status dari '{$fromState}' ke '{$toState}' pada Aplikasi #{$applicationId} oleh User #" . ($actorId ?? 'Guest') . " IP: " . request()->ip());

        parent::__construct($message, $code);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Illegal State Transition',
                'message' => $this->getMessage(),
                'from_state' => $this->fromState,
                'to_state' => $this->toState,
            ], 422);
        }

        return back()->withErrors(['status' => $this->getMessage()]);
    }
}
