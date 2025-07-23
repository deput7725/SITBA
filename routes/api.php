<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\UraianController;
use App\Http\Controllers\Api\LembagaController;
use App\Http\Controllers\Api\KasMasukController; 
use App\Http\Controllers\Api\PendaftaranZakatController;

// --- Rute untuk mengelola Lembaga ---
Route::apiResource('lembaga', LembagaController::class)->parameters([
    'lembaga' => 'lembaga:id_lb' // Menggunakan ID kustom untuk route model binding
]);

// --- Rute untuk mengelola Kas Masuk ---
// Definisikan route spesifik untuk Kas Masuk terlebih dahulu
Route::post('kas-masuk/import', [KasMasukController::class, 'import']);
Route::get('kas-masuk/{kasMasuk}/cetak-bukti-pdf-from-word', [KasMasukController::class, 'cetakBuktiPdfFromWord']);
Route::post('kas-masuk/cetak-bukti-pdf-batch-from-word', [KasMasukController::class, 'cetakBuktiPdfBatchFromWord']);

// Baru definisikan apiResource untuk Kas Masuk
Route::apiResource('kas-masuk', KasMasukController::class)
    ->parameters(['kas-masuk' => 'kasMasuk']);

// Rute spesifik untuk import data massal
Route::post('pendaftaran-zakat/import/perorangan', [PendaftaranZakatController::class, 'importPerorangan']);
Route::post('pendaftaran-zakat/import/lembaga/{id_lb}', [PendaftaranZakatController::class, 'importLembaga']);

// Rute untuk update massal
Route::post('pendaftaran-zakat/update-from-file', [PendaftaranZakatController::class, 'updateFromFile']);

// Rute untuk hapus data massal
Route::delete('pendaftaran-zakat/batch-delete', [PendaftaranZakatController::class, 'batchDelete'])->name('pendaftaran.hapus.batch');

// Setelah semua route spesifik didefinisikan, baru panggil apiResource
Route::apiResource('pendaftaran-zakat', PendaftaranZakatController::class);

Route::get('uraian', [UraianController::class, 'index']);
Route::post('uraian', [UraianController::class, 'store']);
Route::delete('uraian/{uraian}', [UraianController::class, 'destroy']);

Route::get('bank', [BankController::class, 'index']);