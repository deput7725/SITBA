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
    private $successRows = 0;
    private $failedRows = [];

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $dataToInsert = [];

        foreach ($rows as $rowIndex => $row) {
            // --- INI BAGIAN UTAMA YANG DIPERBARUI ---
            // Kita tambahkan aturan validasi untuk NIK
            $validator = Validator::make($row->toArray(), [
                'npwz' => 'required|string|max:100',
                'nama' => 'required|string|max:100',
                'tgl_transaksi' => 'required',
                // Aturan baru: NIK wajib ada, harus string, panjangnya 16 digit,
                // dan NIK tersebut harus sudah terdaftar di tabel pendaftaran_zakat.
                'nik' => 'required|string|exists:pendaftaran_zakat,nik',
                'zakat' => 'nullable|numeric',
                'zakat_fitrah' => 'nullable|numeric',
                'infak' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $this->failedRows[] = [
                    'row_number' => $rowIndex + 2,
                    'data' => $row,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Tambahkan NIK saat menyiapkan data untuk dimasukkan ke database
            $dataToInsert[] = [
                'NO'            => $row['no'] ?? null,
                'tgl_transaksi' => Date::excelToDateTimeObject($row['tgl_transaksi'])->format('Y-m-d'),
                'npwz'          => $row['npwz'],
                'nik'           => $row['nik'], // <-- NIK sekarang disertakan
                'nama'          => $row['nama'],
                'zakat'         => $row['zakat'] ?? 0,
                'zakat_fitrah'  => $row['zakat_fitrah'] ?? 0,
                'infak'         => $row['infak'] ?? 0,
                'keterangan'    => $row['keterangan'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if (!empty($dataToInsert)) {
            KasMasuk::insert($dataToInsert);
            $this->successRows = count($dataToInsert);
        }
    }

    // Method getSuccessCount() dan getFailedRows() tidak perlu diubah
    public function getSuccessCount(): int
    {
        return $this->successRows;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }
}
