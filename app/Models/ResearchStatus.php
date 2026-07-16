<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchStatus extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'status_name',
        'description',
    ];

    /**
     * @return HasMany<Research, $this>
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class, 'status_id');
    }
}
