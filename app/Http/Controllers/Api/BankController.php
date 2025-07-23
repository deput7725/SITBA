<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank; // Pastikan model Bank sudah ada
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Mengambil semua data bank, dikelompokkan berdasarkan kategori.
     */
    // app/Http/Controllers/Api/BankController.php
    public function index()
    {
        $banks = Bank::all()->groupBy(function($item) {
            return strtolower($item->kategori); // Paksa kunci menjadi huruf kecil
        });
        return response()->json($banks);
    }
}