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
    // Properti untuk menyimpan hasil proses
    private $successRows = 0;
    private $failedRows = [];

    /**
     * Method ini akan dipanggil dengan seluruh data dari file.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $rowIndex => $row) {
            // 1. Definisikan aturan validasi untuk setiap baris
            $validator = Validator::make($row->toArray(), [
                'npwz' => 'required|string|max:100',
                'nama' => 'required|string|max:100',
                'tgl_transaksi' => 'required',
                'zakat' => 'nullable|numeric',
                'zakat_fitrah' => 'nullable|numeric',
                'infak' => 'nullable|numeric',
            ]);

            // 2. Jika validasi gagal
            if ($validator->fails()) {
                // Tambahkan baris yang gagal beserta errornya ke dalam laporan
                $this->failedRows[] = [
                    'row_number' => $rowIndex + 2, // +2 karena baris 1 adalah heading, dan index dimulai dari 0
                    'data' => $row,
                    'errors' => $validator->errors()->all(),
                ];
                continue; // Lanjutkan ke baris berikutnya
            }

            // 3. Jika validasi berhasil, siapkan data untuk dimasukkan ke database
            $dataToInsert[] = [
                'NO'            => $row['no'] ?? null,
                'tgl_transaksi' => Date::excelToDateTimeObject($row['tgl_transaksi'])->format('Y-m-d'),
                'npwz'          => $row['npwz'],
                'nama'          => $row['nama'],
                'zakat'         => $row['zakat'] ?? 0,
                'zakat_fitrah'  => $row['zakat_fitrah'] ?? 0,
                'infak'         => $row['infak'] ?? 0,
                'keterangan'    => $row['keterangan'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // 4. Masukkan semua data yang valid sekaligus (Bulk Insert) untuk efisiensi
        if (!empty($dataToInsert)) {
            KasMasuk::insert($dataToInsert);
            $this->successRows = count($dataToInsert);
        }
    }

    /**
     * Method untuk mengambil jumlah data yang berhasil diimpor.
     */
    public function getSuccessCount(): int
    {
        return $this->successRows;
    }

    /**
     * Method untuk mengambil daftar data yang gagal diimpor.
     */
    public function getFailedRows(): array
    {
        return $this->failedRows;
    }
}
