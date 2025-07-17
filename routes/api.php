<?php

use App\Http\Controllers\Api\LembagaController;
use App\Http\Controllers\Api\KasMasukController; 
use App\Http\Controllers\Api\PendaftaranZakatController;
use Illuminate\Support\Facades\Route;

// --- Rute untuk mengelola Lembaga ---
Route::apiResource('lembaga', LembagaController::class)->parameters([
    'lembaga' => 'lembaga:id_lb' // Menggunakan ID kustom untuk route model binding
]);

// --- Rute untuk mengelola Kas Masuk ---
Route::apiResource('kas-masuk', KasMasukController::class)
    ->parameters(['kas-masuk' => 'kasMasuk']);

// --- Rute untuk mengelola Pendaftaran Zakat ---
Route::apiResource('pendaftaran-zakat', PendaftaranZakatController::class);

// --- Rute spesifik untuk import data massal ---
Route::post('pendaftaran-zakat/import/perorangan', [PendaftaranZakatController::class, 'importPerorangan']);
Route::post('pendaftaran-zakat/import/lembaga/{id_lb}', [PendaftaranZakatController::class, 'importLembaga']);

// --- RUTE BARU UNTUK UPDATE MASSAL ---
Route::post('pendaftaran-zakat/update-from-file', [PendaftaranZakatController::class, 'updateFromFile']);

// --- RUTE BARU UNTUK IMPORT KAS MASUK ---
Route::post('kas-masuk/import', [KasMasukController::class, 'import']);
// Rute untuk mencetak SATU bukti setor
Route::get('kas-masuk/{kasMasuk}/cetak-bukti-pdf-from-word', [KasMasukController::class, 'cetakBuktiPdfFromWord']);
// Rute untuk mencetak BANYAK bukti setor (batch)
Route::post('kas-masuk/cetak-bukti-pdf-batch-from-word', [KasMasukController::class, 'cetakBuktiPdfBatchFromWord']);
