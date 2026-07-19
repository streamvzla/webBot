<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'imap_username',
        'imap_password',
        'username',
        'is_active',
        'last_checked_at',
        'user_id',
        'is_authorized',
        'authorization_notes',
    ];

    protected $hidden = [
        'imap_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
        'imap_port' => 'integer',
        'is_authorized' => 'boolean',
    ];

    // Keep old relationship for backward compatibility
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // New many-to-many relationship
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'email_account_users')
            ->withTimestamps();
    }

    // Client relationship (one-to-one or one-to-many)
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function getAssignedUsersTextAttribute(): string
    {
        return $this->users->pluck('username')->implode(', ');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
