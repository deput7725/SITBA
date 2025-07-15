<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        // File: database/migrations/xxxx_modify_tipe_muzaki...
    public function up(): void
    {
        Schema::table('pendaftaran_zakat', function (Blueprint $table) {
            // Kita ubah kolom `tipe_muzaki` menjadi `id_lembaga` agar lebih deskriptif
            // dan bisa menyimpan ID seperti 'lb001'. Kolom ini bisa NULL
            // karena tidak semua pendaftaran adalah lembaga.
            $table->string('id_lembaga', 10)->nullable()->after('tipe_muzaki');

            // Tambahkan foreign key constraint
            $table->foreign('id_lembaga')->references('id_lb')->on('lembaga')->onDelete('set null');

            // Hapus kolom lama jika sudah tidak diperlukan
            $table->dropColumn('tipe_muzaki');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_zakat', function (Blueprint $table) {
            //
        });
    }
};
