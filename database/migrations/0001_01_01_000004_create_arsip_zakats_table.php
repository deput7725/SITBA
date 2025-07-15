<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_arsip_zakats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arsip_zakat', function (Blueprint $table) {
            $table->id();
            // Data dari pendaftaran_zakat
            $table->string('nama', 100);
            $table->string('npwp', 100)->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('nip', 30)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('pekerjaan', 100)->nullable();
            $table->text('alamat_korespondensi')->nullable();
            $table->text('alamat_rumah')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('handphone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('upz', 100)->nullable();
            $table->bigInteger('zakat_awal')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('tipe_muzaki', ['Perorangan', 'Lembaga']);

            // Data dari kas_masuk
            $table->integer('NO')->nullable();
            $table->date('tgl_transaksi');
            $table->bigInteger('zakat_ulang')->default(0);
            $table->bigInteger('zakat_fitrah')->default(0);
            $table->bigInteger('infak')->default(0);
            $table->text('keterangan')->nullable();

            // Informasi waktu arsip
            $table->timestamp('tanggal_arsip')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_zakat');
    }
};
