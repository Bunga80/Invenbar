<?php

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
        Schema::table('barangs', function (Blueprint $table) {
        $table->integer('kondisi_baik')->default(0)->after('kondisi');
        $table->integer('kondisi_rusak_ringan')->default(0)->after('kondisi_baik');
        $table->integer('kondisi_rusak_berat')->default(0)->after('kondisi_rusak_ringan');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
        $table->dropColumn(['kondisi_baik', 'kondisi_rusak_ringan', 'kondisi_rusak_berat']);
    });
    }
};
