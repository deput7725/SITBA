<?php

namespace App\Imports;

use App\Models\PendaftaranZakat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PendaftaranZakatUpdateImport implements ToCollection, WithHeadingRow
{
    // Properti untuk menyimpan statistik hasil import
    private $updatedCount = 0;
    private $skippedCount = 0;

    /**
     * Method ini akan dipanggil dengan seluruh data dari file sebagai sebuah Collection.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Cek apakah ada kolom 'id' dan nilainya tidak kosong
            if (!isset($row['id']) || empty($row['id'])) {
                $this->skippedCount++;
                continue; // Lewati baris ini jika tidak ada ID
            }

            // 2. Cari pendaftaran berdasarkan ID dari file
            $pendaftaran = PendaftaranZakat::find($row['id']);

            // 3. Jika pendaftaran ditemukan, update datanya
            if ($pendaftaran) {
                $pendaftaran->update([
                    // Ambil nilai dari file, jika tidak ada, gunakan nilai lama (tidak diubah)
                    'tanggal_registrasi'   => isset($row['tanggal_registrasi']) ? Date::excelToDateTimeObject($row['tanggal_registrasi'])->format('Y-m-d') : $pendaftaran->tanggal_registrasi,
                    'nama'                 => $row['nama'] ?? $pendaftaran->nama,
                    'npwp'                 => $row['npwp'] ?? $pendaftaran->npwp,
                    'nik'                  => $row['nik'] ?? $pendaftaran->nik,
                    'nip'                  => $row['nip'] ?? $pendaftaran->nip,
                    'tanggal_lahir'        => isset($row['tanggal_lahir']) ? Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d') : $pendaftaran->tanggal_lahir,
                    'tempat_lahir'         => $row['tempat_lahir'] ?? $pendaftaran->tempat_lahir,
                    'jenis_kelamin'        => $row['jenis_kelamin'] ?? $pendaftaran->jenis_kelamin,
                    'pekerjaan'            => $row['pekerjaan'] ?? $pendaftaran->pekerjaan,
                    'alamat_korespondensi' => $row['alamat_korespondensi'] ?? $pendaftaran->alamat_korespondensi,
                    'alamat_rumah'         => $row['alamat_rumah'] ?? $pendaftaran->alamat_rumah,
                    'alamat_kantor'        => $row['alamat_kantor'] ?? $pendaftaran->alamat_kantor,
                    'telepon'              => $row['telepon'] ?? $pendaftaran->telepon,
                    'handphone'            => $row['handphone'] ?? $pendaftaran->handphone,
                    'email'                => $row['email'] ?? $pendaftaran->email,
                    'upz'                  => $row['upz'] ?? $pendaftaran->upz,
                    'zakat'                => $row['zakat'] ?? $pendaftaran->zakat,
                    'catatan'              => $row['catatan'] ?? $pendaftaran->catatan,
                    // Kita tidak mengupdate id_lembaga melalui file ini untuk menjaga konsistensi
                ]);
                $this->updatedCount++;
            } else {
                // Jika ID dari file tidak ditemukan di database, lewati
                $this->skippedCount++;
            }
        }
    }

    /**
     * Method untuk mengambil hasil statistik dari proses import.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
        ];
    }
}
