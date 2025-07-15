<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendaftaranZakatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tanggal_registrasi' => $this->tanggal_registrasi,
            'nama' => $this->nama,
            'npwp' => $this->npwp,
            'nik' => $this->nik,
            'nip' => $this->nip,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tempat_lahir' => $this->tempat_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'pekerjaan' => $this->pekerjaan,
            'alamat_korespondensi' => $this->alamat_korespondensi,
            'alamat_rumah' => $this->alamat_rumah,
            'alamat_kantor' => $this->alamat_kantor,
            'telepon' => $this->telepon,
            'handphone' => $this->handphone,
            'email' => $this->email,
            'upz' => $this->upz,
            'zakat' => (int) $this->zakat, // Casting ke integer
            'catatan' => $this->catatan,
            'created_at' => $this->created_at->toDateTimeString(),
            
            // Tampilkan data lembaga jika ada relasi
            'lembaga' => $this->whenLoaded('lembaga', function () {
                return [
                    'id_lembaga' => $this->lembaga->id_lb,
                    'nama_lembaga' => $this->lembaga->nama,
                ];
            }, null) // Jika tidak ada, tampilkan null
        ];
    }
}
