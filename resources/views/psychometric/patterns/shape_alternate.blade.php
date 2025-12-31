@php
    // Shape Alternate Pattern - bentuk bergantian
    $shapes = ['circle', 'square', 'circle', 'square', 'circle'];
    $answersData = ['A' => 'square', 'B' => 'triangle', 'C' => 'circle', 'D' => 'diamond'];
    
    if (isset($answer)) {
        $shape = $answersData[$answer] ?? 'circle';
    } else {
        $shape = $shapes[$step - 1] ?? 'circle';
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @if($shape === 'circle')
            <circle cx="30" cy="30" r="15" fill="black"/>
        @elseif($shape === 'square')
            <rect x="15" y="15" width="30" height="30" fill="black"/>
        @elseif($shape === 'triangle')
            <polygon points="30,15 15,45 45,45" fill="black"/>
        @else
            <polygon points="30,10 50,30 30,50 10,30" fill="black"/>
        @endif
    </svg>
</div>
