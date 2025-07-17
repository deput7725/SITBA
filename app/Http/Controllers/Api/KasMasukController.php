<?php
// File: app/Http/Controllers/Api/KasMasukController.php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KasMasuk;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Terbilang;

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
            'nik' => 'required|string|max:100',
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
    public function cetakBuktiPdfFromWord(KasMasuk $kasMasuk)
    {
        try {
            $templatePath = storage_path('app/templates/template_bukti_setoran.docx');
            if (!file_exists($templatePath)) {
                return response()->json(['message' => 'File template Word tidak ditemukan.'], 404);
            }

            $templateProcessor = new TemplateProcessor($templatePath);
            $kasMasuk->load('pendaftaran');
            $total = $kasMasuk->zakat + $kasMasuk->zakat_fitrah + $kasMasuk->infak;

            $this->fillTemplate($templateProcessor, $kasMasuk, $total);

            // Panggil helper method yang sudah dioptimalkan
            $html = $this->getHtmlFromProcessor($templateProcessor);
            $pdf = Pdf::loadHTML($html);
            
            $fileName = 'Bukti Setor - ' . ($kasMasuk->pendaftaran->nama ?? $kasMasuk->nama) . '.pdf';
            return $pdf->stream($fileName);

        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal membuat dokumen.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * FINAL: Mencetak BANYAK bukti setor (batch) dalam satu file PDF dari template Word.
     */
    public function cetakBuktiPdfBatchFromWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:kas_masuk,id',
        ]);

        if ($validator->fails()) { return response()->json(['errors' => $validator->errors()], 422); }

        try {
            $templatePath = storage_path('app/templates/template_bukti_setoran.docx');
            if (!file_exists($templatePath)) { return response()->json(['message' => 'File template Word tidak ditemukan.'], 404); }

            $kasMasukRecords = KasMasuk::with('pendaftaran')->whereIn('id', $request->input('ids'))->get();
            if ($kasMasukRecords->isEmpty()) { return response()->json(['message' => 'Data tidak ditemukan.'], 404); }

            $finalHtml = '';
            foreach ($kasMasukRecords as $index => $kasMasuk) {
                $templateProcessor = new TemplateProcessor($templatePath);
                $total = $kasMasuk->zakat + $kasMasuk->zakat_fitrah + $kasMasuk->infak;
                
                $this->fillTemplate($templateProcessor, $kasMasuk, $total);
                
                // Panggil helper method yang sudah dioptimalkan
                $html = $this->getHtmlFromProcessor($templateProcessor);
                $finalHtml .= $html;

                // Tambahkan pemisah halaman, kecuali untuk halaman terakhir
                if ($index < $kasMasukRecords->count() - 1) {
                    $finalHtml .= '<div style="page-break-after: always;"></div>';
                }
            }

            $pdf = Pdf::loadHTML($finalHtml);
            return $pdf->stream('kumpulan-bukti-setor-' . date('Y-m-d') . '.pdf');

        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal membuat dokumen batch.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method untuk mengisi placeholder di template. (Tidak ada perubahan di sini)
     */
    private function fillTemplate(TemplateProcessor $templateProcessor, KasMasuk $kasMasuk, $total)
    {
        $templateProcessor->setValue('nama_penyetor', $kasMasuk->pendaftaran->nama ?? $kasMasuk->nama);
        $templateProcessor->setValue('npwz', $kasMasuk->npwz);
        $templateProcessor->setValue('nik', $kasMasuk->pendaftaran->nik ?? '-');
        $templateProcessor->setValue('alamat', $kasMasuk->pendaftaran->alamat_rumah ?? '-');
        $templateProcessor->setValue('kontak', ($kasMasuk->pendaftaran->handphone ?? '-') . ' / ' . ($kasMasuk->pendaftaran->email ?? '-'));
        $templateProcessor->setValue('nomor_bukti', $kasMasuk->NO ? str_pad($kasMasuk->NO, 8, '0', STR_PAD_LEFT) : 'N/A');
        $templateProcessor->setValue('periode', \Carbon\Carbon::parse($kasMasuk->tgl_transaksi)->isoFormat('MMMM YYYY'));
        $templateProcessor->setValue('tanggal_transaksi', \Carbon\Carbon::parse($kasMasuk->tgl_transaksi)->isoFormat('DD/MM/YYYY'));
        $templateProcessor->setValue('jumlah', number_format($total, 0, ',', '.'));
        $templateProcessor->setValue('terbilang', ucwords(Terbilang::make($total)) . ' Rupiah');
        $templateProcessor->setValue('catatan_transaksi', $kasMasuk->keterangan ?? 'Tidak ada catatan.');
    }

    /**
     * --- INI METHOD YANG DIPERBAIKI ---
     * Helper method untuk mengkonversi prosesor template ke HTML dengan CSS yang dioptimalkan.
     */
    private function getHtmlFromProcessor(TemplateProcessor $templateProcessor): string
    {
        $tempFilePath = $templateProcessor->save();
        $phpWord = IOFactory::load($tempFilePath);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
        $htmlContent = $htmlWriter->getContent();

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }

        // --- INI KODE PERBAIKANNYA ---
        // Buat CSS untuk "memadatkan" layout
        $compactCss = "
            <style>
                body { font-family: 'Times New Roman', serif; font-size: 10.5pt; }
                p { margin: 0; padding: 0; line-height: 1.2; }
                table { border-collapse: collapse; width: 100%; page-break-inside: avoid; }
                td { padding: 1px 2px !important; }
            </style>
        ";

        // Suntikkan (inject) CSS ini ke dalam <head> dari HTML yang dihasilkan
        $htmlContent = str_replace('</head>', $compactCss . '</head>', $htmlContent);

        return $htmlContent;
    }
}

