<?php
// File: app/Http/Controllers/Api/KasMasukController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KasMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\KasMasukImport;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KasMasukController extends Controller
{
    public function index()
    {
        $data = KasMasuk::orderBy('tgl_transaksi', 'desc')->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tgl_transaksi' => 'required|date',
            'npwz' => 'required|string|max:100',
            'nama' => 'required|string|max:100',
            'zakat' => 'nullable|numeric',
            'zakat_fitrah' => 'nullable|numeric',
            'infak' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kas = KasMasuk::create($request->all());
        return response()->json($kas, 201);
    }

    public function show(KasMasuk $kasMasuk)
    {
        return response()->json($kasMasuk);
    }

    public function update(Request $request, KasMasuk $kasMasuk)
    {
        $validator = Validator::make($request->all(), [
            'tgl_transaksi' => 'sometimes|required|date',
            'npwz' => 'sometimes|required|string|max:100',
            'nama' => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kasMasuk->update($request->all());
        return response()->json($kasMasuk);
    }

    public function destroy(KasMasuk $kasMasuk)
    {
        $kasMasuk->delete();
        return response()->json(null, 204);
    }

    /**
     * Mengimpor data kas masuk dan memberikan laporan hasil.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kita tidak lagi menggunakan DB::beginTransaction() di sini karena
        // logika insert sudah ditangani secara massal di dalam kelas Import.
        try {
            $file = $request->file('file');

            // Buat instance dari kelas import
            $import = new KasMasukImport();
            
            // Jalankan proses import
            Excel::import($import, $file);

            // Ambil statistik dari proses import
            $successCount = $import->getSuccessCount();
            $failedRows = $import->getFailedRows();
            $failedCount = count($failedRows);

            // Bangun pesan respon berdasarkan hasil
            $message = "Proses impor selesai. {$successCount} data berhasil diimpor";
            if ($failedCount > 0) {
                $message .= " dan {$failedCount} data gagal.";
            } else {
                $message .= ".";
            }

            return response()->json([
                'message' => $message,
                'data' => [
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'failures' => $failedRows, // Sertakan detail data yang gagal
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan fatal saat memproses file.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

