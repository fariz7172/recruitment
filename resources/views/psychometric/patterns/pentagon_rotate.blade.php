@php
    // Pentagon Rotate Pattern
    $rotations = [0, 72, 144, 216, 288];
    $answersData = ['A' => 216, 'B' => 360, 'C' => 288, 'D' => 144];
    
    if (isset($answer)) {
        $rotation = $answersData[$answer] ?? 288;
    } else {
        $rotation = $rotations[$step - 1] ?? 0;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <g transform="rotate({{ $rotation }} 30 30)">
            <polygon points="30,10 48,25 42,47 18,47 12,25" fill="black"/>
        </g>
    </svg>
</div>
