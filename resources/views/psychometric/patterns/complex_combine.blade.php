@php
    // Complex Combine Pattern - kombinasi bentuk
    $combos = [
        ['circle'], 
        ['circle', 'square'], 
        ['circle', 'square', 'triangle'], 
        ['circle', 'square', 'triangle', 'line'],
        ['circle', 'square', 'triangle', 'line', 'cross']
    ];
    $answersData = [
        'A' => ['circle', 'square', 'triangle', 'line'],
        'B' => ['circle', 'square', 'triangle'],
        'C' => ['circle', 'square', 'triangle', 'line', 'line'],
        'D' => ['circle', 'square', 'triangle', 'line', 'cross']
    ];
    
    if (isset($answer)) {
        $shapes = $answersData[$answer] ?? [];
    } else {
        $shapes = $combos[$step - 1] ?? [];
    }
@endphp
<div class="pattern-box">
    <svg viewBox="0 0 60 60" class="pattern-svg">
        <rect x="5" y="5" width="50" height="50" fill="white" stroke="black" stroke-width="2"/>
        @if(in_array('circle', $shapes))
            <circle cx="20" cy="20" r="8" fill="none" stroke="black" stroke-width="2"/>
        @endif
        @if(in_array('square', $shapes))
            <rect x="32" y="12" width="16" height="16" fill="none" stroke="black" stroke-width="2"/>
        @endif
        @if(in_array('triangle', $shapes))
            <polygon points="20,48 12,32 28,32" fill="none" stroke="black" stroke-width="2"/>
        @endif
        @if(in_array('line', $shapes))
            <line x1="35" y1="35" x2="50" y2="50" stroke="black" stroke-width="2"/>
        @endif
        @if(in_array('cross', $shapes))
            <line x1="35" y1="42" x2="50" y2="42" stroke="black" stroke-width="2"/>
            <line x1="42" y1="35" x2="42" y2="50" stroke="black" stroke-width="2"/>
        @endif
    </svg>
</div>
