<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\PendaftaranViewController;
use App\Http\Controllers\Web\DataManagementController;


// Rute untuk menampilkan halaman login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Rute untuk memproses data login
Route::post('/login', [LoginController::class, 'login']);


Route::middleware(['auth'])->group(function () {

    // Rute untuk Landing Page setelah login
    Route::get('/', [PendaftaranViewController::class, 'landingPage'])->name('landing');

    // Rute untuk menampilkan data perorangan
    Route::get('/pendaftaran/perorangan', [PendaftaranViewController::class, 'tampilPerorangan'])->name('pendaftaran.perorangan');

    // Rute untuk menampilkan data lembaga
    Route::get('/pendaftaran/lembaga', [PendaftaranViewController::class, 'tampilLembaga'])->name('pendaftaran.lembaga');

    // Rute untuk mengunduh template
    Route::get('/unduh/template-pendaftaran', [DataManagementController::class, 'downloadTemplatePendaftaran'])->name('template.pendaftaran.download');
    Route::get('/unduh/template-kas-masuk', [DataManagementController::class, 'downloadTemplateKasMasuk'])->name('template.kasmasuk.download');

    Route::post('/pendaftaran/cetak-laporan-batch', [PendaftaranViewController::class, 'cetakLaporanPdfBatch'])->name('pendaftaran.cetak.batch');

    // Rute untuk logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

});
