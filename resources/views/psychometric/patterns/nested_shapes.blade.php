@php
    // Nested Shapes Pattern - bentuk bersarang
    $nests = [1, 2, 3, 4, 5];
    $answersData = ['A' => 4, 'B' => 3, 'C' => 6, 'D' => 5];
    
    if (isset($answer)) {
        $count = $answersData[$answer] ?? 5;
    } else {
        $count = $nests[$step - 1] ?? 1;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @for($i = 0; $i < $count; $i++)
            <rect x="{{ 10 + $i * 4 }}" y="{{ 10 + $i * 4 }}" width="{{ 40 - $i * 8 }}" height="{{ 40 - $i * 8 }}" fill="none" stroke="black" stroke-width="2"/>
        @endfor
    </svg>
</div>
