<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SklController;
use App\Http\Controllers\NisnController;
use App\Http\Controllers\BkController;

Route::get('/', function () {
    return view('welcome');
});

// Public: Cek Kelulusan
Route::get('/cek-kelulusan', [SklController::class, 'cekKelulusan'])->name('cek-kelulusan');
Route::post('/cek-kelulusan/check', [SklController::class, 'cekKelulusanCheck'])->name('cek-kelulusan.check');
Route::get('skl/cetak/pdf/{student}', [SklController::class, 'cetakPdf'])->name('skl.cetak.pdf');

// Public: Cetak NISN
Route::get('/cetak-nisn', [NisnController::class, 'index'])->name('cetak-nisn.index');
Route::post('/cetak-nisn/check', [NisnController::class, 'check'])->name('cetak-nisn.check');
Route::get('/cetak-nisn/pdf/{student}', [NisnController::class, 'pdf'])->name('cetak-nisn.pdf');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::resource('students', StudentController::class);
    Route::get('students/{student}/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');
    Route::get('students-export-excel', [StudentController::class, 'exportExcel'])->name('students.export-excel');
    Route::get('students-import-template', [StudentController::class, 'importTemplate'])->name('students.import-template');
    Route::post('students-import-excel', [StudentController::class, 'importExcel'])->name('students.import-excel');
    Route::resource('classes', SchoolClassController::class);
    Route::resource('semesters', SemesterController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{class}', [ReportController::class, 'show'])->name('reports.show');
    Route::post('reports/{class}', [ReportController::class, 'store'])->name('reports.store');
    Route::get('reports/{class}/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('reports/{class}/import-template', [ReportController::class, 'importTemplate'])->name('reports.import-template');
    Route::post('reports/{class}/import-excel', [ReportController::class, 'importExcel'])->name('reports.import-excel');
    Route::resource('subjects', SubjectController::class);

    // Cetak SKL
    Route::get('skl/cetak', [SklController::class, 'cetakIndex'])->name('skl.cetak.index');
    Route::post('skl/cetak/settings', [SklController::class, 'saveCetakSettings'])->name('skl.cetak.settings');
    Route::get('skl/cetak/{class}', [SklController::class, 'cetakShow'])->name('skl.cetak.show');

    // SKL
    Route::get('skl', [SklController::class, 'index'])->name('skl.index');
    Route::get('skl/{class}', [SklController::class, 'show'])->name('skl.show');
    Route::get('skl/{class}/export-excel', [SklController::class, 'exportExcel'])->name('skl.export-excel');

    // BK (Bimbingan Konseling)
    Route::get('bk', [BkController::class, 'dashboard'])->name('bk.dashboard');
    Route::get('bk/konsultasi', [BkController::class, 'konsultasiIndex'])->name('bk.konsultasi.index');
    Route::post('bk/konsultasi', [BkController::class, 'konsultasiStore'])->name('bk.konsultasi.store');
    Route::put('bk/konsultasi/{konsultasi}', [BkController::class, 'konsultasiUpdate'])->name('bk.konsultasi.update');
    Route::delete('bk/konsultasi/{konsultasi}', [BkController::class, 'konsultasiDestroy'])->name('bk.konsultasi.destroy');
    Route::post('bk/jadwal', [BkController::class, 'jadwalStore'])->name('bk.jadwal.store');
    Route::put('bk/jadwal/{jadwal}', [BkController::class, 'jadwalUpdate'])->name('bk.jadwal.update');
    Route::get('bk/pelanggaran', [BkController::class, 'pelanggaranIndex'])->name('bk.pelanggaran.index');
    Route::post('bk/pelanggaran', [BkController::class, 'pelanggaranStore'])->name('bk.pelanggaran.store');
    Route::put('bk/pelanggaran/{pelanggaran}', [BkController::class, 'pelanggaranUpdate'])->name('bk.pelanggaran.update');
    Route::delete('bk/pelanggaran/{pelanggaran}', [BkController::class, 'pelanggaranDestroy'])->name('bk.pelanggaran.destroy');
    Route::get('bk/poin', [BkController::class, 'poinIndex'])->name('bk.poin.index');
    Route::post('bk/poin', [BkController::class, 'poinStore'])->name('bk.poin.store');
    Route::delete('bk/poin/{poin}', [BkController::class, 'poinDestroy'])->name('bk.poin.destroy');
    Route::post('bk/solusi', [BkController::class, 'solusiStore'])->name('bk.solusi.store');
    Route::put('bk/solusi/{solusi}', [BkController::class, 'solusiUpdate'])->name('bk.solusi.update');
    Route::get('bk/riwayat/{student}', [BkController::class, 'riwayat'])->name('bk.riwayat');
    Route::get('bk/laporan', [BkController::class, 'laporanIndex'])->name('bk.laporan.index');
    Route::get('bk/laporan/pdf', [BkController::class, 'laporanExportPdf'])->name('bk.laporan.pdf');

    // Settings
    Route::get('settings/website', [SettingController::class, 'website'])->name('settings.website');
    Route::post('settings/website', [SettingController::class, 'websiteUpdate'])->name('settings.website.update');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('git-update', [SettingController::class, 'gitUpdate'])->name('settings.git-update');
});
