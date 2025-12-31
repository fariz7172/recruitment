@php
    // Mirror Line Pattern - pola cermin
    $types = ['left', 'right', 'left', 'right', 'left'];
    $answersData = ['A' => 'right', 'B' => 'left', 'C' => 'both', 'D' => 'none'];
    
    if (isset($answer)) {
        $type = $answersData[$answer] ?? 'left';
    } else {
        $type = $types[$step - 1] ?? 'left';
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        <line x1="30" y1="5" x2="30" y2="55" stroke="gray" stroke-width="1" stroke-dasharray="3"/>
        @if($type === 'left' || $type === 'both')
            <polygon points="12,30 25,20 25,40" fill="black"/>
        @endif
        @if($type === 'right' || $type === 'both')
            <polygon points="48,30 35,20 35,40" fill="black"/>
        @endif
    </svg>
</div>
