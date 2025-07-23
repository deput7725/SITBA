<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Uraian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UraianController extends Controller
{
    /**
     * Mengambil semua uraian, dikelompokkan berdasarkan kategori.
     */
    public function index()
    {
        // Mengambil semua data dan mengelompokkannya
        $uraian = Uraian::all()->groupBy('kategori');
        return response()->json($uraian);
    }

    /**
     * Menyimpan uraian baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|in:zakat,infaq',
            'nama_uraian' => 'required|string|max:255|unique:uraian,nama_uraian,NULL,id,kategori,' . $request->kategori,
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $uraian = Uraian::create($request->all());

        return response()->json($uraian, 201);
    }

    public function destroy(Uraian $uraian)
    {
        try {
            $uraian->delete();
            return response()->json(['message' => 'Uraian berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            // Jika terjadi error, misal karena relasi database
            return response()->json([
                'message' => 'Gagal menghapus uraian.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}