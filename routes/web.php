<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');
    Route::resource('classes', SchoolClassController::class);
    Route::resource('subjects', SubjectController::class);

    // Settings
    Route::get('settings/website', [SettingController::class, 'website'])->name('settings.website');
    Route::post('settings/website', [SettingController::class, 'websiteUpdate'])->name('settings.website.update');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('git-update', [SettingController::class, 'gitUpdate'])->name('settings.git-update');
});
