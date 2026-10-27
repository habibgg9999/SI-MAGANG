<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Mahasiswa & Kelompok Magang (Bento-Grid & Stepper Timeline).
     */
    public function index(): View
    {
        $user = Auth::user();
        $myGroup = $user ? $user->currentActiveGroup() : null;

        if ($myGroup) {
            $myGroup->load([
                'leader',
                'activeMembers.user',
                'latestApplication.company',
                'latestApplication.statusLogs.changedBy',
            ]);
        }

        $latestApp = $myGroup ? $myGroup->latestApplication : null;

        // Hitung Stepper Progress Magang:
        // 1. Bentuk Kelompok
        // 2. Pilih Instansi
        // 3. Menunggu Balasan Instansi
        // 4. Diterima / Magang Berjalan
        $currentStep = 1;
        if ($myGroup) {
            $currentStep = 2;
            if ($latestApp) {
                if ($latestApp->status === 'menunggu' || $latestApp->status === 'under_review') {
                    $currentStep = 3;
                } elseif ($latestApp->status === 'diterima') {
                    $currentStep = 4;
                } elseif ($latestApp->status === 'ditolak' || $latestApp->status === 'dibatalkan') {
                    $currentStep = 2; // Kembali memilih instansi
                }
            }
        }

        // Rekomendasi Instansi Hijau (Radar Aman)
        $recommendedCompanies = Company::active()->approved()
            ->withQuotaCounts()
            ->limit(3)
            ->get();

        return view('dashboard', compact(
            'user',
            'myGroup',
            'latestApp',
            'currentStep',
            'recommendedCompanies'
        ));
    }
}
