<?php

namespace App\States\Application;

use App\Exceptions\IllegalStateTransitionException;
use App\Models\ApplicationStatusLog;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

abstract class ApplicationState
{
    public function __construct(protected InternshipApplication $application)
    {
    }

    /**
     * Nama string state (e.g. 'draft', 'menunggu', 'under_review', 'diterima', 'ditolak', 'dibatalkan').
     */
    abstract public function name(): string;

    /**
     * Label manusiawi.
     */
    abstract public function label(): string;

    /**
     * Daftar target state yang diizinkan untuk berpindah dari state saat ini.
     *
     * @return array<string>
     */
    abstract public function allowedTransitions(): array;

    /**
     * Apakah state ini adalah terminal state (immutable).
     */
    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }

    /**
     * Validasi apakah target state diizinkan.
     */
    public function canTransitionTo(string $targetState): bool
    {
        return in_array($targetState, $this->allowedTransitions());
    }

    /**
     * Eksekusi transisi status dengan Cryptographic Tamper-Evident Audit Trail.
     *
     * @throws IllegalStateTransitionException
     */
    public function transitionTo(string $targetState, ?User $actor = null, ?string $reason = null): InternshipApplication
    {
        if (!$this->canTransitionTo($targetState)) {
            throw new IllegalStateTransitionException(
                fromState: $this->name(),
                toState: $targetState,
                applicationId: $this->application->id,
                actorId: $actor?->id
            );
        }

        $fromState = $this->name();
        $application = $this->application;

        return DB::transaction(function () use ($application, $fromState, $targetState, $actor, $reason) {
            // Update status & responded_at jika applicable
            $updateData = ['status' => $targetState];
            if (in_array($targetState, ['diterima', 'ditolak'])) {
                $updateData['responded_at'] = now();
            }
            $application->update($updateData);

            // Buat tamper-evident cryptographic log
            ApplicationStatusLog::recordMutation(
                application: $application,
                statusBefore: $fromState,
                statusAfter: $targetState,
                actor: $actor,
                reason: $reason ?? "Transisi status dari {$fromState} ke {$targetState} via State Machine Engine."
            );

            return $application;
        });
    }

    /**
     * Resolusi objek state konkret berdasarkan status permohonan.
     */
    public static function fromApplication(InternshipApplication $application): self
    {
        return match ($application->status) {
            'draft' => new DraftState($application),
            'under_review' => new UnderReviewState($application),
            'diterima' => new AcceptedState($application),
            'ditolak' => new RejectedState($application),
            'dibatalkan' => new WithdrawnState($application),
            default => new SubmittedState($application), // default 'menunggu'
        };
    }
}
