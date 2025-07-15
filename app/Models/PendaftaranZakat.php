<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranZakat extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_zakat';

    protected $fillable = [
        'tanggal_registrasi', 'nama', 'npwp', 'nik', 'nip', 'tanggal_lahir',
        'tempat_lahir', 'jenis_kelamin', 'pekerjaan', 'alamat_korespondensi',
        'alamat_rumah', 'alamat_kantor', 'telepon', 'handphone', 'email',
        'upz', 'zakat', 'catatan', 'id_lembaga',
    ];

    /**
     * Mendefinisikan relasi ke model Lembaga.
     */
    public function lembaga()
    {
        return $this->belongsTo(Lembaga::class, 'id_lembaga', 'id_lb');
    }
}
