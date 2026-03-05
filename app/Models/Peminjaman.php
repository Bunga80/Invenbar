<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{

    use HasFactory;
    protected $table = 'peminjamans'; 

    protected $fillable = [
        'kode_peminjaman',
        'peminjam', 
        'no_telp',         
        'barang_id',         
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
    ];

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
