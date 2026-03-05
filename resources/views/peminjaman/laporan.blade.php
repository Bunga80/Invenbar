<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('peminjaman.partials.style-laporan')
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>
    
    @include('peminjaman.partials.list-laporan')

</body>
</html>