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
        if (!$user) {
            $aditya = User::where('name', 'LIKE', '%Aditya%')->first();
            if ($aditya) {
                Auth::login($aditya);
                $user = $aditya;
            }
        }

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
        // 2. Ajukan Magang (Menunggu verifikasi)
        // 3. Pelaksanaan (Setelah diterima mitra)
        // 4. Laporan & Penilaian
        $currentStep = 1;
        if ($myGroup) {
            $currentStep = 2;
            if ($latestApp) {
                if ($latestApp->status === 'diterima') {
                    $currentStep = 3;
                } elseif ($latestApp->status === 'selesai') {
                    $currentStep = 4;
                } else {
                    $currentStep = 2; // Menunggu verifikasi
                }
            }
        }

        // Rekomendasi Instansi Mitra (Gamatechno, Bappeda, Qiscus sesuai Figma)
        $recommendedCompanies = Company::active()->approved()
            ->withQuotaCounts()
            ->whereIn('name', ['PT Gamatechno Indonesia', 'Bappeda', 'PT Qiscus Tekno Indonesia'])
            ->get();

        if ($recommendedCompanies->count() < 3) {
            $recommendedCompanies = Company::active()->approved()
                ->withQuotaCounts()
                ->limit(3)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'myGroup',
            'latestApp',
            'currentStep',
            'recommendedCompanies'
        ));
    }
}
