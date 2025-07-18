<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranZakat;
use Illuminate\Http\Request;

class PendaftaranViewController extends Controller
{
    /**
     * Menampilkan halaman daftar pendaftaran perorangan.
     */
    public function tampilPerorangan()
    {
        // 1. Ambil data dari database menggunakan Model yang sudah ada
        // Kita filter hanya yang perorangan (id_lembaga adalah null)
        $pendaftar = PendaftaranZakat::whereNull('id_lembaga')
                                    ->orderBy('nama', 'asc')
                                    ->paginate(10); // Gunakan paginasi untuk data yang banyak

        // 2. Kirim data tersebut ke sebuah file 'view'
        // Kita akan membuat file 'pendaftaran/perorangan.blade.php' selanjutnya
        return view('pendaftaran.perorangan', [
            'pendaftar' => $pendaftar
        ]);
    }
}
