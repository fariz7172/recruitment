@php
    // Circle Increase Pattern - lingkaran bertambah
    $circles = [1, 2, 3, 4, 5];
    $answersData = ['A' => 4, 'B' => 3, 'C' => 6, 'D' => 5];
    
    if (isset($answer)) {
        $count = $answersData[$answer] ?? 5;
    } else {
        $count = $circles[$step - 1] ?? 1;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @for($i = 0; $i < $count; $i++)
            <circle cx="{{ 15 + ($i % 3) * 15 }}" cy="{{ 20 + floor($i / 3) * 20 }}" r="5" fill="black"/>
        @endfor
    </svg>
</div>
