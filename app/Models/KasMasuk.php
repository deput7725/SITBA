<?php
// File: app/Models/KasMasuk.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMasuk extends Model
{
    use HasFactory;

    protected $table = 'kas_masuk';

    protected $fillable = [
        'NO',
        'tgl_transaksi',
        'npwz',
        'nama',
        'zakat',
        'zakat_fitrah',
        'infak',
        'keterangan',
    ];
}
