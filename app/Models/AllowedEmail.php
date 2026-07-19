<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class AllowedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'description',
        'is_active',
        'is_public',
        'user_id',
        'assigned_to',
        'email_account_id',
        'platform_id',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_public'  => 'boolean',
        'expires_at' => 'datetime',
        'paused_at'  => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Cuentas SIN clientes activos (libres o con todas las asignaciones vencidas).
     */
    public function scopeFree($query)
    {
        return $query->where(function ($q) {
            // Sin ningún cliente asignado
            $q->doesntHave('clients')
            // O donde TODOS los clientes tienen expires_at en el pasado
              ->orWhereHas('clients', function ($inner) {
                    $inner->whereNotNull('allowed_email_client.expires_at')
                          ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                }, '=', DB::raw('(SELECT COUNT(*) FROM allowed_email_client aec2 WHERE aec2.allowed_email_id = allowed_emails.id)'));
        });
    }

    /**
     * Scope for visibility based on hierarchy.
     */
    public function scopeVisibleToUser($query, User $user)
    {

        
        // Obtener todos los ancestros (línea hacia arriba)
        $ancestorIds = [];
        $current = $user;
        while ($current->parent_id) {
            $ancestorIds[] = $current->parent_id;
            $current = User::find($current->parent_id);
            if (!$current) break;
        }
        
        $descendantIds = $user->getDescendantsIds();
        
        return $query->where(function ($q) use ($user, $descendantIds, $ancestorIds) {
            $q->where('user_id', $user->id)
              ->orWhere('assigned_to', $user->id)
              ->orWhereIn('assigned_to', $descendantIds)
              ->orWhereIn('assigned_to', $ancestorIds); // Heredar de ancestros
        });
    }

    /**
     * Cuentas CON al menos un cliente activo (no vencido).
     */
    public function scopeOccupied($query)
    {
        return $query->whereHas('clients', function ($inner) {
            $inner->where(function ($c) {
                $c->whereNull('allowed_email_client.expires_at')
                  ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
            });
        });
    }

    /**
     * Cuentas cuyo expires_at (propio, no pivot) ya venció.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }

    // -------------------------------------------------------------------------
    // Helpers de instancia
    // -------------------------------------------------------------------------

    /**
     * ¿Tiene clientes activos (asignación no vencida)?
     */
    public function hasActiveClients(): bool
    {
        return $this->clients()
            ->where(function ($q) {
                $q->whereNull('allowed_email_client.expires_at')
                  ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
            })
            ->exists();
    }

    /**
     * ¿La cuenta está libre (sin clientes activos)?
     */
    public function isFree(): bool
    {
        return !$this->hasActiveClients();
    }

    /**
     * ¿La asignación a este cliente específico está vencida?
     * Usar cuando el modelo viene de la relación con withPivot.
     */
    public function isExpiredForClient(): bool
    {
        if (!isset($this->pivot->expires_at)) {
            return false;
        }
        return $this->pivot->expires_at !== null
            && \Carbon\Carbon::parse($this->pivot->expires_at)->isPast();
    }

    /**
     * El usuario que creó el correo (Dueño / Admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El usuario al que fue asignado (Staff / Sub-revendedor).
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
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
        return $this->belongsToMany(Client::class)
                    ->withPivot('assigned_at', 'expires_at', 'price');
    }
}
