<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->enum('status_peminjaman', ['Bisa Dipinjam', 'Tidak Bisa Dipinjam'])
                  ->default('Bisa Dipinjam')
                  ->after('kondisi');
        });
    }

    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('status_peminjaman');
        });
    }
};

// Jalankan: php artisan make:migration add_status_peminjaman_to_barangs_table
// Copy kode di atas ke file migration yang dibuat
// Lalu jalankan: php artisan migrate