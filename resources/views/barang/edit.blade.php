<x-main-layout :title-page="__('Edit Barang')">
    <form class="card" action="{{ route('barang.update', $barang->id) }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @csrf
            @method('PUT')
            @include('barang.partials._form', ['update' => true])
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
