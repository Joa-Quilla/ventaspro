<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool hasPermission(string $permissionName)
 * @method bool hasRole(string $roleName)
 * @method bool hasAnyRole(array $roles)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Cache key para los permisos del usuario
     */
    private function permissionsCacheKey(): string
    {
        return "user_{$this->id}_permissions";
    }

    /**
     * Obtiene todos los permisos del usuario (con cache)
     */
    public function getAllPermissions(): array
    {
        return cache()->remember($this->permissionsCacheKey(), 3600, function () {
            return $this->roles()->with('permissions')->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('name')
                ->unique()
                ->toArray();
        });
    }

    /**
     * Limpia el cache de permisos
     */
    public function clearPermissionsCache(): void
    {
        cache()->forget($this->permissionsCacheKey());
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        return in_array($permissionName, $this->getAllPermissions());
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }
}
