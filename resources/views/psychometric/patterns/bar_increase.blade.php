@php
    // Bar Increase Pattern - batang meningkat
    $heights = [[10], [10,20], [10,20,30], [10,20,30,40], [10,20,30,40,50]];
    $answersData = [
        'A' => [10,20,30,40], 
        'B' => [10,20,30,40,50], 
        'C' => [10,20,30,40,40], 
        'D' => [10,20,30,40,45]
    ];
    
    if (isset($answer)) {
        $bars = $answersData[$answer] ?? [10,20,30,40,50];
    } else {
        $bars = $heights[$step - 1] ?? [10];
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @foreach($bars as $i => $h)
            <rect x="{{ 10 + $i * 9 }}" y="{{ 50 - $h }}" width="7" height="{{ $h }}" fill="black"/>
        @endforeach
    </svg>
</div>
