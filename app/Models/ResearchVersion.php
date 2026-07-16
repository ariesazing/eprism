<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResearchVersion extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'research_id',
        'submission_type_id',
        'version_number',
        'parent_version_id',
        'status_id',
        'is_current',
        'submitted_by',
        'submitted_at',
        'remarks',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class);
    }

    public function submissionType(): BelongsTo
    {
        return $this->belongsTo(SubmissionType::class);
    }

    public function parentVersion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_version_id');
    }

    /**
     * @return HasMany<ResearchVersion, $this>
     */
    public function childVersions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_version_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ResearchStatus::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return HasMany<VersionFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(\App\Models\VersionFile::class);
    }

    /**
     * @return HasOne<SramResult, $this>
     */
    public function sramResult(): HasOne
    {
        return $this->hasOne(\App\Models\SramResult::class);
    }
}