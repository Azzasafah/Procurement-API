<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'photo',
        'password',
        'department_id',
        'role',
        'phone',
        'is_active'
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
            'password' => 'hashed',
        ];
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn($photo) => $photo ? url('/storage/' . $photo) : null,
        );
    }

    // role constants
    const ROLE_EMPLOYEE   = 'employee';
    const ROLE_PURCHASING = 'purchasing';
    const ROLE_MANAGER    = 'manager'; // Atasan purchasing
    const ROLE_WAREHOUSE  = 'warehouse';
    const ROLE_ADMIN      = 'admin';

    const ROLES = [
        self::ROLE_EMPLOYEE,
        self::ROLE_PURCHASING,
        self::ROLE_MANAGER,
        self::ROLE_WAREHOUSE,
        self::ROLE_ADMIN,
    ];

    // check apakah user memiliki beberapa role tertentu
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    // check apakah user memiliki role tertentu
    public function hasRole(string $role)
    {
        return $this->role === $role;
    }

    // relationship department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // relationship user
    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class, 'requester_id');
    }

    // relationship user
    public function approvals()
    {
        return $this->hasMany(User::class, 'approver_id');
    }
}
