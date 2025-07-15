<?php
// File: app/Http/Controllers/Api/ArsipZakatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArsipZakat;
use Illuminate\Http\Request;

class ArsipZakatController extends Controller
{
    /**
     * Menampilkan data arsip.
     */
    public function index()
    {
        $data = ArsipZakat::orderBy('tanggal_arsip', 'desc')->get();
        return response()->json(['data' => $data]);
    }

    /**
     * Menampilkan satu data arsip spesifik.
     */
    public function show(ArsipZakat $arsipZakat)
    {
        return response()->json($arsipZakat);
    }
    
    // Method store, update, dan destroy sengaja tidak dibuat
    // karena tabel arsip biasanya hanya untuk dibaca (read-only).
    // Penambahan data ke arsip seharusnya melalui proses bisnis terpisah.
}

