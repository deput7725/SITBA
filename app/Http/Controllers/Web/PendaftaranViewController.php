<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranZakat;
use Illuminate\Http\Request;
use App\Models\Lembaga;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use NumberToWords\NumberToWords;
class PendaftaranViewController extends Controller
{
    public function landingPage()
    {
        return view('landing');
    }

    /**
     * Menampilkan halaman daftar pendaftaran perorangan dengan filter dan search.
     */
    public function tampilPerorangan(Request $request)
    {
        $query = PendaftaranZakat::whereNull('id_lembaga');

        // --- INI LOGIKA BARUNYA ---
        // Cek apakah ada parameter 'search' di URL
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            // Tambahkan kondisi where untuk mencari di beberapa kolom
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nik', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pekerjaan', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }
        // --- AKHIR LOGIKA BARU ---

        $pendaftar = $query->orderBy('nama', 'asc')->paginate(15);

        return view('pendaftaran.perorangan', [
            'pendaftar' => $pendaftar
        ]);
    }

    /**
     * Menampilkan halaman daftar pendaftaran lembaga dengan filter dan search.
     */
    public function tampilLembaga(Request $request)
    {
        $query = PendaftaranZakat::with('lembaga')->whereNotNull('id_lembaga');

        // Logika filter berdasarkan lembaga (tidak berubah)
        if ($request->has('id_lembaga') && $request->id_lembaga != '') {
            $query->where('id_lembaga', $request->id_lembaga);
        }

        // --- INI LOGIKA BARUNYA ---
        // Cek apakah ada parameter 'search' di URL
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nik', 'LIKE', "%{$searchTerm}%");
            });
        }
        // --- AKHIR LOGIKA BARU ---

        $pendaftar = $query->orderBy('nama', 'asc')->paginate(15);
        $daftarLembaga = Lembaga::orderBy('nama', 'asc')->get();

        return view('pendaftaran.lembaga', [
            'pendaftar' => $pendaftar,
            'daftarLembaga' => $daftarLembaga,
            'selectedLembagaId' => $request->id_lembaga
        ]);
    }
    public function cetakLaporanPdfBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pendaftaran_zakat,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data ID tidak valid.', 'errors' => $validator->errors()], 422);
        }

        try {
            $templatePath = storage_path('app/templates/template_bukti_setoran.docx');
            if (!file_exists($templatePath)) {
                return response()->json(['message' => 'File template Word tidak ditemukan.'], 404);
            }

            $ids = $request->input('ids');
            $pendaftarRecords = PendaftaranZakat::with('lembaga')->whereIn('id', $ids)->get();

            if ($pendaftarRecords->isEmpty()) {
                return response()->json(['message' => 'Data tidak ditemukan untuk ID yang dipilih.'], 404);
            }

            $finalHtml = '';
            foreach ($pendaftarRecords as $index => $pendaftar) {
                $templateProcessor = new TemplateProcessor($templatePath);
                
                // --- Panggil helper method yang sudah diperbaiki ---
                $this->fillPendaftaranTemplate($templateProcessor, $pendaftar);
                
                $html = $this->getHtmlFromProcessor($templateProcessor);
                $finalHtml .= $html;
            }

            $pdf = Pdf::loadHTML($finalHtml);
            return $pdf->download('kumpulan-bukti-pendaftaran-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan di server saat membuat PDF.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * --- INI METHOD YANG DIPERBAIKI ---
     * Helper method untuk mengisi placeholder template dari data pendaftaran.
     */
    private function fillPendaftaranTemplate(TemplateProcessor $templateProcessor, PendaftaranZakat $pendaftar)
    {   
        $total = $pendaftar->zakat + $pendaftar->zakat_fitrah + $pendaftar->infak;
         // 1. Buat instance dari pustaka NumberToWords.
        $numberToWords = new NumberToWords();

        // 2. Dapatkan "transformer" khusus untuk Bahasa Indonesia.
        $numberTransformer = $numberToWords->getNumberTransformer('id');

        // 3. Konversi angka ke dalam kata-kata Bahasa Indonesia.
        $terbilangIndonesia = $numberTransformer->toWords($total);
        
        // Menggunakan nama placeholder yang sama persis dengan template Anda
        $templateProcessor->setValue('nama_penyetor', $pendaftar->nama);
        $templateProcessor->setValue('npwz', $pendaftar->nik);
        $templateProcessor->setValue('nik', $pendaftar->nik ?? '-');
        $templateProcessor->setValue('alamat', $pendaftar->alamat_rumah ?? '-');
        $templateProcessor->setValue('kontak', ($pendaftar->handphone ?? '-') . ' / ' . ($pendaftar->email ?? '-'));
        
        // Data Transaksi (diisi dengan nilai default karena belum ada)
        $nomor_kompleks = \Carbon\Carbon::parse($pendaftar->tgl_transaksi)->isoFormat('DD/MM/YYYY') .' / '.'km'.' / '.($pendaftar->jumlah_transaksi ?? '-').' / '.($pendaftar->no_transaksi ? str_pad($pendaftar->no_transaksi, 6, '0', STR_PAD_LEFT) : 'N/A');
        $templateProcessor->setValue('NO', $nomor_kompleks); // <-- Menggunakan placeholder 'NO' yang benar
        
        $templateProcessor->setValue('periode', \Carbon\Carbon::parse($pendaftar->tgl_transaksi)->isoFormat('MMMM YYYY'));
        $templateProcessor->setValue('tanggal_transaksi', \Carbon\Carbon::parse($pendaftar->tgl_transaksi)->isoFormat('DD/MM/YYYY'));
                $templateProcessor->setValue('jumlah', number_format($total, 0, ',', '.'));
        $templateProcessor->setValue('terbilang', ucwords($terbilangIndonesia) . ' Rupiah');
        $templateProcessor->setValue('catatan_transaksi', $pendaftar->catatan ?? 'Tidak ada catatan.');
        $templateProcessor->setValue('jumlah_ts', $pendaftar->jumlah_transaksi ?? '-'); // <-- Mengisi placeholder 'jumlah_ts'
    }

    /**
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
        
        $compactCss = "<style>@page { margin: 20px 30px !important; } body { font-family: 'Times New Roman', serif; font-size: 10pt !important; } p, span, div { margin: 0 !important; padding: 0 !important; line-height: 1.15 !important; } table { border-collapse: collapse !important; width: 100% !important; page-break-inside: avoid !important; } td { padding: 1px 2px !important; }</style>";
        return str_replace('</head>', $compactCss . '</head>', $htmlContent);
    }
}
