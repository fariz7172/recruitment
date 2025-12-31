@php
    // Fill Increase Pattern - pengisian bertambah
    $fills = [25, 50, 75, 100, 100];
    $answersData = ['A' => 75, 'B' => 100, 'C' => 50, 'D' => 25];
    
    if (isset($answer)) {
        $fill = $answersData[$answer] ?? 100;
    } else {
        $fill = $fills[$step - 1] ?? 25;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <rect x="5" y="{{ 55 - ($fill/100 * 50) }}" width="50" height="{{ $fill/100 * 50 }}" fill="black"/>
    </svg>
</div>
