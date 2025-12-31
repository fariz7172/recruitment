@php
    // Quarter Rotate Pattern - kuartal terisi berputar
    $quarters = [1, 2, 3, 4, 1];
    $answersData = ['A' => 4, 'B' => 1, 'C' => 2, 'D' => 3];
    
    if (isset($answer)) {
        $q = $answersData[$answer] ?? 1;
    } else {
        $q = $quarters[$step - 1] ?? 1;
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <line x1="30" y1="5" x2="30" y2="55" stroke="black" stroke-width="1"/>
        <line x1="5" y1="30" x2="55" y2="30" stroke="black" stroke-width="1"/>
        @if($q === 1)
            <rect x="30" y="5" width="25" height="25" fill="black"/>
        @elseif($q === 2)
            <rect x="30" y="30" width="25" height="25" fill="black"/>
        @elseif($q === 3)
            <rect x="5" y="30" width="25" height="25" fill="black"/>
        @else
            <rect x="5" y="5" width="25" height="25" fill="black"/>
        @endif
    </svg>
</div>
