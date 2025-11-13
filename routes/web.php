<?php

use App\Livewire\Dashboard;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
// Category & Product removed
use App\Livewire\SatuanManagement;
use App\Livewire\ProposalManagement;
use App\Livewire\AnnualBudgetManagement;
use App\Livewire\ApprovalManagement;
use App\Livewire\PengumumanManagement;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\PendingActivation;
use App\Http\Controllers\ProposalPdfController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserIsActive::class])
    ->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsActive::class])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // User management routes
    Route::get('/users', UserManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':user.view')
        ->name('users.index');

    // Product & Category routes removed

    // Satuan management routes
    Route::get('/satuans', SatuanManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':satuan.view')
        ->name('satuans.index');

    // Proposal management routes
    Route::get('/proposals', ProposalManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':proposal.view')
        ->name('proposals.index');
    Route::get('/proposals/{proposal}/pdf', [ProposalPdfController::class, 'download'])
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':proposal.view')
        ->name('proposals.pdf');
    Route::get('/proposals/pdf/batch', [ProposalPdfController::class, 'downloadBatch'])
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':proposal.view')
        ->name('proposals.pdf.batch');

    // Annual Budget management routes
    Route::get('/annual-budgets', AnnualBudgetManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':annualbudget.view')
        ->name('annualbudgets.index');

    // Approval management routes
    Route::get('/approvals', ApprovalManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':approval.view')
        ->name('approvals.index');

    // Pengumuman management routes
    Route::get('/pengumuman', PengumumanManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':pengumuman.view')
        ->name('pengumuman.index');

        Route::get('/roles', RoleManagement::class)
        ->middleware(\App\Http\Middleware\CheckPermission::class . ':role.view')
        ->name('roles.index');
});

// Pending activation page
Route::get('/activation/pending', PendingActivation::class)
    ->middleware(['auth'])
    ->name('activation.pending');

require __DIR__.'/auth.php';
