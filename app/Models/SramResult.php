<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SramResult extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'research_version_id',
        'overall_score',
        'overall_result',
        'recommendation',
        'evaluated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'evaluated_at' => 'datetime',
        ];
    }

    public function researchVersion(): BelongsTo
    {
        return $this->belongsTo(ResearchVersion::class);
    }

    /**
     * @return HasMany<SramCheck, $this>
     */
    public function checks(): HasMany
    {
        return $this->hasMany(\App\Models\SramCheck::class);
    }
}