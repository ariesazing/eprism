<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchProponent extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'research_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'position_title',
        'organizational_unit_name',
        'email',
        'contact_number',
        'photo_path',
        'photo_disk',
        'photo_filename',
    ];

    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class);
    }
}
