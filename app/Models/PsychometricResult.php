<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'test_id',
        'answers',
        'scores',
        'primary_type',
        'secondary_type',
        'analysis',
        'completion_time_seconds',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'scores' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychometricTest::class, 'test_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->completion_time_seconds) {
            return '-';
        }
        $minutes = floor($this->completion_time_seconds / 60);
        $seconds = $this->completion_time_seconds % 60;
        return "{$minutes}m {$seconds}s";
    }

    /**
     * Get DISC type description
     */
    public function getTypeDescriptionAttribute(): string
    {
        $descriptions = [
            'D' => 'Dominance - Tegas, kompetitif, dan berorientasi hasil',
            'I' => 'Influence - Antusias, optimis, dan pandai bersosialisasi',
            'S' => 'Steadiness - Tenang, sabar, dan dapat diandalkan',
            'C' => 'Compliance - Analitis, teliti, dan mengutamakan kualitas',
        ];

        return $descriptions[$this->primary_type] ?? 'Tidak diketahui';
    }

    /**
     * Get score percentage for a dimension
     */
    public function getScorePercentage(string $dimension): int
    {
        $scores = $this->scores ?? [];
        $total = array_sum($scores);
        
        if ($total === 0) return 0;
        
        return (int) round(($scores[$dimension] ?? 0) / $total * 100);
    }
}
