@php
    // Domino Pattern - pola domino
    $dots = [[1,1], [2,2], [3,3], [4,4], [5,5]];
    $answersData = ['A' => [4,4], 'B' => [5,6], 'C' => [6,5], 'D' => [5,5]];
    
    if (isset($answer)) {
        $pair = $answersData[$answer] ?? [5,5];
    } else {
        $pair = $dots[$step - 1] ?? [1,1];
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <line x1="5" y1="30" x2="55" y2="30" stroke="black" stroke-width="1"/>
        <!-- Top dots -->
        @for($i = 0; $i < min($pair[0], 3); $i++)
            <circle cx="{{ 15 + $i * 10 }}" cy="17" r="4" fill="black"/>
        @endfor
        <!-- Bottom dots -->
        @for($i = 0; $i < min($pair[1], 3); $i++)
            <circle cx="{{ 15 + $i * 10 }}" cy="43" r="4" fill="black"/>
        @endfor
    </svg>
</div>
