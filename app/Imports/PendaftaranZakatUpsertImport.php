<?php

namespace App\Imports;

use App\Models\PendaftaranZakat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PendaftaranZakatUpsertImport implements ToCollection, WithHeadingRow
{
    private $idLembaga;
    
    // Properti untuk statistik tidak berubah
    private $createdCount = 0;
    private $updatedCount = 0;
    private $failedRows = [];

    public function __construct(?string $idLembaga)
    {
        $this->idLembaga = $idLembaga;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            // Validasi tetap menggunakan 'nik' dan 'nama' karena Laravel menormalisasi header
            $validator = Validator::make($row->toArray(), [
                'nik' => 'required|string|max:30',
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

            // --- INI BAGIAN UTAMA YANG DISESUAIKAN ---
            // Kita memetakan setiap kolom dari file template baru Anda ke kolom database.
            // Pastikan nama kunci (seperti 'tanggal_registrasi') cocok dengan header di file Anda
            // (setelah diubah menjadi huruf kecil dan spasi menjadi garis bawah).
            $dataToUpsert = [
                'nama'                 => $row['nama'], // Dari kolom "Nama"
                'tanggal_registrasi'   => isset($row['tanggal_registrasi']) ? Date::excelToDateTimeObject($row['tanggal_registrasi'])->format('Y-m-d') : null,
                'npwp'                 => $row['npwp'] ?? null,
                'nip'                  => $row['nip'] ?? null,
                'tanggal_lahir'        => isset($row['tanggal_lahir']) ? Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d') : null,
                'tempat_lahir'         => $row['tempat_lahir'] ?? null,
                'jenis_kelamin'        => $row['jenis_kelamin'] ?? 'Laki-laki',
                'pekerjaan'            => $row['pekerjaan'] ?? null,
                'alamat_korespondensi' => $row['alamat_korespondensi'] ?? null,
                'alamat_rumah'         => $row['alamat_rumah'] ?? null,
                'alamat_kantor'        => $row['alamat_kantor'] ?? null,
                'telepon'              => $row['telepon'] ?? null,
                'handphone'            => $row['handphone'] ?? null,
                'email'                => $row['email'] ?? null,
                'upz'                  => $row['upz'] ?? null,
                'zakat'                => $row['zakat'] ?? 0,
                'catatan'              => $row['catatan'] ?? null,
                'id_lembaga'           => $this->idLembaga,
            ];

            // Logika Update atau Create berdasarkan NIK tidak berubah
            $pendaftaran = PendaftaranZakat::updateOrCreate(
                ['nik' => $row['nik']], // Kunci pencarian tetap NIK
                $dataToUpsert
            );

            // Logika statistik tidak berubah
            if ($pendaftaran->wasRecentlyCreated) {
                $this->createdCount++;
            } elseif ($pendaftaran->wasChanged()) {
                $this->updatedCount++;
            }
        }
    }

    /**
     * Method untuk mengambil statistik tidak berubah.
     */
    public function getStats(): array
    {
        return [
            'created' => $this->createdCount,
            'updated' => $this->updatedCount,
            'failed' => count($this->failedRows),
            'failures' => $this->failedRows,
        ];
    }
}
