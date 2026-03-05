<x-main-layout :title-page="__('Barang')">

    <!-- Keterangan Kondisi Barang -->
            <div class="mt-3 p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #4CAF50;">
                <div class="d-flex gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle" style="background-color: #00bcd4; width: 16px; height: 16px; display: inline-block;"></span>
                        <span style="font-size: 1rem; font-weight: 500;">Baik</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle" style="background-color: #ffc107; width: 16px; height: 16px; display: inline-block;"></span>
                        <span style="font-size: 1rem; font-weight: 500;">Rusak Ringan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle" style="background-color: #f44336; width: 16px; height: 16px; display: inline-block;"></span>
                        <span style="font-size: 1rem; font-weight: 500;">Rusak Berat</span>
                    </div>
                </div>
            </div><br>

    <div class="card">
        <div class="card-body">
            @include('barang.partials.toolbar')
            <x-notif-alert class="mt-4" />
        </div>

        @include('barang.partials.list-barang')

        <div class="card-body">
            {{ $barangs->links() }}
        </div>
    </div>
</x-main-layout>