@php
    // Triangle Flip Pattern - segitiga berbalik
    $directions = ['up', 'down', 'up', 'down', 'up'];
    $answersData = ['A' => 'down', 'B' => 'left', 'C' => 'up', 'D' => 'right'];
    
    if (isset($answer)) {
        $dir = $answersData[$answer] ?? 'up';
    } else {
        $dir = $directions[$step - 1] ?? 'up';
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @if($dir === 'up')
            <polygon points="30,15 15,45 45,45" fill="black"/>
        @elseif($dir === 'down')
            <polygon points="30,45 15,15 45,15" fill="black"/>
        @elseif($dir === 'left')
            <polygon points="15,30 45,15 45,45" fill="black"/>
        @else
            <polygon points="45,30 15,15 15,45" fill="black"/>
        @endif
    </svg>
</div>
