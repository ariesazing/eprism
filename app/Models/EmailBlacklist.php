<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailBlacklist extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'reason_code',
        'reason_details',
        'blocked_by',
        'blocked_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
        ];
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
}
