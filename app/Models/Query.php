<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Query extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'email_account_id',
        'platform_id',
        'email',
        'ip_address',
        'user_agent',
        'result',
        'code_hash',
        'code_status',
        'response_time',
    ];

    protected $attributes = [
        'user_id' => null,
    ];

    protected $casts = [
        'response_time' => 'decimal:3',
    ];

    /**
     * Get the client that made the query.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the admin user that made the query (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the email account used for the query.
     */
    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * Get the platform queried.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Get the result status.
     */
    public function getStatusAttribute(): string
    {
        if ($this->result === 'success') {
            return 'Éxito';
        } elseif ($this->result === 'pending') {
            return 'Pendiente';
        } elseif ($this->result === 'no_code') {
            return 'Sin código';
        } elseif ($this->result === 'error') {
            return 'Error';
        }
        return 'Desconocido';
    }

    /**
     * Generate hash for code (for security - never store full code).
     */
    public static function hashCode(string $code): string
    {
        return hash('sha256', $code);
    }
}
