<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th style="width: 30%">Nama Barang</th>
            <td>{{ $barang->nama_barang }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategori->nama_kategori }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
        </tr>
        <tr>
            <th>Status Peminjaman</th>
            <td>
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
        </tr>
        <tr>
            <th>Sumber Dana</th>
            <td>{{ $barang->sumber_dana ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>
            @php
                $badgeClass = 'bg-success';
                if ($barang->kondisi == 'Rusak Ringan') {
                    $badgeClass = 'bg-warning text-dark';
                }
                if ($barang->kondisi == 'Rusak Berat') {
                    $badgeClass = 'bg-danger';
                }
            @endphp
                <span class="badge {{ $badgeClass }}">{{ $barang->kondisi }}</span>
            </td>
        </tr>
        <tr>
            <th>Tanggal Pengadaan</th>
            <td>{{ \Carbon\Carbon::parse($barang->tanggal_pengadaan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ $barang->updated_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>
    </tbody>
</table>

<div class="mt-4">
    <h5 class="mb-3">Detail Kondisi Per Unit Barang</h5>
    
    @if($barang->detailBarangs->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">Kode Unit</th>
                        <th style="width: 30%">Nama Barang</th>
                        <th style="width: 20%">Kondisi</th>
                        @if($barang->status_peminjaman == 'Bisa Dipinjam')
                        <th style="width: 20%">Status Peminjaman</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang->detailBarangs as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $detail->kode_unit }}</strong></td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>
                            @php
                                $badgeClass = 'bg-success';
                                $iconClass = 'bi-check-circle-fill';
                                if ($detail->kondisi == 'Rusak Ringan') {
                                    $badgeClass = 'bg-warning text-dark';
                                    $iconClass = 'bi-exclamation-triangle-fill';
                                }
                                if ($detail->kondisi == 'Rusak Berat') {
                                    $badgeClass = 'bg-danger';
                                    $iconClass = 'bi-x-circle-fill';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                <i class="bi {{ $iconClass }} me-1"></i>
                                {{ $detail->kondisi }}
                            </span>
                        </td>
                        @if($barang->status_peminjaman == 'Bisa Dipinjam')
                        <td>
                            @if($detail->bisaDipinjam())
                                <span class="badge bg-success">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Bisa Dipinjam
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-lg me-1"></i>
                                    Tidak Bisa Dipinjam
                                </span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>Ringkasan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Total {{ $barang->satuan }}: <strong>{{ $barang->detailBarangs->count() }}</strong></li>
                        <li>Kondisi Baik: <strong>{{ $barang->detailBarangs->where('kondisi', 'Baik')->count() }}</strong></li>
                        <li>Kondisi Rusak Ringan: <strong>{{ $barang->detailBarangs->where('kondisi', 'Rusak Ringan')->count() }}</strong></li>
                        <li>Kondisi Rusak Berat: <strong>{{ $barang->detailBarangs->where('kondisi', 'Rusak Berat')->count() }}</strong></li>
                        @if($barang->status_peminjaman == 'Bisa Dipinjam')
                        <li>Bisa Dipinjam: <strong>{{ $barang->detailBarangs->filter(fn($d) => $d->bisaDipinjam())->count() }}</strong></li>
                        <li>Tidak Bisa Dipinjam: <strong>{{ $barang->detailBarangs->where('kondisi', 'Rusak Berat')->count() }}</strong></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Belum ada detail kondisi barang. Silakan tambahkan melalui halaman edit.
        </div>
    @endif
</div>