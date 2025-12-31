<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'department',
        'location',
        'employment_type',
        'min_education',
        'min_experience_years',
        'required_skills',
        'preferred_skills',
        'salary_min',
        'salary_max',
        'deadline',
        'status',
        'created_by',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'preferred_skills' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'deadline' => 'date',
    ];

    /**
     * Get the user who created the job.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all applications for this job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get formatted salary range.
     */
    public function getSalaryRangeAttribute(): ?string
    {
        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        } elseif ($this->salary_min) {
            return 'Mulai dari Rp ' . number_format($this->salary_min, 0, ',', '.');
        } elseif ($this->salary_max) {
            return 'Sampai Rp ' . number_format($this->salary_max, 0, ',', '.');
        }
        return null;
    }

    /**
     * Check if job is still open for applications.
     */
    public function getIsOpenAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->deadline && $this->deadline->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Scope active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
