<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $peminjamans = Peminjaman::with('barang')
            ->when($search, function ($query, $search) {
                $query->where('kode_peminjaman', 'like', "%$search%")
                    ->orWhere('peminjam', 'like', "%$search%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        $barang = Barang::where('status_peminjaman', 'Bisa Dipinjam')
        ->whereRaw('(kondisi_baik + kondisi_rusak_ringan - jumlah_dipinjam) > 0')
        ->with(['kategori', 'lokasi'])
        ->get()
        ->map(function($barang) {
            // Hitung jumlah tersedia (tidak termasuk rusak berat dan yang sedang dipinjam)
            $barang->jumlah_tersedia = ($barang->kondisi_baik + $barang->kondisi_rusak_ringan) - $barang->jumlah_dipinjam;
            return $barang;
        });
    
        $peminjaman = new Peminjaman();

        return view('peminjaman.create', compact('peminjaman', 'barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_peminjaman' => 'required|string|max:50|unique:peminjamans,kode_peminjaman',
            'peminjam' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:20',
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        
    $barang = Barang::findOrFail($request->barang_id);

        // Cek apakah barang bisa dipinjam
        if ($barang->status_peminjaman != 'Bisa Dipinjam') {
            return back()->with('error', 'Barang ini tidak bisa dipinjam!');
        }
        
        // Hitung jumlah tersedia (tidak termasuk rusak berat dan yang sedang dipinjam)
        $jumlahTersedia = ($barang->kondisi_baik + $barang->kondisi_rusak_ringan) - $barang->jumlah_dipinjam;
        
        // Validasi stok tersedia (PERBAIKAN DI SINI)
        if ($request->jumlah > $jumlahTersedia) {
            return back()->withErrors(['jumlah' => 'Jumlah pinjam melebihi stok yang tersedia. Stok tersedia: ' . $jumlahTersedia . ' ' . $barang->satuan . ' (tidak termasuk rusak berat)'])
                        ->withInput();
        }

        $validated['status'] = 'dipinjam';

        Peminjaman::create($validated);

        // Update jumlah dipinjam di barang
        $barang->increment('jumlah_dipinjam', $request->jumlah);


        return redirect()->route('peminjaman.index')
                        ->with('success', 'Peminjaman berhasil ditambahkan.');
    }

    public function edit(Peminjaman $peminjaman)
    {
    
    // Ambil hanya barang yang bisa dipinjam dan punya kondisi baik/rusak ringan
        $barang = Barang::where('status_peminjaman', 'Bisa Dipinjam')
            ->whereRaw('(kondisi_baik + kondisi_rusak_ringan - jumlah_dipinjam) > 0')
            ->orWhere('id', $peminjaman->barang_id) // Tambahkan barang yang sedang dipinjam
            ->with(['kategori', 'lokasi'])
            ->get()
            ->map(function($barang) use ($peminjaman) {
                // Hitung jumlah tersedia (tidak termasuk rusak berat dan yang sedang dipinjam)
                $barang->jumlah_tersedia = ($barang->kondisi_baik + $barang->kondisi_rusak_ringan) - $barang->jumlah_dipinjam;
                
                // Jika ini barang yang sedang diedit, tambahkan kembali jumlah yang dipinjam
                if ($barang->id == $peminjaman->barang_id) {
                    $barang->jumlah_tersedia += $peminjaman->jumlah;
                }
                
                return $barang;
            })
            ->filter(function($barang) {
                // Filter hanya barang dengan stok tersedia > 0
                return $barang->jumlah_tersedia > 0;
            });
    
    return view('peminjaman.edit', compact('peminjaman', 'barang'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
        'peminjam' => 'required|string|max:150',
        'no_telp' => 'nullable|string|max:20',
        'barang_id' => 'required|exists:barangs,id',
        'jumlah' => 'required|integer|min:1',
        'tanggal_pinjam' => 'required|date',
        'tanggal_kembali' => 'nullable|date',
    ]);

    $barang = Barang::findOrFail($request->barang_id);
    
    // Hitung jumlah tersedia (tidak termasuk rusak berat)
        $jumlahTersedia = ($barang->kondisi_baik + $barang->kondisi_rusak_ringan) - $barang->jumlah_dipinjam;
        
        // Jika barang yang sama, tambahkan kembali jumlah peminjaman lama
        if ($peminjaman->barang_id == $request->barang_id) {
            $jumlahTersedia += $peminjaman->jumlah;
        }
        
        // Validasi stok tersedia (PERBAIKAN DI SINI)
        if ($request->jumlah > $jumlahTersedia) {
            return back()->withErrors(['jumlah' => 'Jumlah pinjam melebihi stok yang tersedia. Stok tersedia: ' . $jumlahTersedia . ' ' . $barang->satuan . ' (tidak termasuk rusak berat)'])
                        ->withInput();
        }

        // Jika ganti barang, update jumlah_dipinjam di barang lama dan baru
        if ($peminjaman->barang_id != $request->barang_id) {
            // Kurangi di barang lama
            $barangLama = Barang::findOrFail($peminjaman->barang_id);
            $barangLama->decrement('jumlah_dipinjam', $peminjaman->jumlah);
            
            // Tambah di barang baru
            $barang->increment('jumlah_dipinjam', $request->jumlah);
        } 
        // Jika barang sama tapi jumlah berubah
        elseif ($peminjaman->jumlah != $request->jumlah) {
            $selisih = $request->jumlah - $peminjaman->jumlah;
            if ($selisih > 0) {
                $barang->increment('jumlah_dipinjam', $selisih);
            } else {
                $barang->decrement('jumlah_dipinjam', abs($selisih));
            }
        }

    $peminjaman->update($validated);

    return redirect()->route('peminjaman.index')
                    ->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function kembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Barang sudah dikembalikan.');
        }

        // Kurangi jumlah dipinjam di barang
    $barang = Barang::findOrFail($peminjaman->barang_id);
    $barang->decrement('jumlah_dipinjam', $peminjaman->jumlah);

    $peminjaman->update([
        'status' => 'sudah dikembalikan',
        'tanggal_kembali' => now(),
    ]);

        return redirect()->route('peminjaman.index')
                        ->with('success', 'Barang berhasil dikembalikan.');
    
    }

public function cetakLaporan()
{
    $peminjamans = Peminjaman::with(['barang', 'user'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

    $data = [
        'title' => 'Laporan Data Peminjaman Barang',
        'date' => date('d F Y'),
        'peminjamans' => $peminjamans
    ];

    $pdf = Pdf::loadView('peminjaman.laporan', $data);
    return $pdf->stream('laporan-peminjaman.pdf');
}

}
