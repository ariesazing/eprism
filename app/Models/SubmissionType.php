<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionType extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type_name',
        'description',
    ];

    /**
     * @return HasMany<ResearchVersion, $this>
     */
    public function researchVersions(): HasMany
    {
        return $this->hasMany(\App\Models\ResearchVersion::class);
    }
}