@php
    // Grid Fill Pattern - kotak grid terisi
    $fills = [1, 2, 3, 4, 5];
    $answersData = ['A' => 4, 'B' => 6, 'C' => 5, 'D' => 3];
    
    if (isset($answer)) {
        $count = $answersData[$answer] ?? 5;
    } else {
        $count = $fills[$step - 1] ?? 1;
    }
    $positions = [[0,0], [1,0], [0,1], [1,1], [2,0], [2,1]];
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <!-- Grid lines -->
        <line x1="22" y1="5" x2="22" y2="55" stroke="black" stroke-width="1"/>
        <line x1="38" y1="5" x2="38" y2="55" stroke="black" stroke-width="1"/>
        <line x1="5" y1="30" x2="55" y2="30" stroke="black" stroke-width="1"/>
        @for($i = 0; $i < $count && $i < count($positions); $i++)
            <rect x="{{ 6 + $positions[$i][0] * 17 }}" y="{{ 6 + $positions[$i][1] * 25 }}" width="15" height="23" fill="black"/>
        @endfor
    </svg>
</div>
