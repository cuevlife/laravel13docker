<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'status', 'tokens', 'max_folders'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 1;
    public const ROLE_TENANT_ADMIN = 5;
    public const ROLE_SUPER_ADMIN = 9;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'max_folders' => 'integer',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'suspended' => 'Suspended',
            default => 'Active',
        };
    }

    /**
     * Check if user is an Admin (Role 5 or higher)
     */
    public function isAdmin(): bool
    {
        return (int) $this->role >= self::ROLE_TENANT_ADMIN;
    }

    /**
     * Check if user is a Superadmin (Role 9 or higher)
     */
    public function isSuperAdmin(): bool
    {
        return (int) $this->role >= self::ROLE_SUPER_ADMIN;
    }

    public function roleLabel(): string
    {
        return match (true) {
            $this->isSuperAdmin() => 'Super Admin',
            $this->isAdmin() => 'Tenant Admin',
            default => 'User',
        };
    }

    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }

    /**
     * Disable remember token functionality since the column was removed for optimization.
     */
    public function getRememberTokenName()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Do nothing
    }

    public function getRememberToken()
    {
        return null;
    }

    public function accessibleMerchants(): Builder
    {
        if ($this->isSuperAdmin()) {
            return Merchant::query()->where('status', 'active')->latest();
        }

        return $this->merchants()->where('status', 'active')->latest()->getQuery();
    }

    public function templates()
    {
        return $this->hasMany(SlipTemplate::class);
    }

    public function slips()
    {
        return $this->hasMany(Slip::class);
    }

    public function tokenLogs()
    {
        return $this->hasMany(TokenLog::class);
    }
}
