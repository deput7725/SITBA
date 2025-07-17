
<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_kas_masuk_table.php

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
        Schema::create('kas_masuk', function (Blueprint $table) {
            $table->id();
            $table->integer('NO')->nullable();
            $table->date('tgl_transaksi');
            $table->string('npwz', 100);
            $table->string('nama', 100);
            
            // Kolom NIK untuk relasi ke pendaftaran_zakat
            $table->string('nik', 30)->nullable();
            $table->foreign('nik')->references('nik')->on('pendaftaran_zakat')->onDelete('set null');

            $table->bigInteger('zakat')->default(0);
            $table->bigInteger('zakat_fitrah')->default(0);
            $table->bigInteger('infak')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_masuk');
    }
};
