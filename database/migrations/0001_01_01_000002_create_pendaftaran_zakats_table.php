<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_pendaftaran_zakats_table.php

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
        Schema::create('pendaftaran_zakat', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_registrasi')->nullable();
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
            $table->string('email', 100)->nullable()->unique();
            $table->string('upz', 100)->nullable();
            $table->bigInteger('zakat')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('tipe_muzaki', ['Perorangan', 'Lembaga'])->default('Perorangan');
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_zakat');
    }
};
