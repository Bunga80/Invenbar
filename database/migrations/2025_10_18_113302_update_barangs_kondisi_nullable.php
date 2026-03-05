// Buat migration baru untuk update table barangs
// Jalankan command: php artisan make:migration update_barangs_kondisi_nullable

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Ubah kolom kondisi menjadi nullable dengan default 0
            $table->integer('kondisi_baik')->default(0)->change();
            $table->integer('kondisi_rusak_ringan')->default(0)->change();
            $table->integer('kondisi_rusak_berat')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('kondisi_baik')->default(0)->change();
            $table->integer('kondisi_rusak_ringan')->default(0)->change();
            $table->integer('kondisi_rusak_berat')->default(0)->change();
        });
    }
};
