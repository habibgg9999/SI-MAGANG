<?php

namespace App\Http\Requests;

use App\Models\InternshipApplication;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        /** @var InternshipApplication|null $application */
        $application = $this->route('application');
        if (!$application) {
            return false;
        }

        // Celah 2: Otorisasi ketat - Hanya Ketua Kelompok bersangkutan atau Koordinator
        return $user->isKoordinator() || $user->id === $application->group->leader_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:diterima,ditolak,dibatalkan'],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    /**
     * Business logic validation: State Machine transitions (Celah 8).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var InternshipApplication|null $application */
            $application = $this->route('application');
            $newStatus = $this->input('status');

            if (!$application || !$newStatus) {
                return;
            }

            // Celah 8: State Machine Jump
            // Transisi sah: 'menunggu' -> 'diterima' / 'ditolak' / 'dibatalkan'
            // 'diterima' / 'ditolak' / 'dibatalkan' adalah state final.
            if ($application->status !== 'menunggu') {
                $validator->errors()->add(
                    'status',
                    "Lompatan status tidak sah. Pengajuan yang sudah berstatus '{$application->status}' tidak dapat diubah kembali menjadi '{$newStatus}'."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status pengajuan wajib dipilih.',
            'status.in' => 'Status pengajuan harus berupa diterima, ditolak, atau dibatalkan.',
            'reason.required' => 'Alasan perubahan status wajib diisi.',
            'reason.min' => 'Alasan perubahan status minimal 3 karakter.',
            'reason.max' => 'Alasan perubahan status maksimal 500 karakter.',
        ];
    }
}
