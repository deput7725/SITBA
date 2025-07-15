<?php
// File: app/Models/ArsipZakat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipZakat extends Model
{
    use HasFactory;

    protected $table = 'arsip_zakat';

    // Arsip biasanya tidak di-update, jadi fillable bisa lebih terbatas
    // Namun untuk kemudahan, kita definisikan semua
    protected $fillable = [
        'nama', 'npwp', 'nik', 'nip', 'tanggal_lahir', 'tempat_lahir',
        'jenis_kelamin', 'pekerjaan', 'alamat_korespondensi', 'alamat_rumah',
        'alamat_kantor', 'telepon', 'handphone', 'email', 'upz', 'zakat_awal',
        'catatan', 'tipe_muzaki', 'NO', 'tgl_transaksi', 'zakat_ulang',
        'zakat_fitrah', 'infak', 'keterangan'
    ];
}
