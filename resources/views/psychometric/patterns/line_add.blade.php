@php
    // Line Add Pattern - garis bertambah
    $lines = [1, 2, 3, 4, 5];
    $answersData = ['A' => 5, 'B' => 4, 'C' => 6, 'D' => 3];
    
    if (isset($answer)) {
        $count = $answersData[$answer] ?? 5;
    } else {
        $count = $lines[$step - 1] ?? 1;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @for($i = 0; $i < $count; $i++)
            <line x1="15" y1="{{ 15 + $i * 8 }}" x2="45" y2="{{ 15 + $i * 8 }}" stroke="black" stroke-width="2"/>
        @endfor
    </svg>
</div>
