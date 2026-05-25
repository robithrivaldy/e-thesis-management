<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Thesis extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'status',
        'submission_date',
        'approval_date',
        'scheduled_defense_date',
        'defense_date',
        'defense_score',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'approval_date' => 'datetime',
        'scheduled_defense_date' => 'datetime',
        'defense_date' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_DEFENSE_SCHEDULED = 'defense_scheduled';
    const STATUS_DEFENDED = 'defended';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ARCHIVED = 'archived';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
            self::STATUS_DEFENSE_SCHEDULED => 'Defense Scheduled',
            self::STATUS_DEFENDED => 'Defended',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get the student who submitted this thesis
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisors for this thesis
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisors', 'thesis_id', 'supervisor_id')
            ->withPivot('role', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Get the chapters of this thesis
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(ThesisChapter::class);
    }

    /**
     * Get the guidance sessions for this thesis
     */
    public function guidanceSessions(): HasMany
    {
        return $this->hasMany(Guidance::class);
    }

    /**
     * Get the defense for this thesis
     */
    public function defense(): BelongsTo
    {
        return $this->belongsTo(Defense::class);
    }

    /**
     * Get the files uploaded for this thesis
     */
    public function files(): HasMany
    {
        return $this->hasMany(ThesisFile::class);
    }

    /**
     * Scope: Get submitted theses
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope: Get approved theses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: Get completed theses
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Get pending for defense
     */
    public function scopePendingDefense($query)
    {
        return $query->where('status', self::STATUS_DEFENSE_SCHEDULED);
    }
}
