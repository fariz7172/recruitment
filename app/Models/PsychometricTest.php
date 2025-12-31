<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychometricTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'duration_minutes',
        'total_questions',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(PsychometricQuestion::class, 'test_id')->orderBy('order');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PsychometricResult::class, 'test_id');
    }

    public function activeQuestions(): HasMany
    {
        return $this->questions()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedDurationAttribute(): string
    {
        return $this->duration_minutes . ' menit';
    }
}
