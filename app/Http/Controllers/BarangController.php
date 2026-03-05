<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
        ];
    }


    private function syncDetailBarangs(Barang $barang, $kondisiBaik, $kondisiRusakRingan, $kondisiRusakBerat)
    {
        // Hapus detail barang yang ada
        $barang->detailBarangs()->delete();
        
        $counter = 1;
        
        // Buat detail untuk kondisi Baik
        for ($i = 0; $i < $kondisiBaik; $i++) {
            DetailBarang::create([
                'barang_id' => $barang->id,
                'kode_unit' => $barang->kode_barang . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                'kondisi' => 'Baik',
            ]);
        }
        
        // Buat detail untuk kondisi Rusak Ringan
        for ($i = 0; $i < $kondisiRusakRingan; $i++) {
            DetailBarang::create([
                'barang_id' => $barang->id,
                'kode_unit' => $barang->kode_barang . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                'kondisi' => 'Rusak Ringan',
            ]);
        }
        
        // Buat detail untuk kondisi Rusak Berat
        for ($i = 0; $i < $kondisiRusakBerat; $i++) {
            DetailBarang::create([
                'barang_id' => $barang->id,
                'kode_unit' => $barang->kode_barang . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                'kondisi' => 'Rusak Berat',
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $barangs = Barang::with(['kategori', 'lokasi'])
            ->withSum(['peminjamans as jumlah_dipinjam' => function ($q) {
                $q->where('status', 'dipinjam');
            }], 'jumlah')
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $search . '%');
            })
            ->when($request->kondisi, function ($query, $kondisi) {
            if ($kondisi === 'baik') {
                $query->where('kondisi_baik', '>', 0);
            } elseif ($kondisi === 'rusak_ringan') {
                $query->where('kondisi_rusak_ringan', '>', 0);
            } elseif ($kondisi === 'rusak_berat') {
                $query->where('kondisi_rusak_berat', '>', 0);
            }
        })
        ->latest()
        ->paginate()
        ->withQueryString();

        return view('barang.index', compact('barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        
        $barang = new Barang();

        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'kondisi_baik' => 'nullable|integer|min:0',
            'kondisi_rusak_ringan' => 'nullable|integer|min:0',
            'kondisi_rusak_berat' => 'nullable|integer|min:0',
            'status_peminjaman' => 'required|in:Bisa Dipinjam,Tidak Bisa Dipinjam',
            'sumber_dana' => 'nullable|string',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Set default 0 untuk kondisi yang kosong
        $kondisiBaik = $request->kondisi_baik ?? 0;
        $kondisiRusakRingan = $request->kondisi_rusak_ringan ?? 0;
        $kondisiRusakBerat = $request->kondisi_rusak_berat ?? 0;
        
        // Cek apakah semua kondisi kosong
        if ($kondisiBaik == 0 && $kondisiRusakRingan == 0 && $kondisiRusakBerat == 0) {
            return back()->withErrors([
                'kondisi_error' => 'Minimal satu kondisi barang harus diisi!'
            ])->withInput();
        }

        $totalJumlah = $kondisiBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // Set nilai kondisi ke validated array
        $validated['kondisi_baik'] = $kondisiBaik;
        $validated['kondisi_rusak_ringan'] = $kondisiRusakRingan;
        $validated['kondisi_rusak_berat'] = $kondisiRusakBerat;
        $validated['jumlah'] = $totalJumlah;

        // Tentukan kondisi utama (yang paling banyak)
        $kondisiUtama = 'Baik';
        $maxKondisi = $kondisiBaik;
        
        if ($kondisiRusakRingan > $maxKondisi) {
            $kondisiUtama = 'Rusak Ringan';
            $maxKondisi = $kondisiRusakRingan;
        }
        
        if ($kondisiRusakBerat > $maxKondisi) {
            $kondisiUtama = 'Rusak Berat';
        }
        
        $validated['kondisi'] = $kondisiUtama;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }

        $barang = Barang::create($validated);

        // TAMBAHAN: Generate detail barang otomatis
        $this->syncDetailBarangs($barang, $kondisiBaik, $kondisiRusakRingan, $kondisiRusakBerat);

        return redirect()->route('barang.index')
                        ->with('success', 'Data barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi', 'detailBarangs']);

        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();
        $barang->load('detailBarangs');

        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
{
    $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'kondisi_baik' => 'nullable|integer|min:0',
            'kondisi_rusak_ringan' => 'nullable|integer|min:0',
            'kondisi_rusak_berat' => 'nullable|integer|min:0',
            'status_peminjaman' => 'required|in:Bisa Dipinjam,Tidak Bisa Dipinjam',
            'sumber_dana' => 'nullable|string',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

    // Set default 0 untuk kondisi yang kosong
        $kondisiBaik = $request->kondisi_baik ?? 0;
        $kondisiRusakRingan = $request->kondisi_rusak_ringan ?? 0;
        $kondisiRusakBerat = $request->kondisi_rusak_berat ?? 0;

        // Cek apakah semua kondisi kosong
        if ($kondisiBaik == 0 && $kondisiRusakRingan == 0 && $kondisiRusakBerat == 0) {
            return back()->withErrors([
                'kondisi_error' => 'Minimal satu kondisi barang harus diisi!'
            ])->withInput();
        }

        $totalJumlah = $kondisiBaik + $kondisiRusakRingan + $kondisiRusakBerat;

        // Set nilai kondisi ke validated array
        $validated['kondisi_baik'] = $kondisiBaik;
        $validated['kondisi_rusak_ringan'] = $kondisiRusakRingan;
        $validated['kondisi_rusak_berat'] = $kondisiRusakBerat;
        $validated['jumlah'] = $totalJumlah;

        // Tentukan kondisi utama (yang paling banyak)
        $kondisiUtama = 'Baik';
        $maxKondisi = $kondisiBaik;
        
        if ($kondisiRusakRingan > $maxKondisi) {
            $kondisiUtama = 'Rusak Ringan';
            $maxKondisi = $kondisiRusakRingan;
        }
        
        if ($kondisiRusakBerat > $maxKondisi) {
            $kondisiUtama = 'Rusak Berat';
        }
        
        $validated['kondisi'] = $kondisiUtama;

        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('gambar-barang')->delete($barang->gambar);
            }
            
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }

        $barang->update($validated);

        // TAMBAHAN: Sync ulang detail barang
        $this->syncDetailBarangs($barang, $kondisiBaik, $kondisiRusakRingan, $kondisiRusakBerat);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->gambar) {
            Storage::disk('gambar-barang')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }

    public function cetakLaporan()
    {
        $barangs = Barang::with(['kategori', 'lokasi'])->get();

        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs
        ];

        $pdf = Pdf::loadView('barang.laporan', $data);

        return $pdf->stream('laporan-inventaris-barang.pdf');
    }
}