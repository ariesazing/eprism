<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchCategory extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_name',
        'description',
    ];

    /**
     * @return HasMany<Research, $this>
     */
    public function researches(): HasMany
    {
        return $this->hasMany(\App\Models\Research::class, 'category_id');
    }
}
