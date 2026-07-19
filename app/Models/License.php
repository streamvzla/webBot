<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'domain',
        'client_name',
        'client_email',
        'status',           // active | suspended | revoked
        'notes',
        'activated_at',     // Cuando el cliente instalo por primera vez
        'last_verified_at', // Último ping de verificación exitoso
        'max_clients',      // Límite de clientes permitidos (null = ilimitado)
        'max_queries_day',  // Límite global de consultas/día (null = ilimitado)
    ];

    protected $casts = [
        'activated_at'     => 'datetime',
        'last_verified_at' => 'datetime',
        'max_clients'      => 'integer',
        'max_queries_day'  => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** ¿La licencia está activa? */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** ¿La licencia está suspendida? */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /** ¿La licencia ha sido revocada? */
    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    /**
     * Registrar la primera activación en un dominio.
     * Si ya tiene dominio, no sobreescribir.
     */
    public function activate(string $domain): void
    {
        if (!$this->domain) {
            $this->domain       = $domain;
            $this->activated_at = now();
        }

        $this->last_verified_at = now();
        $this->save();

        // Limpiar caché de validación
        Cache::forget("license_valid_{$this->license_key}");
    }

    /**
     * Verificar que la clave de licencia es válida para el dominio actual.
     * Usa caché de 10 minutos para no golpear la BD en cada request.
     */
    public static function validate(string $licenseKey, string $currentDomain): array
    {
        $cacheKey = "license_valid_{$licenseKey}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($licenseKey, $currentDomain) {
            $license = self::where('license_key', $licenseKey)->first();

            if (!$license) {
                return ['valid' => false, 'reason' => 'Licencia no encontrada.'];
            }

            if (!$license->isActive()) {
                return ['valid' => false, 'reason' => "Licencia {$license->status}. Contacta al soporte."];
            }

            // Si tiene dominio registrado, verificar que coincide
            if ($license->domain && $license->domain !== $currentDomain) {
                return [
                    'valid'  => false,
                    'reason' => "Esta licencia está vinculada al dominio '{$license->domain}', no a '{$currentDomain}'.",
                ];
            }

            // Primera instalación: auto-vincular dominio
            if (!$license->domain) {
                $license->activate($currentDomain);
            } else {
                // Solo actualizar timestamp de verificación
                $license->last_verified_at = now();
                $license->save();
            }

            return ['valid' => true, 'license' => $license];
        });
    }

    /**
     * Suspender la licencia y limpiar caché.
     */
    public function suspend(string $reason = ''): void
    {
        $this->status = 'suspended';
        $this->notes  = $reason ?: $this->notes;
        $this->save();
        Cache::forget("license_valid_{$this->license_key}");
    }

    /**
     * Revocar la licencia permanentemente.
     */
    public function revoke(): void
    {
        $this->status = 'revoked';
        $this->save();
        Cache::forget("license_valid_{$this->license_key}");
    }

    /**
     * Reactivar una licencia suspendida.
     */
    public function reactivate(): void
    {
        $this->status = 'active';
        $this->save();
        Cache::forget("license_valid_{$this->license_key}");
    }
}
