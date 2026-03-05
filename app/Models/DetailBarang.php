<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'kode_unit',
        'kondisi',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function bisaDipinjam()
    {
        return $this->kondisi !== 'Rusak Berat';
    }

    public function getStatusPeminjamanAttribute()
    {
        return $this->kondisi === 'Rusak Berat' ? 'Tidak Bisa Dipinjam' : 'Bisa Dipinjam';
    }
}