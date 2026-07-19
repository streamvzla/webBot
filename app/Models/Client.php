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
        'phone',
        'avatar',
        'last_login_at',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'pending_email',
        'email_change_token',
        'email_change_token_expires_at',
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
        'last_login_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'email_change_token_expires_at' => 'datetime',
    ];

    public function warrantyRequests()
    {
        return $this->hasMany(WarrantyRequest::class);
    }

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
        return $this->belongsToMany(AllowedEmail::class)
                    ->withPivot('assigned_at', 'expires_at', 'price');
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
     *
     * Logic:
     * - If access_mode === 'all': Client can access any email from the parent user OR global emails
     * - If access_mode === 'selective': Client can only access emails specifically assigned to them (and not expired)
     */
    public function hasAccessToEmail(string $email): bool
    {
        if ($this->access_mode === 'all') {
            // MODO ACCESO TOTAL: El cliente puede acceder a cualquier correo del usuario padre O correos globales
            // Verificar correos del usuario padre
            if ($this->user && $this->user->allowedEmails()->where('email', $email)->exists()) {
                return true;
            }

            // Verificar correos globales (sin user_id)
            return AllowedEmail::whereNull('user_id')
                ->where('email', $email)
                ->exists();
        }

        // MODO ACCESO SELECTIVO: Solo puede acceder a los correos asignados específicamente Y que no estén vencidos
        return $this->allowedEmails()
            ->where('email', $email)
            ->where(function ($query) {
                $query->whereNull('allowed_email_client.expires_at')
                      ->orWhere('allowed_email_client.expires_at', '>=', now()->startOfDay());
            })
            ->exists();
    }

    /**
     * Get the client's full email.
     */
    public function getFullEmailAttribute(): string
    {
        $fallbackDomain = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?? 'localhost';
        return $this->email . '@' . config('app.domain', $fallbackDomain);
    }

    /**
     * Check if client can make a query based on rate limiting.
     */
    public function canMakeQuery(): bool
    {
        $minutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);

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
        if ($this->last_query_at && !$this->last_query_at->isToday()) {
            return false;
        }
        return $this->query_count >= ($this->max_queries_per_day ?? 100);
    }

    /**
     * Increment query count and update last query time.
     */
    public function recordQuery(): void
    {
        if ($this->last_query_at && !$this->last_query_at->isToday()) {
            $this->query_count = 1;
        } else {
            $this->query_count += 1;
        }
        $this->last_query_at = now();
        $this->save();
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
