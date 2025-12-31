@php
    // Cross Extend Pattern - silang memanjang
    $lengths = [5, 10, 15, 20, 25];
    $answersData = ['A' => 25, 'B' => 20, 'C' => 30, 'D' => 15];
    
    if (isset($answer)) {
        $len = $answersData[$answer] ?? 25;
    } else {
        $len = $lengths[$step - 1] ?? 5;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <line x1="{{ 30 - $len }}" y1="30" x2="{{ 30 + $len }}" y2="30" stroke="black" stroke-width="3"/>
        <line x1="30" y1="{{ 30 - $len }}" x2="30" y2="{{ 30 + $len }}" stroke="black" stroke-width="3"/>
    </svg>
</div>
