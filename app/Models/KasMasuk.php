<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMasuk extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_zakat';

    protected $fillable = [
        'jumlah_transaksi',
        'no_transaksi',
        'tgl_transaksi',
        'npwz',
        'nama',
        'nik',
        'zakat',
        'zakat_fitrah',
        'infak',
        'keterangan',
    ];

    /**
     * Mendefinisikan relasi ke PendaftaranZakat.
     * Fungsi ini akan mencoba mencocokkan kolom 'npwz' dari tabel ini
     * dengan kolom 'nik' di tabel pendaftaran_zakat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranZakat::class, 'npwz', 'nik');
    }
}
