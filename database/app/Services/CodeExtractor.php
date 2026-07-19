<?php

namespace App\Services;

/**
 * CodeExtractor - Extracción de códigos de verificación usando Regex
 *
 * ⚠️ ADVERTENCIA: Los Regex de extracción deben actualizarse periódicamente
 * ya que los servicios cambian sus templates de email.
 */
class CodeExtractor
{
    /**
     * Patrones de regex por plataforma
     * Cada plataforma puede tener múltiples patrones para diferentes tipos de códigos
     */
    protected static array $patterns = [
        'netflix' => [
            // Código de verificación de 4-6 caracteres alfanuméricos
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            // Código temporal de Netflix
            '/c(?:ó|o)digo\s*temporal[:\s]*([A-Z0-9]{4,8})/i',
            // Tu código de acceso
            '/tu\s*c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            // Código de verificación
            '/c(?:ó|o)digo\s*de\s*verificaci(?:ó|o)n[:\s]*([A-Z0-9]{4,8})/i',
            // Netflix verification code
            '/verification\s*code[:\s]*([A-Z0-9]{4,8})/i',
            // Código de 6 dígitos numéricos (común en confirmaciones)
            '/:\\s*([0-9]{6})/',
            // Código de 6 dígitos al final
            '/([0-9]{6})/',
            // Código de 4-6 dígitos en contexto de confirmación
            '/c(?:ó|o)digo\s*de\s*confirmaci(?:ó|o)n[:\s]*([0-9]{4,6})/i',
        ],
        'spotify' => [
            // Código de Spotify
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{6})/i',
            // Spotify verification
            '/spotify\s*code[:\s]*([A-Z0-9]{6})/i',
            // Código de 6 caracteres
            '/([A-Z0-9]{6})/',
        ],
        'primevideo' => [
            // Prime Video code
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{5,8})/i',
            '/prime\s*video\s*code[:\s]*([A-Z0-9]{5,8})/i',
        ],
        'max' => [
            // Max (HBO Max) code
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            '/max\s*verification[:\s]*([A-Z0-9]{4,8})/i',
        ],
        'disney' => [
            // Disney+ code
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            '/disney\s*verification[:\s]*([A-Z0-9]{4,8})/i',
        ],
        'hbo' => [
            // HBO code
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            '/hbo\s*verification[:\s]*([A-Z0-9]{4,8})/i',
        ],
        'apple' => [
            // Apple ID code
            '/c(?:ó|o)digo[:\s]*([0-9]{6})/i',
            '/apple\s*id\s*verification[:\s]*([0-9]{6})/i',
        ],
        'amazon' => [
            // Amazon code
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{6})/i',
            '/amazon\s*verification[:\s]*([A-Z0-9]{6})/i',
        ],
        'default' => [
            // Patrón genérico para cualquier código de verificación
            '/c(?:ó|o)digo[:\s]*([A-Z0-9]{4,8})/i',
            '/verification\s*code[:\s]*([A-Z0-9]{4,8})/i',
            '/c(?:ó|o)digo\s*de\s*acceso[:\s]*([A-Z0-9]{4,8})/i',
            '/([0-9]{4})/', // Código de 4 dígitos común
            '/([0-9]{6})/', // Código de 6 dígitos común
        ],
    ];

    /**
     * Extraer código de verificación del cuerpo del email
     */
    public static function extract(string $body, string $platform = 'default'): ?string
    {
        $patterns = self::$patterns[$platform] ?? self::$patterns['default'];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $body, $matches)) {
                $code = trim($matches[1]);

                // Validar que el código parezca válido (no vacío, formato correcto)
                if (!empty($code) && strlen($code) >= 4 && strlen($code) <= 12) {
                    return $code;
                }
            }
        }

        // Si no encontró con patrones específicos, intentar con patrones genéricos
        foreach (self::$patterns['default'] as $pattern) {
            if (preg_match($pattern, $body, $matches)) {
                $code = trim($matches[1]);
                if (!empty($code) && strlen($code) >= 4 && strlen($code) <= 12) {
                    return $code;
                }
            }
        }

        return null;
    }

    /**
     * Extraer múltiples códigos (para debugging)
     */
    public static function extractAll(string $body, string $platform = 'default'): array
    {
        $patterns = self::$patterns[$platform] ?? self::$patterns['default'];
        $codes = [];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $body, $matches)) {
                foreach ($matches[1] as $match) {
                    $code = trim($match);
                    if (!empty($code) && strlen($code) >= 4 && strlen($code) <= 12) {
                        $codes[] = $code;
                    }
                }
            }
        }

        return array_unique($codes);
    }

    /**
     * Agregar patrón personalizado para una plataforma
     */
    public static function addPattern(string $platform, string $pattern): void
    {
        self::$patterns[$platform][] = $pattern;
    }

    /**
     * Obtener patrones de una plataforma
     */
    public static function getPatterns(string $platform): array
    {
        return self::$patterns[$platform] ?? self::$patterns['default'];
    }

    /**
     * Validar formato de código
     */
    public static function validateCode(string $code): bool
    {
        // Códigos típicos son 4-8 caracteres alfanuméricos
        return preg_match('/^[A-Z0-9]{4,8}$/i', $code) === 1;
    }

    /**
     * Enmascarar código para mostrar parcialmente
     */
    public static function maskCode(string $code): string
    {
        if (strlen($code) <= 4) {
            return str_repeat('*', strlen($code));
        }

        return substr($code, 0, 2) . str_repeat('*', strlen($code) - 4) . substr($code, -2);
    }

    /**
     * Normalizar texto para búsqueda
     */
    public static function normalizeText(string $text): string
    {
        // Convertir a minúsculas
        $text = mb_strtolower($text, 'UTF-8');

        // Reemplazar caracteres especiales comunes
        $replacements = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n',
        ];

        return strtr($text, $replacements);
    }
}
