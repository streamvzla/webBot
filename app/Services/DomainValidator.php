<?php

namespace App\Services;

use App\Models\AllowedEmail;
use App\Models\Setting;

/**
 * DomainValidator - Validación de dominios contra lista blanca
 *
 * ⚠️ CRÍTICO: Valida dominios en tiempo real antes de operación IMAP
 *
 * Los dominios autorizados se obtienen ÚNICAMENTE de:
 *   1. La tabla `allowed_emails` (correos activos del sistema)
 *   2. El campo `whitelisted_domains` de la configuración (Setting)
 *
 * NO hay dominios hardcodeados por seguridad.
 */
class DomainValidator
{
    protected ?Setting $settings;
    protected ?AllowedEmail $allowedEmail;

    public function __construct(?AllowedEmail $allowedEmail = null)
    {
        $this->settings     = Setting::first();
        $this->allowedEmail = $allowedEmail;
    }

    /**
     * Verificar si el dominio del correo está autorizado
     */
    public function isDomainAllowed(string $email): bool
    {
        // Si el filtro está desactivado, permitir todos
        if (!$this->isFilterEnabled()) {
            return true;
        }

        $domain = $this->extractDomain($email);

        if (!$domain) {
            return false;
        }

        return $this->isInWhitelist($domain);
    }

    /**
     * Verificar si el filtro de dominios está activado
     */
    public function isFilterEnabled(): bool
    {
        return $this->settings?->email_filter_enabled ?? false;
    }

    /**
     * Extraer dominio de un email
     */
    public function extractDomain(string $email): ?string
    {
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return null;
        }

        return strtolower(trim($parts[1]));
    }

    /**
     * Verificar si el dominio está en la lista blanca.
     *
     * Fuentes de dominios (en orden de prioridad):
     *   1. Correos activos en allowed_emails → extrae su dominio
     *   2. Lista de dominios en settings.whitelisted_domains (campo JSON/texto)
     *
     * ⚠️ SEGURIDAD: NO hay dominios hardcodeados aquí.
     *    Todos los dominios autorizados deben estar en la BD.
     */
    public function isInWhitelist(string $domain): bool
    {
        // 1. Dominios derivados de correos activos en la BD
        $emailDomains = AllowedEmail::where('is_active', true)
            ->get()
            ->map(fn($item) => $this->extractDomain($item->email))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // 2. Dominios configurados manualmente en Settings
        $configDomains = [];
        if ($this->settings && !empty($this->settings->whitelisted_domains)) {
            $raw = $this->settings->whitelisted_domains;
            // Soporta tanto JSON array como lista separada por comas/saltos de línea
            if (is_array($raw)) {
                $configDomains = $raw;
            } elseif (is_string($raw)) {
                $configDomains = array_filter(
                    array_map('trim', preg_split('/[\n,]+/', $raw))
                );
            }
        }

        $allDomains = array_unique(array_merge($emailDomains, $configDomains));

        return in_array(strtolower($domain), $allDomains, true);
    }

    /**
     * Obtener lista completa de dominios autorizados
     */
    public function getWhitelistedDomains(): array
    {
        $emailDomains = AllowedEmail::where('is_active', true)
            ->get()
            ->map(fn($item) => $this->extractDomain($item->email))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $configDomains = [];
        if ($this->settings && !empty($this->settings->whitelisted_domains)) {
            $raw = $this->settings->whitelisted_domains;
            if (is_array($raw)) {
                $configDomains = $raw;
            } elseif (is_string($raw)) {
                $configDomains = array_filter(
                    array_map('trim', preg_split('/[\n,]+/', $raw))
                );
            }
        }

        return array_values(array_unique(array_merge($emailDomains, $configDomains)));
    }

    /**
     * Validar o fallar si el dominio no está autorizado
     *
     * @throws \Exception Si el dominio no está autorizado
     */
    public function validateOrFail(string $email): void
    {
        if (!$this->isDomainAllowed($email)) {
            $domain = $this->extractDomain($email);
            throw new \Exception(
                "El dominio '@{$domain}' no está autorizado para realizar consultas. " .
                "Solo se permiten dominios registrados en el sistema."
            );
        }
    }

    /**
     * Obtener estado de validación del dominio
     */
    public function getValidationStatus(string $email): array
    {
        return [
            'email'               => $email,
            'domain'              => $this->extractDomain($email),
            'filter_enabled'      => $this->isFilterEnabled(),
            'is_allowed'          => $this->isDomainAllowed($email),
            'whitelisted_domains' => $this->getWhitelistedDomains(),
        ];
    }

    /**
     * Normalizar email para comparación
     */
    public function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Verificar si es un email válido (formato básico)
     */
    public function isValidEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar email completo (formato y dominio)
     */
    public function validateFullEmail(string $email): array
    {
        return [
            'valid_format'   => $this->isValidEmailFormat($email),
            'domain'         => $this->extractDomain($email),
            'domain_allowed' => $this->isDomainAllowed($email),
            'can_proceed'    => $this->isValidEmailFormat($email) && $this->isDomainAllowed($email),
        ];
    }
}
