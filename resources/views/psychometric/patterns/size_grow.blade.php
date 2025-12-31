@php
    // Size Grow Pattern - ukuran membesar
    $sizes = [8, 12, 16, 20, 24];
    $answersData = ['A' => 20, 'B' => 16, 'C' => 28, 'D' => 24];
    
    if (isset($answer)) {
        $size = $answersData[$answer] ?? 24;
    } else {
        $size = $sizes[$step - 1] ?? 8;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <circle cx="30" cy="30" r="{{ $size }}" fill="black"/>
    </svg>
</div>
