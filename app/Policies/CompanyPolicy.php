<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Siapa saja boleh melihat katalog instansi aktif & disetujui.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Detail instansi hanya boleh dilihat jika aktif & approved, kecuali koordinator.
     */
    public function view(?User $user, Company $company): bool
    {
        if ($user && $user->isKoordinator()) {
            return true;
        }

        return $company->is_active && $company->is_approved;
    }

    /**
     * Hanya koordinator yang boleh membuat instansi resmi langsung.
     */
    public function create(User $user): bool
    {
        return $user->isKoordinator();
    }

    /**
     * Hanya koordinator yang boleh mengupdate data & kuota instansi.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->isKoordinator();
    }

    /**
     * Hanya koordinator yang boleh menghapus instansi.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->isKoordinator();
    }
}
