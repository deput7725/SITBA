<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // File: database/migrations/xxxx_create_lembagas_table.php
    public function up(): void
    {
        Schema::create('lembaga', function (Blueprint $table) {
            // id_lb akan menjadi Primary Key dengan format 'lb001'
            $table->string('id_lb', 10)->primary(); 
            $table->string('nama', 255);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lembagas');
    }
};
