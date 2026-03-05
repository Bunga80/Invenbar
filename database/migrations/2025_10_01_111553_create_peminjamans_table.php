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
    Schema::create('peminjamans', function (Blueprint $table) {
    $table->id();
    $table->string('kode_peminjaman')->unique();
    $table->string('peminjam'); // input manual
    $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
    $table->integer('jumlah');
    $table->date('tanggal_pinjam');
    $table->date('tanggal_kembali')->nullable();
    $table->enum('status', ['dipinjam', 'sudah dikembalikan'])->default('dipinjam');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
