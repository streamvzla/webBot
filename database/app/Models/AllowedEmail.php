<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AllowedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'description',
        'is_active',
        'is_public',
        'user_id',
        'email_account_id',
        'platform_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns this allowed email.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * Get the platform associated with this allowed email.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Get the clients that can access this email.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
