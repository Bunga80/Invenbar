<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Status Peminjaman</th>
            <th>Kondisi</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>
    
    @forelse ($barangs as $index => $barang)
        <tr>
            <td>{{ $barangs->firstItem() + $index }}</td>
            <td>{{ $barang->kode_barang }}</td>
            <td>{{ $barang->nama_barang }}</td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
            <td>
    {{ $barang->jumlah }} {{ $barang->satuan }}
    @if($barang->jumlah_dipinjam > 0)
        ({{ $barang->jumlah_dipinjam }} dipinjam)
    @endif
            </td>
            <td>
                <!-- TAMPILKAN STATUS PEMINJAMAN -->
                @if($barang->status_peminjaman == 'Bisa Dipinjam')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Bisa Dipinjam
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle"></i> Tidak Bisa Dipinjam
                    </span>
                @endif
            </td>
            <td>
    <div class="d-flex align-items-center gap-2">
        @if(isset($barang->kondisi_baik) && $barang->kondisi_baik > 0)
        <span style="display: inline-block; width: 15px; height: 15px; background-color: #0dcaf0; border-radius: 50%;" title="Baik: {{ $barang->kondisi_baik }}"></span>
        @endif
        
        @if(isset($barang->kondisi_rusak_ringan) && $barang->kondisi_rusak_ringan > 0)
        <span style="display: inline-block; width: 15px; height: 15px; background-color: #ffc107; border-radius: 50%;" title="Rusak Ringan: {{ $barang->kondisi_rusak_ringan }}"></span>
        @endif
        
        @if(isset($barang->kondisi_rusak_berat) && $barang->kondisi_rusak_berat > 0)
        <span style="display: inline-block; width: 15px; height: 15px; background-color: #dc3545; border-radius: 50%;" title="Rusak Berat: {{ $barang->kondisi_rusak_berat }}"></span>
        @endif
    </div>
</td>
            <td class="text-end">
                @can('manage barang')
                    <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                    <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                    <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                @endcan
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-danger">
                    Data barang belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>