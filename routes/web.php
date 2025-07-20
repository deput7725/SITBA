<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PendaftaranViewController;
use App\Http\Controllers\Web\DataManagementController;
// Rute untuk Landing Page
Route::get('/', [PendaftaranViewController::class, 'landingPage'])->name('landing');

// Rute untuk menampilkan data perorangan
Route::get('/pendaftaran/perorangan', [PendaftaranViewController::class, 'tampilPerorangan'])->name('pendaftaran.perorangan');

// Rute untuk menampilkan data lembaga
Route::get('/pendaftaran/lembaga', [PendaftaranViewController::class, 'tampilLembaga'])->name('pendaftaran.lembaga');

// Rute untuk menampilkan halaman
Route::get('/manajemen-data', [DataManagementController::class, 'index'])->name('data.management');

// Rute untuk mengunduh template
Route::get('/unduh/template-pendaftaran', [DataManagementController::class, 'downloadTemplatePendaftaran'])->name('template.pendaftaran.download');
Route::get('/unduh/template-kas-masuk', [DataManagementController::class, 'downloadTemplateKasMasuk'])->name('template.kasmasuk.download');

Route::post('/pendaftaran/cetak-laporan-batch', [PendaftaranViewController::class, 'cetakLaporanPdfBatch'])->name('pendaftaran.cetak.batch');