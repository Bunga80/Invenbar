<x-main-layout :title-page="__('Tambah Peminjaman')">
    <form class="card" action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            @include('peminjaman.partials._form')
        </div>
    </form>
</x-main-layout>