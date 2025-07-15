<?php

namespace App\Imports;

use App\Models\PendaftaranZakat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PendaftaranZakatImport implements ToModel, WithHeadingRow
{
    private $idLembaga;

    public function __construct(?string $idLembaga)
    {
        $this->idLembaga = $idLembaga;
    }

    public function model(array $row)
    {
        if (!isset($row['nama'])) {
            return null;
        }

        return new PendaftaranZakat([
            'tanggal_registrasi'   => isset($row['tanggal_registrasi']) ? Date::excelToDateTimeObject($row['tanggal_registrasi'])->format('Y-m-d') : null,
            'nama'                 => $row['nama'],
            'npwp'                 => $row['npwp'] ?? null,
            'nik'                  => $row['nik'] ?? null,
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
        ]);
    }
}
