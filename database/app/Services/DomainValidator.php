<?php

namespace App\Services;

use App\Models\AllowedEmail;
use App\Models\Setting;

/**
 * DomainValidator - Validación de dominios contra lista blanca
 *
 * ⚠️ CRÍTICO: Valida dominios en tiempo real antes de operación IMAP
 */
class DomainValidator
{
    protected ?Setting $settings;
    protected ?AllowedEmail $allowedEmail;

    public function __construct(?AllowedEmail $allowedEmail = null)
    {
        $this->settings = Setting::first();
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

        // Extraer dominio del email
        $domain = $this->extractDomain($email);

        if (!$domain) {
            return false;
        }

        // Verificar contra lista blanca
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
     * Verificar si el dominio está en la lista blanca
     */
    public function isInWhitelist(string $domain): bool
    {
        // Verificar en AllowedEmail
        $allowedDomains = AllowedEmail::where('is_active', true)
            ->get()
            ->map(function ($item) {
                return $this->extractDomain($item->email);
            })
            ->filter()
            ->unique()
            ->toArray();

        // Dominios comunes autorizados (desde settings/configuración)
        $commonDomains = [
            'netbca.com',
            'anchasa.com',
            'primereca.xyz',
            'ipetercode.com',
            'example.com',
        ];

        $allDomains = array_merge($allowedDomains, $commonDomains);

        return in_array($domain, $allDomains, true);
    }

    /**
     * Obtener lista de dominios autorizados
     */
    public function getWhitelistedDomains(): array
    {
        // Dominios desde AllowedEmail
        $allowedDomains = AllowedEmail::where('is_active', true)
            ->get()
            ->map(function ($item) {
                return $this->extractDomain($item->email);
            })
            ->filter()
            ->unique()
            ->toArray();

        // Dominios desde la configuración
        $configDomains = $this->settings?->whitelisted_domains ?? [];

        return array_unique(array_merge($allowedDomains, $configDomains));
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
                "Solo se permiten dominios de la lista blanca."
            );
        }
    }

    /**
     * Obtener estado de validación del dominio
     */
    public function getValidationStatus(string $email): array
    {
        return [
            'email' => $email,
            'domain' => $this->extractDomain($email),
            'filter_enabled' => $this->isFilterEnabled(),
            'is_allowed' => $this->isDomainAllowed($email),
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
            'valid_format' => $this->isValidEmailFormat($email),
            'domain' => $this->extractDomain($email),
            'domain_allowed' => $this->isDomainAllowed($email),
            'can_proceed' => $this->isValidEmailFormat($email) && $this->isDomainAllowed($email),
        ];
    }
}
