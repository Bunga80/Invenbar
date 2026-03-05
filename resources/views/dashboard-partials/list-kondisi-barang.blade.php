<div class="card-body">
    @php
        $kondisis = [
            [
                'judul' => 'Baik',
                'jumlah' => $jumlahBarang,
                'kondisi' => $kondisiBaik,
                'color' => 'success'
            ],
            [
                'judul' => 'Rusak Ringan',
                'jumlah' => $jumlahBarang,
                'kondisi' => $kondisiRusakRingan,
                'color' => 'warning'
            ],
            [
                'judul' => 'Rusak Berat',
                'jumlah' => $jumlahBarang,
                'kondisi' => $kondisiRusakBerat,
                'color' => 'danger'
            ]
        ];
    @endphp

    @foreach($kondisis as $kondisi)
        @php
            extract($kondisi);
        @endphp
        @php
    // Hitung persentase berdasarkan batas maksimal 50
    $maxValue = 100;
    $percentage = ($kondisi / $maxValue) * 100;
    
    // Batasi maksimal 100%
    if ($percentage > 100) {
        $percentage = 100;
    }
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex align-items-center gap-2">
            @php
                $colorMap = [
                    'success' => '#0dcaf0',
                    'warning' => '#ffc107',
                    'danger' => '#dc3545'
                ];
                $bgColor = $colorMap[$color] ?? '#6c757d';
            @endphp
            <span class="rounded-circle" style="background-color: {{ $bgColor }}; width: 16px; height: 16px; display: inline-block;"></span>
            <span>{{ $judul }}</span>
        </div>
        <span class="fw-bold">{{ $kondisi }}</span>
    </div>
    
    <div class="progress" style="height: 20px;">
        <div class="progress-bar bg-{{ $color }}" 
             role="progressbar" 
             style="width: {{ $percentage }}%;" 
             aria-valuenow="{{ $kondisi }}" 
             aria-valuemin="0" 
             aria-valuemax="{{ $maxValue }}">
        </div>
    </div>
    </div>
    @endforeach
</div>