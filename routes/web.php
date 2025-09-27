<?php

use App\Livewire\Dashboard;
use App\Livewire\RoleManagement;
use App\Livewire\UserManagement;
use App\Livewire\Settings\Profile;
use App\Livewire\ProductManagement;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // User management routes
    Route::get('/users', UserManagement::class)
        ->middleware('permission:user.view')
        ->name('users.index');

    // Product management routes
    Route::get('/products', ProductManagement::class)
        ->middleware('permission:product.view')
        ->name('products.index');

        Route::get('/roles', RoleManagement::class)
        ->middleware('permission:role.view')
        ->name('roles.index');
});

require __DIR__.'/auth.php';
