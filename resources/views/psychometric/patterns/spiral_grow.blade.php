@php
    // Spiral Grow Pattern - spiral membesar
    $turns = [1, 2, 3, 4, 5];
    $answersData = ['A' => 5, 'B' => 4, 'C' => 6, 'D' => 3];
    
    if (isset($answer)) {
        $turn = $answersData[$answer] ?? 5;
    } else {
        $turn = $turns[$step - 1] ?? 1;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <path d="M 30 30 
            @for($i = 1; $i <= $turn; $i++)
                @php
                    $radius = $i * 4;
                    $angle = $i * 90;
                @endphp
                A {{ $radius }} {{ $radius }} 0 0 1 {{ 30 + cos(deg2rad($angle)) * $radius }} {{ 30 + sin(deg2rad($angle)) * $radius }}
            @endfor
        " stroke="black" fill="none" stroke-width="2"/>
    </svg>
</div>
