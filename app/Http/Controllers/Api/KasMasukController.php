<?php
// File: app/Http/Controllers/Api/KasMasukController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KasMasuk;
use App\Models\Bank;
use App\Models\PendaftaranZakat; // Pastikan model ini ada dan benar
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use NumberToWords\NumberToWords;
use App\Imports\KasMasukImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasMasukController extends Controller
{
    // ... semua metode Anda yang lain (index, store, dll.) tidak perlu diubah ...
    // ... saya sertakan lagi untuk kelengkapan ...

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

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv,xls',
            'object_zis' => 'required|string|max:255',
            'uraian' => 'nullable|string',
            'bank_rekening' => 'required|integer|exists:bank,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data yang diberikan tidak valid.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $bank = Bank::find($request->input('bank_rekening'));
            if (!$bank) {
                return response()->json(['message' => 'Bank yang dipilih tidak valid.'], 404);
            }
            $file = $request->file('file');
            $objectZis = $request->input('object_zis');
            $uraian = $request->input('uraian');
            $namaBank = $bank->nama_bank;
            $nomorRekening = $bank->nomor_rekening;
            $import = new KasMasukImport($objectZis, $uraian, $namaBank, $nomorRekening);
            Excel::import($import, $file);
            DB::commit();
            $stats = $import->getStats();
            return response()->json(['message' => 'Proses impor Kas Masuk berhasil diselesaikan.', 'data' => ['records_updated' => $stats['updated'] ?? 0, 'records_failed_or_skipped' => $stats['failed'] ?? 0, 'failures' => $stats['failures'] ?? []]], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Kas Masuk Gagal: ' . $e->getMessage(), ['file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'N/A', 'exception' => $e]);
            return response()->json(['message' => 'Terjadi kesalahan fatal saat memproses file.', 'error' => $e->getMessage(), 'line' => $e->getLine()], 500);
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
            $html = $this->getHtmlFromProcessor($templateProcessor);
            $pdf = Pdf::loadHTML($html);
            $fileName = 'Bukti Setor - ' . ($kasMasuk->pendaftaran->nama ?? $kasMasuk->nama) . '.pdf';
            return $pdf->stream($fileName);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal membuat dokumen.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ========================================================================
     * [METODE YANG DIPERBAIKI] Mencetak BANYAK bukti setor (batch)
     * ========================================================================
     */
    public function cetakBuktiPdfBatchFromWord(Request $request)
    {
        // 1. Validasi yang sudah diperbaiki untuk menerima 'ids' ATAU 'limit'
        $validator = Validator::make($request->all(), [
            'ids' => 'sometimes|required_without:limit|array',
            'ids.*' => 'integer|exists:pendaftaran_zakat,id',
            'limit' => 'sometimes|required_without:ids|integer|min:1',
        ]);

        if ($validator->fails()) { 
            return response()->json(['message' => 'Data ID tidak valid.', 'errors' => $validator->errors()], 422); 
        }

        try {
            $templatePath = storage_path('app/templates/template_bukti_setoran.docx');
            if (!file_exists($templatePath)) { 
                return response()->json(['message' => 'File template Word tidak ditemukan.'], 404); 
            }

            // 2. Logika untuk mengambil data Pendaftar, bukan Kas Masuk
            $pendaftarQuery = PendaftaranZakat::query();

            if ($request->has('limit')) {
                $limit = $request->input('limit');
                $pendaftarRecords = $pendaftarQuery->orderBy('id', 'desc')->take($limit)->get();
            } else {
                $ids = $request->input('ids');
                $pendaftarRecords = $pendaftarQuery->whereIn('id', $ids)->get();
            }

            if ($pendaftarRecords->isEmpty()) { 
                return response()->json(['message' => 'Data pendaftar tidak ditemukan.'], 404); 
            }

            $finalHtml = '';
            foreach ($pendaftarRecords as $pendaftar) {
                $kasMasuk = KasMasuk::where('nik', $pendaftar->nik)->latest()->first();
                
                if (!$kasMasuk) {
                    $kasMasuk = new KasMasuk([
                        'nama' => $pendaftar->nama, 'npwz' => $pendaftar->npwz ?? 'N/A',
                        'nik' => $pendaftar->nik, 'alamat_rumah' => $pendaftar->alamat_rumah,
                        'handphone' => $pendaftar->handphone, 'email' => $pendaftar->email,
                        'tgl_transaksi' => now(),
                        'zakat' => 0, 'zakat_fitrah' => 0, 'infak' => 0,
                    ]);
                }

                $templateProcessor = new TemplateProcessor($templatePath);
                $total = $kasMasuk->zakat + $kasMasuk->zakat_fitrah + $kasMasuk->infak;
                
                $this->fillTemplate($templateProcessor, $kasMasuk, $total);
                
                $html = $this->getHtmlFromProcessor($templateProcessor);
                $finalHtml .= $html;
            }

            $pdf = Pdf::loadHTML($finalHtml);
            return $pdf->stream('kumpulan-bukti-setor-' . date('Y-m-d') . '.pdf');

        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal membuat dokumen batch.', 'error' => $e->getMessage()], 500);
        }
    }

    private function fillTemplate(TemplateProcessor $templateProcessor, KasMasuk $kasMasuk, $total)
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('id');
        $terbilangIndonesia = $numberTransformer->toWords($total);
        
        $templateProcessor->setValue('nama_penyetor', $kasMasuk->nama);
        $templateProcessor->setValue('npwz', $kasMasuk->npwz);
        $templateProcessor->setValue('nik', $kasMasuk->nik ?? '-');
        $templateProcessor->setValue('alamat', $kasMasuk->alamat_rumah ?? '-');
        $templateProcessor->setValue('kontak', ($kasMasuk->handphone ?? '-') . ' / ' . ($kasMasuk->email ?? '-'));
        $templateProcessor->setValue('nomor_bukti', $kasMasuk->no_transaksi ? str_pad($kasMasuk->no_transaksi, 6, '0', STR_PAD_LEFT) : 'N/A');
        $templateProcessor->setValue('periode', \Carbon\Carbon::parse($kasMasuk->tgl_transaksi)->isoFormat('MMMM YYYY'));
        $templateProcessor->setValue('jumlah', number_format($total, 0, ',', '.'));
        $templateProcessor->setValue('terbilang', ucwords($terbilangIndonesia) . ' Rupiah');
        $templateProcessor->setValue('catatan_transaksi', $kasMasuk->catatan ?? 'Tidak ada catatan.');
        $templateProcessor->setValue('NO',''.(\Carbon\Carbon::parse($kasMasuk->tgl_transaksi)->isoFormat('DD/MM/YYYY')).' / '.'km'.' / '.($kasMasuk->jumlah_transaksi ?? '-').' / '.($kasMasuk->no_transaksi ? str_pad($kasMasuk->no_transaksi, 6, '0', STR_PAD_LEFT) : 'N/A'));
        $templateProcessor->setValue('tanggal_transaksi', \Carbon\Carbon::parse($kasMasuk->tgl_transaksi)->isoFormat('DD/MM/YYYY'));
        $templateProcessor->setValue('jumlah_ts', $kasMasuk->jumlah_transaksi ?? '-');
        $templateProcessor->setValue('object_zis', $kasMasuk->object_zis ?? '-');
        $templateProcessor->setValue('uraian', $kasMasuk->uraian ?? '-');
        $templateProcessor->setValue('via', $kasMasuk->via ?? '-');
    }

    private function getHtmlFromProcessor(TemplateProcessor $templateProcessor): string
    {
        $tempFilePath = $templateProcessor->save();
        $phpWord = IOFactory::load($tempFilePath);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
        $htmlContent = $htmlWriter->getContent();

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }

        $compactCss = "<style> body { font-family: Courier New, serif; font-size: 20.5pt; } p { margin: 0; padding: 0; line-height: 1.2; } table { border-collapse: collapse; width: 100%; page-break-inside: avoid; } td { padding: 1px 2px !important; } </style>";
        
        $htmlContent = str_replace('</head>', $compactCss . '</head>', $htmlContent);

        return $htmlContent;
    }
}
