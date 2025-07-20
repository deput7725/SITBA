<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
// Kita tidak lagi memerlukan kelas Export atau Maatwebsite/Excel di sini

class DataManagementController extends Controller
{
    /**
     * Menampilkan halaman manajemen data.
     */
    public function index()
    {
        return view('data-management');
    }

    /**
     * Menangani permintaan unduh untuk template pendaftaran statis.
     */
    public function downloadTemplatePendaftaran()
    {
        // 1. Tentukan path lengkap ke file template Anda di dalam folder storage.
        $filePath = storage_path('app/templates/template_pendaftaran_zakat.xlsx');

        // 2. Cek apakah file tersebut ada untuk menghindari error.
        if (!file_exists($filePath)) {
            // abort(404) akan menampilkan halaman "Not Found".
            abort(404, 'File template pendaftaran tidak ditemukan.');
        }

        // 3. Gunakan response()->download() untuk mengirim file ke browser.
        return response()->download($filePath);
    }

    /**
     * Menangani permintaan unduh untuk template kas masuk statis.
     */
    public function downloadTemplateKasMasuk()
    {
        // Lakukan hal yang sama untuk template kas masuk
        $filePath = storage_path('app/templates/template_migrasi_kas_masuk.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File template kas masuk tidak ditemukan.');
        }

        return response()->download($filePath);
    }
}
