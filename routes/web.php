<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DisasterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AlertPhotoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CitizenHomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAlertController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/alertes-publiques', [PublicAlertController::class, 'index'])->name('public.alerts.index');
Route::get('/alertes-publiques/{alert}', [PublicAlertController::class, 'show'])->name('public.alerts.show');
Route::get('/photos-alertes/{photo}', [AlertPhotoController::class, 'show'])->name('alert.photos.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/inscription', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store']);
    Route::get('/mot-de-passe-oublie', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetLinkController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/mon-espace', CitizenHomeController::class)
        ->middleware('role:citizen')
        ->name('citizen.home');

    Route::get('/tableau-de-bord', DashboardController::class)
        ->middleware('role:moderator,admin')
        ->name('dashboard');

    Route::resource('alertes', AlertController::class)
        ->parameters(['alertes' => 'alert'])
        ->names('alerts')
        ->middlewareFor('store', 'throttle:10,1');
    Route::delete('/alertes/{alert}/photos/{photo}', [AlertController::class, 'destroyPhoto'])->name('alerts.photos.destroy');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware('role:moderator,admin')->group(function (): void {
        Route::patch('/moderation/alertes/{alert}', [ModerationController::class, 'update'])->name('moderation.alerts.update');
    });

    Route::prefix('administration')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('catastrophes', DisasterController::class)->parameters(['catastrophes' => 'disaster'])->names('disasters')->only(['index', 'store', 'update', 'destroy']);
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::patch('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
});
