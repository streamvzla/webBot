<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'phone',
        'address',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the platforms owned by this user.
     */
    public function platforms(): HasMany
    {
        return $this->hasMany(Platform::class);
    }

    /**
     * Get the allowed emails owned by this user.
     */
    public function allowedEmails(): HasMany
    {
        return $this->hasMany(AllowedEmail::class);
    }

    /**
     * Get the clients owned by this user.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function emailAccounts(): BelongsToMany
    {
        return $this->belongsToMany(EmailAccount::class, 'email_account_users')
            ->withTimestamps();
    }

    public function queries(): HasMany
    {
        return $this->hasMany(Query::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if user can access a resource (admin can access all, user only their own).
     */
    public function canAccess($resource): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $resource->user_id === $this->id;
    }
}
