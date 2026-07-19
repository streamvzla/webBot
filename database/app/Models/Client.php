<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'email_account_id',
        'user_id',
        'last_query_at',
        'query_count',
        'max_queries_per_day',
        'access_mode',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_query_at' => 'datetime',
        'max_queries_per_day' => 'integer',
        'query_count' => 'integer',
        'access_mode' => 'string',
    ];

    /**
     * Get the user (admin/user) that owns this client.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the email account associated with the client.
     */
    public function emailAccount()
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * Get the allowed emails that this client can access.
     */
    public function allowedEmails()
    {
        return $this->belongsToMany(AllowedEmail::class);
    }

    /**
     * Get the platforms that this client can access.
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class);
    }

    /**
     * Check if client has access to a specific email.
     */
    public function hasAccessToEmail(string $email): bool
    {
        // Si el cliente tiene acceso total, permitir
        if ($this->access_mode === 'all') {
            return true;
        }

        // Verificar si el email está en los emails autorizados del cliente
        if ($this->allowedEmails()->where('email', $email)->exists()) {
            return true;
        }

        // Verificar si el email está en los emails autorizados del usuario padre
        if ($this->user) {
            if ($this->user->allowedEmails()->where('email', $email)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the client's full email.
     */
    public function getFullEmailAttribute(): string
    {
        return $this->email . '@' . config('app.domain', 'netbca.com');
    }

    /**
     * Check if client can make a query based on rate limiting.
     */
    public function canMakeQuery(): bool
    {
        $minutes = \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);

        if (!$this->last_query_at) {
            return true;
        }

        return $this->last_query_at->addMinutes($minutes)->isPast();
    }

    /**
     * Check if client has reached daily query limit.
     */
    public function hasReachedDailyLimit(): bool
    {
        return $this->query_count >= ($this->max_queries_per_day ?? 100);
    }

    /**
     * Increment query count and update last query time.
     */
    public function recordQuery(): void
    {
        $this->increment('query_count');
        $this->update(['last_query_at' => now()]);
    }

    /**
     * Reset daily query count.
     */
    public function resetDailyQueryCount(): void
    {
        $this->update(['query_count' => 0]);
    }

    /**
     * Get the queries for the client.
     */
    public function queries()
    {
        return $this->hasMany(Query::class);
    }
}
