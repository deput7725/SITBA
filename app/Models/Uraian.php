<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// app/Models/Uraian.php
class Uraian extends Model
{
    use HasFactory;
    protected $table = 'uraian';
    protected $fillable = ['kategori', 'nama_uraian'];
}
