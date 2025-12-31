<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'applicant_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter_path',
        'notes',
        'status',
    ];

    /**
     * Get the job this application is for.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the screening result for this application.
     */
    public function screeningResult(): HasOne
    {
        return $this->hasOne(ScreeningResult::class);
    }

    /**
     * Get psychometric test results.
     */
    public function psychometricResults(): HasMany
    {
        return $this->hasMany(PsychometricResult::class);
    }

    /**
     * Check if application has been screened.
     */
    public function getIsScreenedAttribute(): bool
    {
        return $this->screeningResult !== null;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'screening' => 'info',
            'reviewed' => 'primary',
            'shortlisted' => 'success',
            'rejected' => 'danger',
            'hired' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'screening' => 'Sedang Dianalisis',
            'reviewed' => 'Sudah Ditinjau',
            'shortlisted' => 'Lolos Seleksi',
            'rejected' => 'Ditolak',
            'hired' => 'Diterima',
            default => 'Unknown',
        };
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
