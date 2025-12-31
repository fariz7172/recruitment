<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychometricQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question_text',
        'question_type',
        'options',
        'dimension',
        'order',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'options' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychometricTest::class, 'test_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
