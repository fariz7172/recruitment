<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'extracted_data',
        'score',
        'recommendation',
        'skill_match',
        'ai_analysis',
        'ai_summary',
        'psychometric_summary',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'skill_match' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the application this result belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who reviewed this result.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get recommendation color for UI.
     */
    public function getRecommendationColorAttribute(): string
    {
        return match($this->recommendation) {
            'SANGAT_SESUAI' => 'success',
            'SESUAI' => 'info',
            'PERTIMBANGKAN' => 'warning',
            'TIDAK_SESUAI' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get recommendation label in Indonesian.
     */
    public function getRecommendationLabelAttribute(): string
    {
        return match($this->recommendation) {
            'SANGAT_SESUAI' => 'Sangat Sesuai',
            'SESUAI' => 'Sesuai',
            'PERTIMBANGKAN' => 'Perlu Dipertimbangkan',
            'TIDAK_SESUAI' => 'Tidak Sesuai',
            default => 'Belum Dianalisis',
        };
    }

    /**
     * Get score percentage formatted.
     */
    public function getScorePercentageAttribute(): string
    {
        return $this->score . '%';
    }

    /**
     * Check if score is high (>= 70)
     */
    public function getIsHighScoreAttribute(): bool
    {
        return $this->score >= 70;
    }

    /**
     * Check if score is medium (40-69)
     */
    public function getIsMediumScoreAttribute(): bool
    {
        return $this->score >= 40 && $this->score < 70;
    }

    /**
     * Check if score is low (< 40)
     */
    public function getIsLowScoreAttribute(): bool
    {
        return $this->score < 40;
    }
}
