<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Research extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'research_code',
        'title',
        'lead_proponent_id',
        'organizational_unit_id',
        'category_id',
        'status_id',
        'submitted_at',
        'approved_at',
        'archived_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function leadProponent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_proponent_id');
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ResearchCategory::class, 'category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ResearchStatus::class, 'status_id');
    }

    /**
     * @return HasMany<ResearchProponent, $this>
     */
    public function proponents(): HasMany
    {
        return $this->hasMany(\App\Models\ResearchProponent::class);
    }

    /**
     * @return HasMany<ResearchDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(\App\Models\ResearchDocument::class);
    }
}
