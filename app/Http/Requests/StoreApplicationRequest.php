<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreApplicationRequest extends FormRequest
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

        $groupId = $this->input('group_id');
        if (!$groupId) {
            return true; // Let validation handle missing group_id
        }

        $group = Group::find($groupId);
        if (!$group) {
            return true;
        }

        // Celah 2: Otorisasi ketat - Hanya Ketua Kelompok (leader_id) yang berhak mengajukan
        return $user->id === $group->leader_id;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Support company passed from route parameter
        if ($this->route('company') && !$this->has('company_id')) {
            $company = $this->route('company');
            $this->merge([
                'company_id' => $company instanceof Company ? $company->id : $company,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'exists:groups,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'confirmed_red_warning' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Additional business logic validation (Zero-Loopholes).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $groupId = $this->input('group_id');
            $companyId = $this->input('company_id');

            if (!$groupId || !$companyId) {
                return;
            }

            $group = Group::with('activeMembers')->find($groupId);
            $company = Company::find($companyId);

            if (!$group || !$company) {
                return;
            }

            // Celah 6: Pendaftaran ke Instansi Draft / Non-Aktif / Belum Approved
            if (!$company->is_active || !$company->is_approved) {
                $validator->errors()->add(
                    'company_id',
                    'Instansi ini tidak ditemukan, sedang tidak aktif, atau belum disetujui oleh Koordinator Kampus.'
                );
            }

            // Celah 4: Pendaftaran Ganda - Cek apakah kelompok memiliki pengajuan 'menunggu'
            $hasPending = InternshipApplication::where('group_id', $group->id)
                ->where('status', 'menunggu')
                ->exists();

            if ($hasPending) {
                $validator->errors()->add(
                    'group_id',
                    'Kelompok Anda masih memiliki permohonan magang yang berstatus MENUNGGU. Tidak dapat mendaftar ke instansi lain.'
                );
            }

            // Celah 5: Kelompok sudah berstatus diterima di instansi lain
            $hasAccepted = InternshipApplication::where('group_id', $group->id)
                ->where('status', 'diterima')
                ->exists();

            if ($hasAccepted) {
                $validator->errors()->add(
                    'group_id',
                    'Kelompok Anda sudah berstatus DITERIMA di instansi lain.'
                );
            }

            // Pastikan kelompok memiliki anggota aktif
            if ($group->activeMembers->isEmpty()) {
                $validator->errors()->add(
                    'group_id',
                    'Kelompok Anda belum memiliki anggota aktif. Tambahkan anggota sebelum mengajukan pendaftaran.'
                );
            }
        });
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'group_id.required' => 'Kelompok pengusul wajib dipilih.',
            'group_id.exists' => 'Data kelompok tidak valid.',
            'company_id.required' => 'Instansi tujuan magang wajib dipilih.',
            'company_id.exists' => 'Data instansi tidak ditemukan.',
        ];
    }
}
