@php
    // Tile Checker Pattern - pola papan catur
    $patterns = [
        [1,0,0,0], [0,1,0,0], [0,0,1,0], [0,0,0,1], [1,0,1,0]
    ];
    $answersData = [
        'A' => [1,0,1,0],
        'B' => [0,1,0,1],
        'C' => [1,1,0,0],
        'D' => [0,0,1,1]
    ];
    
    if (isset($answer)) {
        $tiles = $answersData[$answer] ?? [1,0,1,0];
    } else {
        $tiles = $patterns[$step - 1] ?? [1,0,0,0];
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <line x1="30" y1="5" x2="30" y2="55" stroke="black" stroke-width="1"/>
        <line x1="5" y1="30" x2="55" y2="30" stroke="black" stroke-width="1"/>
        @if($tiles[0]) <rect x="5" y="5" width="25" height="25" fill="black"/> @endif
        @if($tiles[1]) <rect x="30" y="5" width="25" height="25" fill="black"/> @endif
        @if($tiles[2]) <rect x="5" y="30" width="25" height="25" fill="black"/> @endif
        @if($tiles[3]) <rect x="30" y="30" width="25" height="25" fill="black"/> @endif
    </svg>
</div>
