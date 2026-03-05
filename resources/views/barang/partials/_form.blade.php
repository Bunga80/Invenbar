@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Kode Barang" name="kode_barang" :value="$barang->kode_barang" />
    </div>
    <div class="col-md-6">
        <x-form-input label="Nama Barang" name="nama_barang" :value="$barang->nama_barang" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-select label="Kategori" name="kategori_id" :value="$barang->kategori_id" :option-data="$kategori" option-label="nama_kategori" option-value="id" />
    </div>
    <div class="col-md-6">
        <x-form-select label="Lokasi" name="lokasi_id" :value="$barang->lokasi_id" :option-data="$lokasi" option-label="nama_lokasi" option-value="id" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Jumlah Total (Otomatis)" name="jumlah_display" :value="$barang->jumlah" type="number" readonly />
        <small class="text-muted">Total akan dihitung otomatis dari detail kondisi di bawah</small>
    </div>

    <div class="col-md-6">
        <x-form-input label="Satuan" name="satuan" :value="$barang->satuan" />
    </div>
</div>

<!-- BAGIAN DETAIL KONDISI BARANG -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="bi bi-clipboard-check me-2"></i>Detail Kondisi Barang
        </h6>
    </div>
    <div class="card-body">
        <!-- ====== TAMBAHKAN ALERT ERROR DI SINI ====== -->
        @if($errors->has('kondisi_error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Perhatian!</strong> {{ $errors->first('kondisi_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <!-- ====== AKHIR ALERT ERROR ====== -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Informasi:</strong> Masukkan jumlah untuk setiap kondisi barang. <strong>Minimal satu kondisi harus diisi.</strong> Total akan dihitung otomatis.
        </div>

        <div class="row g-3">
            <!-- Kondisi Baik -->
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light kondisi-card" id="cardBaik">
                    <div class="text-center mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold mt-2">Kondisi Baik</h6>
                        <small class="text-muted">Berfungsi dengan baik</small>
                    </div>
                    <label class="form-label fw-bold">Jumlah Unit</label>
                    <input type="number" 
                           class="form-control form-control-lg text-center kondisi-input" 
                           name="kondisi_baik" 
                           id="kondisiBaik"
                           value="{{ old('kondisi_baik', $barang->kondisi_baik ?? 0) }}" 
                           min="0" 
                           placeholder="0">
                </div>
            </div>

            <!-- Kondisi Rusak Ringan -->
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light kondisi-card" id="cardRusakRingan">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold mt-2">Rusak Ringan</h6>
                        <small class="text-muted">Perlu perbaikan ringan</small>
                    </div>
                    <label class="form-label fw-bold">Jumlah Unit</label>
                    <input type="number" 
                           class="form-control form-control-lg text-center kondisi-input" 
                           name="kondisi_rusak_ringan" 
                           id="kondisiRusakRingan"
                           value="{{ old('kondisi_rusak_ringan', $barang->kondisi_rusak_ringan ?? 0) }}" 
                           min="0" 
                           placeholder="0">
                </div>
            </div>

            <!-- Kondisi Rusak Berat -->
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light kondisi-card" id="cardRusakBerat">
                    <div class="text-center mb-3">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold mt-2">Rusak Berat</h6>
                        <small class="text-muted">Tidak dapat digunakan</small>
                    </div>
                    <label class="form-label fw-bold">Jumlah Unit</label>
                    <input type="number" 
                           class="form-control form-control-lg text-center kondisi-input" 
                           name="kondisi_rusak_berat" 
                           id="kondisiRusakBerat"
                           value="{{ old('kondisi_rusak_berat', $barang->kondisi_rusak_berat ?? 0) }}" 
                           min="0" 
                           placeholder="0">
                </div>
            </div>
        </div>

        <!-- Total Summary -->
        <div class="alert alert-success mt-3 mb-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-calculator me-2"></i>Total Jumlah Barang
                    </h6>
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="mb-0 fw-bold">
                        <span id="totalKondisi">{{ old('kondisi_baik', $barang->kondisi_baik ?? 0) + old('kondisi_rusak_ringan', $barang->kondisi_rusak_ringan ?? 0) + old('kondisi_rusak_berat', $barang->kondisi_rusak_berat ?? 0) }}</span> Unit
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-3">
    <div class="col-md-6">
        @php
            $sumberDana = [
                ['nama' => 'Donatur'],
                ['nama' => 'Pemerintah'],
                ['nama' => 'Swadaya'],
            ];
        @endphp
        <x-form-select label="Sumber Barang" name="sumber_dana" :value="$barang->sumber_dana" :option-data="$sumberDana" option-label="nama" option-value="nama" />
    </div>
    <div class="col-md-6">
        @php
            $tanggal = $barang->tanggal_pengadaan ? date('Y-m-d', strtotime($barang->tanggal_pengadaan)) : null;
        @endphp
        <x-form-input label="Tanggal Pengadaan" name="tanggal_pengadaan" type="date" :value="$tanggal" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Gambar Barang" name="gambar" type="file" />
        @if($barang->gambar)
        <div class="mt-2">
            <img src="{{ asset('storage/gambar-barang/' . $barang->gambar) }}" alt="Gambar Barang" class="img-thumbnail" style="max-width: 200px;">
        </div>
        @endif
    </div>
    <div class="col-md-6">
        @php
            $statusPeminjaman = [
                ['nama' => 'Bisa Dipinjam'],
                ['nama' => 'Tidak Bisa Dipinjam'],
            ];
        @endphp
        <x-form-select label="Status Peminjaman" name="status_peminjaman" :value="$barang->status_peminjaman ?? 'Bisa Dipinjam'" :option-data="$statusPeminjaman" option-label="nama" option-value="nama" />
    </div>
</div>

<div class="mt-4">
    <x-primary-button type="submit">
        {{ isset($update) ? __('Update') : __('Simpan') }}
    </x-primary-button>
    <x-tombol-kembali :href="route('barang.index')" />
</div>

@push('styles')
<style>
    .kondisi-card {
        transition: all 0.3s ease;
    }
    .kondisi-card.active {
        border-color: #0d6efd !important;
        background-color: #e7f1ff !important;
        border-width: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kondisiBaikInput = document.getElementById('kondisiBaik');
    const kondisiRusakRinganInput = document.getElementById('kondisiRusakRingan');
    const kondisiRusakBeratInput = document.getElementById('kondisiRusakBerat');
    const totalDisplay = document.getElementById('totalKondisi');
    const jumlahDisplay = document.querySelector('input[name="jumlah_display"]');

    function hitungTotal() {
        const baik = parseInt(kondisiBaikInput.value) || 0;
        const rusakRingan = parseInt(kondisiRusakRinganInput.value) || 0;
        const rusakBerat = parseInt(kondisiRusakBeratInput.value) || 0;
        
        const total = baik + rusakRingan + rusakBerat;
        
        totalDisplay.textContent = total;
        if (jumlahDisplay) {
            jumlahDisplay.value = total;
        }

        // Highlight card yang memiliki nilai
        highlightCard('cardBaik', baik);
        highlightCard('cardRusakRingan', rusakRingan);
        highlightCard('cardRusakBerat', rusakBerat);
    }

    function highlightCard(cardId, value) {
        const card = document.getElementById(cardId);
        if (value > 0) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    }

    // Event listener untuk setiap input
    kondisiBaikInput.addEventListener('input', hitungTotal);
    kondisiRusakRinganInput.addEventListener('input', hitungTotal);
    kondisiRusakBeratInput.addEventListener('input', hitungTotal);

    // Hitung total saat halaman dimuat
    hitungTotal();

    // Validasi sebelum submit
    const form = kondisiBaikInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const total = parseInt(totalDisplay.textContent);
            if (total === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 unit barang dengan kondisi tertentu!');
                return false;
            }
        });
    }
});
</script>
@endpush