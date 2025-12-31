@php
/**
 * Wartegg Stimulus Images
 * 8 kotak dengan stimulus visual berbeda untuk tes proyeksi Wartegg
 * 
 * Box 1: Titik di tengah (Self-concept/Ego)
 * Box 2: Garis lengkung kecil (Emotion/Flexibility)  
 * Box 3: Tiga garis vertikal meninggi (Ambition/Growth)
 * Box 4: Kotak hitam kecil di sudut (Anxiety/Security)
 * Box 5: Dua garis berlawanan arah (Energy/Drive)
 * Box 6: Garis horizontal dan vertikal terpisah (Integration)
 * Box 7: Pola titik-titik (Sensitivity/Detail)
 * Box 8: Lengkungan besar (Social/Protection)
 */
$boxNumber = $box ?? 1;
@endphp

<div class="wartegg-stimulus" style="width: 120px; height: 120px; background: white; border: 2px solid #333; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
    <svg viewBox="0 0 100 100" style="width: 100px; height: 100px;">
        @switch($boxNumber)
            @case(1)
                {{-- Box 1: Titik di tengah --}}
                <circle cx="50" cy="50" r="4" fill="#333"/>
                @break
            
            @case(2)
                {{-- Box 2: Garis lengkung kecil (seperti alis atau gelombang) --}}
                <path d="M 20 45 Q 30 35, 40 45" stroke="#333" stroke-width="2" fill="none"/>
                @break
            
            @case(3)
                {{-- Box 3: Tiga garis vertikal meninggi --}}
                <line x1="35" y1="70" x2="35" y2="50" stroke="#333" stroke-width="2"/>
                <line x1="50" y1="70" x2="50" y2="40" stroke="#333" stroke-width="2"/>
                <line x1="65" y1="70" x2="65" y2="30" stroke="#333" stroke-width="2"/>
                @break
            
            @case(4)
                {{-- Box 4: Kotak hitam kecil di sudut kanan atas --}}
                <rect x="65" y="20" width="15" height="15" fill="#333"/>
                @break
            
            @case(5)
                {{-- Box 5: Dua garis berlawanan arah (seperti huruf T terbalik atau panah) --}}
                <line x1="30" y1="60" x2="50" y2="40" stroke="#333" stroke-width="2"/>
                <line x1="50" y1="40" x2="70" y2="60" stroke="#333" stroke-width="2"/>
                @break
            
            @case(6)
                {{-- Box 6: Garis horizontal dan vertikal terpisah --}}
                <line x1="20" y1="50" x2="45" y2="50" stroke="#333" stroke-width="2"/>
                <line x1="55" y1="30" x2="55" y2="70" stroke="#333" stroke-width="2"/>
                @break
            
            @case(7)
                {{-- Box 7: Pola titik-titik (seperti titik-titik berjejer) --}}
                <circle cx="30" cy="50" r="2" fill="#333"/>
                <circle cx="40" cy="50" r="2" fill="#333"/>
                <circle cx="50" cy="50" r="2" fill="#333"/>
                <circle cx="60" cy="50" r="2" fill="#333"/>
                <circle cx="70" cy="50" r="2" fill="#333"/>
                <circle cx="45" cy="40" r="2" fill="#333"/>
                <circle cx="55" cy="40" r="2" fill="#333"/>
                @break
            
            @case(8)
                {{-- Box 8: Lengkungan besar (arc/setengah lingkaran) --}}
                <path d="M 20 70 Q 50 20, 80 70" stroke="#333" stroke-width="2" fill="none"/>
                @break
            
            @default
                <circle cx="50" cy="50" r="4" fill="#333"/>
        @endswitch
    </svg>
</div>
