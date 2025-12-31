@php
    // Arrow Rotate Pattern - panah berputar
    $rotations = [0, 90, 180, 270, 360];
    $answersData = ['A' => 360, 'B' => 270, 'C' => 180, 'D' => 45];
    
    if (isset($answer)) {
        $rotation = $answersData[$answer] ?? 360;
    } else {
        $rotation = $rotations[$step - 1] ?? 0;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <g transform="rotate({{ $rotation }} 30 30)">
            <line x1="30" y1="45" x2="30" y2="15" stroke="black" stroke-width="3"/>
            <polygon points="30,10 25,20 35,20" fill="black"/>
        </g>
    </svg>
</div>
