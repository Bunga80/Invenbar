<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahBarang = Barang::count();
        $jumlahKategori = Kategori::count();
        $jumlahLokasi = Lokasi::count();
        $jumlahUser = User::count();

        // Barang yang sedang dipinjam (belum dikembalikan)
        $jumlahDipinjam = Peminjaman::where('status', 'dipinjam')->count();

        // Barang yang terlambat (sudah lewat tgl_kembali tapi belum dikembalikan)
        $jumlahTerlambat = Peminjaman::where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', Carbon::now())
            ->count();

        // Ambil daftar barang yang dipinjam
        $peminjamans = Peminjaman::with(['barang', 'user'])
            ->where('status', 'dipinjam')
            ->latest()
            ->take(5)
            ->get();

        $kondisiBaik = Barang::sum('kondisi_baik');
    $kondisiRusakRingan = Barang::sum('kondisi_rusak_ringan');
    $kondisiRusakBerat = Barang::sum('kondisi_rusak_berat');
    

        $barangTerbaru = Barang::with(['kategori', 'lokasi'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'jumlahBarang',
            'jumlahKategori',
            'jumlahLokasi',
            'jumlahUser',
            'jumlahDipinjam',
            'jumlahTerlambat',
            'peminjamans',
            'kondisiBaik',
            'kondisiRusakRingan',
            'kondisiRusakBerat',
            'barangTerbaru'
        ));
    }
}
