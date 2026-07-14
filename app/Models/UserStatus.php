<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStatus extends Model
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
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id');
    }
}
