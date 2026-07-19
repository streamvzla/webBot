<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'color',
        'is_active',
        'user_id',
        'is_public',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the platform.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(PlatformSubject::class);
    }

    public function queries(): HasMany
    {
        return $this->hasMany(Query::class);
    }

    /**
     * Get the allowed emails associated with this platform.
     */
    public function allowedEmails(): HasMany
    {
        return $this->hasMany(AllowedEmail::class);
    }

    /**
     * Get the clients that can access this platform.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
