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
        'whatsapp',
        'telegram',
        'website',
        'plan_id',
        'parent_id',
        'last_login_at',
        'subscription_ends_at',
        'grace_days',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
            'subscription_ends_at' => 'datetime',
            'grace_days'        => 'integer',
        ];
    }

    /**
     * Get the platforms owned by this user.
     */
    public function platforms(): HasMany
    {
        return $this->hasMany(Platform::class);
    }

    /**
     * Get the allowed emails owned (created) by this user.
     */
    public function allowedEmails(): HasMany
    {
        return $this->hasMany(AllowedEmail::class);
    }

    /**
     * Get the allowed emails assigned to this user (for resellers).
     */
    public function assignedEmails(): HasMany
    {
        return $this->hasMany(AllowedEmail::class, 'assigned_to');
    }

    /**
     * Hierarchical: Get the parent user (the one who created this user).
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Hierarchical: Get the direct children (sub-resellers/staff) created by this user.
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Recursively get all descendants IDs for a user.
     */
    public function getDescendantsIds(): array
    {
        $ids = [];
        $children = $this->children()->pluck('id')->toArray();
        $ids = array_merge($ids, $children);
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getDescendantsIds());
        }
        
        return array_unique($ids);
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

    /**
     * Get the assigned franchise plan.
     */
    public function franchisePlan()
    {
        return $this->belongsTo(FranchisePlan::class, 'plan_id');
    }

    /**
     * Get the root franchise user (the one who holds the actual plan).
     */
    public function getRootFranchise()
    {
        if ($this->plan_id !== null || $this->parent_id === null || $this->id === 1) {
            return $this;
        }
        
        if ($this->parent) {
            return $this->parent->getRootFranchise();
        }

        return $this;
    }

    /**
     * Get the active franchise plan for this user (direct or inherited).
     */
    public function getActiveFranchisePlan()
    {
        $root = $this->getRootFranchise();
        return $root->franchisePlan;
    }

    /**
     * Check if the subscription is still active (before ends_at).
     */
    public function hasActiveSubscription(): bool
    {
        if (is_null($this->subscription_ends_at)) {
            return true; // No expiration set
        }
        return now()->lessThanOrEqualTo($this->subscription_ends_at);
    }

    /**
     * Check if the subscription is in the grace period (expired, but within grace days).
     */
    public function isInGracePeriod(): bool
    {
        if (is_null($this->subscription_ends_at) || $this->hasActiveSubscription()) {
            return false;
        }

        $graceEndsAt = $this->subscription_ends_at->copy()->addDays($this->grace_days ?? 0);
        return now()->lessThanOrEqualTo($graceEndsAt);
    }

    /**
     * Check if the subscription and grace period are both expired.
     */
    public function isSubscriptionExpired(): bool
    {
        if (is_null($this->subscription_ends_at)) {
            return false;
        }

        return !$this->hasActiveSubscription() && !$this->isInGracePeriod();
    }

    /**
     * Get the number of days until expiration (can be negative if expired).
     */
    public function getDaysUntilExpiration(): int
    {
        if (is_null($this->subscription_ends_at)) {
            return 9999;
        }

        return (int) now()->diffInDays($this->subscription_ends_at, false); // false keeps negative sign
    }
}
