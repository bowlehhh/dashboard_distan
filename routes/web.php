<?php

use App\Http\Controllers\AlsintanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PoktanController;
use App\Http\Controllers\PublicInformationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaprodiController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Models\Alsintan;
use App\Models\Crop;
use App\Models\Poktan;
use App\Models\Saprodi;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', ['stats' => [
        ['value' => Poktan::count(), 'label' => 'Kelompok Tani'],
        ['value' => Alsintan::count(), 'label' => 'Unit Alsintan'],
        ['value' => Saprodi::count(), 'label' => 'Jenis Saprodi'],
        ['value' => Crop::distinct('commodity')->count('commodity'), 'label' => 'Komoditas'],
    ]]);
})->name('home');

// Send stale E-Proposal bookmarks to the dashboard after the feature removal.
Route::get('/proposals/{path?}', fn () => redirect()->route('dashboard'))->where('path', '.*');

Route::controller(PublicInformationController::class)->group(function () {
    Route::get('/data-poktan', 'poktans')->name('public.poktans');
    Route::get('/data-alsintan', 'alsintans')->name('public.alsintans');
    Route::get('/data-saprodi', 'saprodis')->name('public.saprodis');
    Route::get('/tanaman-pangan', 'crops')->name('public.crops');
    Route::get('/laporan-publik', 'reports')->name('public.reports');
});

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/lupa-password', [AuthController::class, 'forgot'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'emailResetLink'])->middleware('throttle:password-email')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/survei-lapangan', [AlsintanController::class, 'create'])->middleware('role:admin,operator,ppl')->name('survey.create');
    Route::resource('alsintans', AlsintanController::class)->middleware('role:admin,operator,ppl');
    Route::middleware('role:admin,operator')->group(function () {
        Route::resource('poktans', PoktanController::class);
        Route::resource('saprodis', SaprodiController::class);
        Route::resource('crops', CropController::class);
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/{report}/ekspor', [ReportController::class, 'download'])->whereIn('report', ['alsintan', 'saprodi', 'tanaman-pangan', 'poktan'])->name('reports.download');
    });
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/pengaturan/profil', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
        Route::put('/pengaturan/tampilan', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
        Route::put('/pengaturan/keamanan', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
        Route::get('/pengaturan/backup', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/pengaturan/backup/pulihkan', [SettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
    });
});
