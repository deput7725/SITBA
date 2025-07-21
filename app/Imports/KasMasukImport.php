<?php

namespace App\Imports;

use App\Models\KasMasuk;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KasMasukImport implements ToCollection, WithHeadingRow
{
    // Properti untuk menyimpan statistik hasil proses
    private $updatedCount = 0;
    private $failedRows = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            $validator = Validator::make($row->toArray(), [
                'nik' => 'required|string',
                'tgl_transaksi' => 'required',
                'nama' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                $this->failedRows[] = [
                    'row_number' => $rowIndex + 2,
                    'data' => $row,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // 2. Cari record kas masuk berdasarkan NIK.
            // Jika ada beberapa transaksi dengan NIK yang sama, ini akan mengambil yang pertama.
            // Pertimbangkan untuk mencari berdasarkan NIK dan Tanggal jika itu lebih unik.
            $kasMasuk = KasMasuk::where('nik', $row['nik'])
                                ->first();

            // 3. Jika record ditemukan, lakukan UPDATE.
            if ($kasMasuk) {
                $newTransactionNumber = $this->generateUniqueTransactionNumber();
                $jumlahTransaksi = $kasMasuk->jumlah_transaksi ?? 0;
                
                $newJumlahTransaksi = $jumlahTransaksi + 1;
                $kasMasuk->update([
                    'jumlah_transaksi' => $newJumlahTransaksi,
                    'no_transaksi'  => $newTransactionNumber, 
                    'tgl_transaksi' => isset($row['tgl_transaksi']) ? Date::excelToDateTimeObject($row['tgl_transaksi'])->format('Y-m-d') : $kasMasuk->tgl_transaksi,
                    'npwz'          => $row['npwz'],
                    'nama'          => $row['nama'],
                    'zakat'         => $row['zakat'] ?? $kasMasuk->zakat,
                    'zakat_fitrah'  => $row['zakat_fitrah'] ?? $kasMasuk->zakat_fitrah,
                    'infak'         => $row['infak'] ?? $kasMasuk->infak,
                    'catatan'    => $row['keterangan'] ?? $kasMasuk->keterangan,
                ]);
                $this->updatedCount++;
            
            // 4. Jika tidak ditemukan, catat sebagai GAGAL.
            } else {
                $this->failedRows[] = [
                    'row_number' => $rowIndex + 2,
                    'data' => $row,
                    'errors' => ['Data kas masuk dengan NIK ini tidak ditemukan di database.'],
                ];
            }
        }
    }

    private function generateUniqueTransactionNumber(): int
    {
        do {
            // Buat nomor acak antara 100000 dan 999999
            $number = mt_rand(100000, 999999);
            
            // Cek apakah nomor ini sudah ada di database
            $exists = KasMasuk::where('no_transaksi', $number)->exists();

        // Ulangi proses jika nomor sudah ada (exists == true)
        } while ($exists);

        return $number;
    }

    /**
     * Method untuk mengambil hasil statistik dari proses import.
     */
    public function getStats(): array
    {
        return [
            'updated' => $this->updatedCount,
            'failed' => count($this->failedRows),
            'failures' => $this->failedRows,
        ];
    }
}
