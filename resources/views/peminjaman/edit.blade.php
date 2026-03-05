<x-main-layout :title-page="__('Edit Peminjaman')">
    <form class="card" action="{{ route('peminjaman.update', $peminjaman->id) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @csrf
            @method('PUT')
            @include('peminjaman.partials._form', ['update' => true])
        </div>
    </form>
</x-main-layout>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
