<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Peminjam</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($peminjamans as $i => $p)
            @php
            
            if ($p->status === 'Sudah dikembalikan') {
                $statusClass = 'status-dikembalikan';
            } elseif ($p->status === 'Dipinjam') {
                if ($p->tanggal_kembali && \Carbon\Carbon::parse($p->tanggal_kembali)->isPast()) {
                    $statusClass = 'status-terlambat';
                } else {
                    $statusClass = 'status-dipinjam';
                }
            } else {
                $statusClass = 'status-terlambat';
            }
        @endphp
        <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->kode_peminjaman }}</td>
                <td>{{ $p->peminjam }}</td>
                <td>{{ $p->barang->nama_barang }}</td>
                <td>{{ $p->jumlah }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d-m-Y') }}</td>
<td>
    {{ $p->tanggal_kembali 
        ? \Carbon\Carbon::parse($p->tanggal_kembali)->format('d-m-Y') 
        : '-' }}
</td>

                <td>{{ ucfirst($p->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

