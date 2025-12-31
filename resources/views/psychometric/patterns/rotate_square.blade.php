@php
    // Rotate Square Pattern - kotak berputar 45° setiap langkah
    $rotations = [0, 45, 90, 135, 180];
    $answers = ['A' => 180, 'B' => 0, 'C' => 180, 'D' => 90];
    
    if (isset($answer)) {
        $rotation = $answers[$answer] ?? 180;
    } else {
        $rotation = $rotations[$step - 1] ?? 0;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <rect x="20" y="20" width="20" height="20" fill="black" transform="rotate({{ $rotation }} 30 30)"/>
    </svg>
</div>
