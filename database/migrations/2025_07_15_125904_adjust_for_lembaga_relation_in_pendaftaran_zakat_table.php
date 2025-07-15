<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_adjust_for_lembaga_relation...

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_zakat', function (Blueprint $table) {
            // Hapus kolom lama jika masih ada
            if (Schema::hasColumn('pendaftaran_zakat', 'tipe_muzaki')) {
                $table->dropColumn('tipe_muzaki');
            }

            // Tambahkan kolom baru untuk foreign key
            $table->string('id_lembaga', 10)->nullable()->after('catatan');

            // Definisikan foreign key constraint
            $table->foreign('id_lembaga')->references('id_lb')->on('lembaga')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_zakat', function (Blueprint $table) {
            $table->dropForeign(['id_lembaga']);
            $table->dropColumn('id_lembaga');
            $table->enum('tipe_muzaki', ['Perorangan', 'Lembaga'])->default('Perorangan');
        });
    }
};
