<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/..._create_uraian_table.php
    public function up()
    {
        Schema::create('uraian', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // 'zakat' atau 'infaq'
            $table->string('nama_uraian');
            $table->timestamps();

            $table->unique(['kategori', 'nama_uraian']); // Mencegah duplikasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uraian');
    }
};
