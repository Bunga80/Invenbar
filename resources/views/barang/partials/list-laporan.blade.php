<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>Tgl. Pengadaan</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($barangs as $index => $barang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $barang->kode_barang }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->kategori->nama_kategori }}</td>
                <td>{{ $barang->lokasi->nama_lokasi }}</td>
                <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
                <td>
    <div class="kondisi-detail">
        @php
            $kondisiLines = [];
            
            if(isset($barang->kondisi_baik) && $barang->kondisi_baik > 0) {
                $kondisiLines[] = "Baik = {$barang->kondisi_baik}";
            }
            
            if(isset($barang->kondisi_rusak_ringan) && $barang->kondisi_rusak_ringan > 0) {
                $kondisiLines[] = "Rusak Ringan = {$barang->kondisi_rusak_ringan}";
            }
            
            if(isset($barang->kondisi_rusak_berat) && $barang->kondisi_rusak_berat > 0) {
                $kondisiLines[] = "Rusak Berat = {$barang->kondisi_rusak_berat}";
            }
        @endphp
        
        @foreach($kondisiLines as $line)
            {{ $line }}@if(!$loop->last)<br>@endif
        @endforeach
    </div>
</td>
                <td>
                    {{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data.</td>
            </tr>
        @endforelse
    </tbody>
</table>