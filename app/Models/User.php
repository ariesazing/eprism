<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * @return array<int, string>
     */
    public static function positionTitles(): array
    {
        return [
            'Teacher I',
            'Teacher II',
            'Teacher III',

            'Master Teacher I',
            'Master Teacher II',
            'Master Teacher III',
            'Master Teacher IV',
            'Master Teacher V',

            'Head Teacher I',
            'Head Teacher II',
            'Head Teacher III',
            'Head Teacher IV',
            'Head Teacher V',
            'Head Teacher VI',  

            'Assistant School Principal I',
            'Assistant School Principal II',
            'Assistant School Principal III',

            'School Principal I',
            'School Principal II',
            'School Principal III',
            'School Principal IV',

            'Project Development Officer I',
            'Project Development Officer II',

            'Senior Education Program Specialist',
            'Education Program Supervisor',
            'Public Schools District Supervisor',
        ];
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'organizational_unit_id',
        'deped_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password',
        'position_title',
        'contact_number',
        'status_id',
        'approved_by',
        'approved_at',
        'last_login_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
            'rejected_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'status_id');
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function approvedUsers(): HasMany
    {
        return $this->hasMany(self::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rejected_by');
    }

    public function rejectedUsers(): HasMany
    {
        return $this->hasMany(self::class, 'rejected_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ])));
    }

    /**
     * @return HasMany<Research, $this>
     */
    public function ledResearches(): HasMany
    {
        return $this->hasMany(\App\Models\Research::class, 'lead_proponent_id');
    }

    /**
     * @return HasMany<ResearchDocument, $this>
     */
    public function uploadedResearchDocuments(): HasMany
    {
        return $this->hasMany(\App\Models\ResearchDocument::class, 'uploaded_by');
    }

    /**
     * @return HasMany<ResearchVersion, $this>
     */
    public function submittedResearchVersions(): HasMany
    {
        return $this->hasMany('App\\Models\\ResearchVersion', 'submitted_by');
    }

    /**
     * @return HasMany<VersionFile, $this>
     */
    public function uploadedVersionFiles(): HasMany
    {
        return $this->hasMany('App\\Models\\VersionFile', 'uploaded_by');
    }
}
