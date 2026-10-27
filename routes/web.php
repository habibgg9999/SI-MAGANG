<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

// Redirect root to radar instansi
Route::get('/', [CompanyController::class, 'index'])->name('home');
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Radar Kuota Instansi (Modul 2)
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
    Route::post('/{company}/apply', [ApplicationController::class, 'store'])
        ->middleware(['idempotent', 'throttle:application-submission'])
        ->name('apply');
});

// Manajemen Kelompok & Anggota (Modul 3)
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('index');
    Route::get('/create', [GroupController::class, 'create'])->name('create');
    Route::post('/', [GroupController::class, 'store'])
        ->middleware('throttle:group-creation')
        ->name('store');
    Route::get('/{group}', [GroupController::class, 'show'])->name('show');
    Route::post('/{group}/members', [GroupController::class, 'addMember'])->name('members.add');
    Route::delete('/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('members.remove');
    Route::post('/{group}/join', [GroupController::class, 'join'])->name('join');
    Route::post('/{group}/toggle-open', [GroupController::class, 'toggleOpen'])->name('toggle-open');
});

// Manajemen Pengajuan Magang & Update Status (Modul 3)
Route::prefix('applications')->name('applications.')->group(function () {
    Route::post('/', [ApplicationController::class, 'store'])
        ->middleware(['idempotent', 'throttle:application-submission'])
        ->name('store');
    Route::patch('/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->middleware('idempotent')
        ->name('update-status');
});

// Forum Pencarian Tim / Matchmaking & WhatsApp Link (Modul 4)
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/create', [ForumController::class, 'create'])->name('create');
    Route::post('/', [ForumController::class, 'store'])
        ->middleware('throttle:forum-post')
        ->name('store');
    Route::patch('/{post}/toggle', [ForumController::class, 'toggleFulfilled'])->name('toggle');
    Route::delete('/{post}', [ForumController::class, 'destroy'])->name('destroy');
});

// Simulator Switch User (untuk mempermudah testing Ketua Kelompok, Anggota, & Koordinator)
Route::post('/dev-switch-user', [CompanyController::class, 'switchUser'])->name('dev.switch-user');

// Modul 5: Dashboard Koordinator (Admin Kampus)
Route::prefix('admin')->name('admin.')->middleware(['koordinator'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'dashboard']);
    Route::get('/export-csv', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'exportCsv'])->name('export.csv');
    
    // Review Usulan Instansi Mahasiswa
    Route::get('/requests', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'reviewRequests'])->name('requests.index');
    Route::post('/requests/{companyRequest}/approve', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{companyRequest}/reject', [\App\Http\Controllers\Admin\AdminCoordinatorController::class, 'rejectRequest'])->name('requests.reject');

    // CRUD Instansi & Penetapan Kuota
    Route::get('/companies', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/companies/{company}/toggle-active', [\App\Http\Controllers\Admin\AdminCompanyController::class, 'toggleActive'])->name('companies.toggle-active');
});
