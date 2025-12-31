@php
    // Dots Diagonal Pattern - titik bergerak diagonal
    $positions = [[15,15], [25,25], [35,35], [45,45], [55,55]];
    $answersData = ['A' => [45,45], 'B' => [55,55], 'C' => [35,55], 'D' => [55,35]];
    
    if (isset($answer)) {
        $pos = $answersData[$answer] ?? [55,55];
    } else {
        $pos = $positions[$step - 1] ?? [15,15];
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <circle cx="{{ $pos[0] }}" cy="{{ $pos[1] }}" r="6" fill="black"/>
    </svg>
</div>
