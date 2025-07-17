<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PendaftaranZakatResource;
use App\Imports\PendaftaranZakatUpsertImport;
use App\Models\Lembaga;
use App\Models\PendaftaranZakat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PendaftaranZakatController extends Controller
{
    /**
     * Menampilkan data pendaftaran, bisa difilter dan dicari.
     * ?tipe=Perorangan
     * ?tipe=Lembaga&id_lembaga=lb001
     * ?search=Budi
     * ?tipe=Perorangan&search=Budi
     */
    public function index(Request $request)
    {
        $query = PendaftaranZakat::query()->with('lembaga');

        // --- Logika Filter berdasarkan Tipe ---
        if ($request->has('tipe')) {
            $tipe = $request->query('tipe');

            if (strtolower($tipe) === 'perorangan') {
                $query->whereNull('id_lembaga');
            } elseif (strtolower($tipe) === 'lembaga') {
                if ($request->has('id_lembaga')) {
                    $idLembaga = $request->query('id_lembaga');
                    if (!Lembaga::find($idLembaga)) {
                        return response()->json(['message' => 'Data lembaga tidak ditemukan.'], 404);
                    }
                    $query->where('id_lembaga', $idLembaga);
                } else {
                    $query->whereNotNull('id_lembaga');
                }
            }
        }

        // --- INI BAGIAN BARUNYA: Logika untuk Pencarian ---
        if ($request->has('search')) {
            $searchTerm = $request->query('search');
            
            // Menambahkan kondisi pencarian ke query
            // Fungsi closure ini penting untuk mengelompokkan kondisi OR
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nik', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pekerjaan', 'LIKE', "%{$searchTerm}%");
            });
        }
        // --- AKHIR BAGIAN BARU ---

        $data = $query->orderBy('nama', 'asc')->paginate(15);
        return PendaftaranZakatResource::collection($data);
    }

    /**
     * Menyimpan satu data pendaftaran baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'nullable|email|unique:pendaftaran_zakat,email',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'id_lembaga' => 'nullable|string|exists:lembaga,id_lb', // Validasi ke tabel lembaga
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pendaftaran = PendaftaranZakat::create($request->all());
        return response()->json(new PendaftaranZakatResource($pendaftaran->load('lembaga')), 201);
    }

    /**
     * Menampilkan satu data pendaftaran spesifik.
     */
    public function show(PendaftaranZakat $pendaftaranZakat)
    {
        return new PendaftaranZakatResource($pendaftaranZakat->load('lembaga'));
    }

    /**
     * Memperbarui data pendaftaran.
     */
    public function update(Request $request, PendaftaranZakat $pendaftaranZakat)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|nullable|email|unique:pendaftaran_zakat,email,' . $pendaftaranZakat->id,
            'id_lembaga' => 'sometimes|nullable|string|exists:lembaga,id_lb',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pendaftaranZakat->update($request->all());
        return new PendaftaranZakatResource($pendaftaranZakat->load('lembaga'));
    }

    /**
     * Menghapus data pendaftaran.
     */
    public function destroy(PendaftaranZakat $pendaftaranZakat)
    {
        $pendaftaranZakat->delete();
        return response()->json(null, 204);
    }

    // --- FUNGSI IMPORT ---
    public function importPerorangan(Request $request)
    {
        return $this->handleImport($request, null);
    }

    public function importLembaga(Request $request, $id_lb)
    {
        if (!Lembaga::find($id_lb)) {
            return response()->json(['message' => 'Lembaga dengan ID ' . $id_lb . ' tidak ditemukan.'], 404);
        }
        return $this->handleImport($request, $id_lb);
    }

    /**
     * Method private untuk menangani logika import (Create & Update).
     *
     * @param Request $request
     * @param string|null $idLembaga
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleImport(Request $request, ?string $idLembaga)
    {
        $validator = Validator::make($request->all(), ['file' => 'required|file|mimes:xlsx,csv,xls']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            
            // Buat instance dari kelas import UPSERT yang baru
            $import = new PendaftaranZakatUpsertImport($idLembaga);
            
            // Jalankan proses import
            Excel::import($import, $file);

            // Jika berhasil, konfirmasi semua perubahan
            DB::commit();

            // Ambil statistik dari proses import
            $stats = $import->getStats();

            return response()->json([
                'message' => 'Proses impor (Create & Update) selesai.',
                'data' => [
                    'records_created' => $stats['created'],
                    'records_updated' => $stats['updated'],
                    'records_failed' => $stats['failed'],
                    'failures' => $stats['failures'],
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses file.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}