@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="Kode Peminjaman" name="kode_peminjaman" :value="$peminjaman->kode_peminjaman ?? old('kode_peminjaman')" />
    </div>
    <div class="col-md-6">
        <x-form-input label="Nama Peminjam" name="peminjam" :value="$peminjaman->peminjam ?? old('peminjam')" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="barang_id" class="form-label">Barang <span class="text-danger">*</span></label>
        <select name="barang_id" id="barang_id" class="form-select @error('barang_id') is-invalid @enderror" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $item)
                {{-- Controller sudah filter dan hitung jumlah_tersedia --}}
                <option value="{{ $item->id }}" 
                        data-stok="{{ $item->jumlah_tersedia }}"
                        data-satuan="{{ $item->satuan }}"
                        data-baik="{{ $item->kondisi_baik }}"
                        data-rusak-ringan="{{ $item->kondisi_rusak_ringan }}"
                        {{ (($peminjaman->barang_id ?? old('barang_id')) == $item->id) ? 'selected' : '' }}>
                    {{ $item->kode_barang }} - {{ $item->nama_barang }} 
                    (Tersedia: {{ $item->jumlah_tersedia }} {{ $item->satuan }})
                </option>
            @endforeach
        </select>
        @error('barang_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> Hanya menampilkan barang yang bisa dipinjam (tidak termasuk rusak berat)
        </small>
    </div>

    <div class="col-md-6">
        <label for="jumlah" class="form-label">Jumlah Dipinjam <span class="text-danger">*</span></label>
        <input type="number" 
               name="jumlah" 
               id="jumlah" 
               class="form-control @error('jumlah') is-invalid @enderror" 
               value="{{ $peminjaman->jumlah ?? old('jumlah') }}"
               min="1"
               max=""
               required>
        @error('jumlah')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted" id="stok-info">
            <i class="bi bi-box"></i> Pilih barang terlebih dahulu
        </small>
    </div>
</div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        @php
            $tgl_pinjam = $peminjaman->tanggal_pinjam ?? old('tanggal_pinjam');
            $tgl_pinjam = $tgl_pinjam ? date('d-m-Y', strtotime($tgl_pinjam)) : null;
        @endphp
        <x-form-input label="Tanggal Pinjam" name="tanggal_pinjam" type="date" :value="$tgl_pinjam" />
    </div>
    <div class="col-md-6">
        @php
            $tgl_kembali = $peminjaman->tanggal_kembali ?? old('tanggal_kembali');
            $tgl_kembali = $tgl_kembali ? date('d-m-Y', strtotime($tgl_kembali)) : null;
        @endphp
        <x-form-input label="Tanggal Kembali" name="tanggal_kembali" type="date" :value="$tgl_kembali" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-form-input label="No. Telp" name="no_telp" :value="$peminjaman->no_telp ?? old('no_telp')" />
    </div>
</div>

<div class="mt-4">
    <x-primary-button type="submit">
        {{ isset($update) ? __('Update') : __('Simpan') }}
    </x-primary-button>
    <x-tombol-kembali :href="route('peminjaman.index')" />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangSelect = document.getElementById('barang_id');
    const jumlahInput = document.getElementById('jumlah');
    const stokInfo = document.getElementById('stok-info');

    // Fungsi untuk update max dan info stok
    function updateStokInfo() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        
        if (selectedOption.value) {
            const stokTersedia = parseInt(selectedOption.dataset.stok);
            const satuan = selectedOption.dataset.satuan || 'unit';
            const baik = parseInt(selectedOption.dataset.baik || 0);
            const rusakRingan = parseInt(selectedOption.dataset.rusakRingan || 0);
            
            // Set max attribute
            jumlahInput.max = stokTersedia;
            
            // Update info text
            stokInfo.innerHTML = `
                <i class="bi bi-box"></i> Stok tersedia: <strong>${stokTersedia} ${satuan}</strong> 
                <span class="text-muted">(Baik: ${baik}, Rusak Ringan: ${rusakRingan})</span>
            `;
            
            // Validasi jika jumlah saat ini melebihi stok
            if (parseInt(jumlahInput.value) > stokTersedia) {
                jumlahInput.value = stokTersedia;
            }
        } else {
            jumlahInput.max = '';
            stokInfo.innerHTML = '<i class="bi bi-box"></i> Pilih barang terlebih dahulu';
        }
    }

    // Event listener saat pilih barang
    barangSelect.addEventListener('change', updateStokInfo);

    // Event listener saat input jumlah
    jumlahInput.addEventListener('input', function() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        
        if (selectedOption.value) {
            const stokTersedia = parseInt(selectedOption.dataset.stok);
            const satuan = selectedOption.dataset.satuan || 'unit';
            
            if (parseInt(this.value) > stokTersedia) {
                this.value = stokTersedia;
                alert(`Jumlah tidak boleh melebihi stok tersedia (${stokTersedia} ${satuan})`);
            }
        }
    });

    // Initialize saat halaman load (untuk mode edit)
    if (barangSelect.value) {
        updateStokInfo();
    }
});
</script>
@endpush