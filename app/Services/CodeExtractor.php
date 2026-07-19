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
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode|passcode).*?\b([0-9]{4,8})\b/is',
            '/(?:temporal|temporary|midlertidig).*?\b([0-9]{4,8})\b/is',
            '/(?:verificaci(?:ó|o)n|verification|bestätigung|bekræftelse).*?\b([0-9]{4,8})\b/is',
            '/\b([0-9]{6})\b/',
        ],
        'spotify' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([A-Z0-9]{6})\b/is',
            '/spotify.*(?:code|kode).*?\b([A-Z0-9]{6})\b/is',
            '/\b([A-Z0-9]{6})\b/',
        ],
        'primevideo' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([0-9]{5,8})\b/is',
            '/prime\s*video.*(?:code|kode).*?\b([0-9]{5,8})\b/is',
        ],
        'max' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([0-9]{4,8})\b/is',
            '/max.*(?:verification|code|kode).*?\b([0-9]{4,8})\b/is',
        ],
        'disney' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?(?: único)?|code|kode).*?\b([0-9]{6,8})\b/is',
            '/disney.*(?:verification|code|kode).*?\b([0-9]{6,8})\b/is',
        ],
        'hbo' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([0-9]{4,8})\b/is',
            '/hbo.*(?:verification|code|kode).*?\b([0-9]{4,8})\b/is',
        ],
        'apple' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([0-9]{6})\b/is',
            '/apple.*(?:verification|code|kode).*?\b([0-9]{6})\b/is',
        ],
        'amazon' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?|code|kode).*?\b([0-9]{6})\b/is',
            '/amazon.*(?:verification|code|kode).*?\b([0-9]{6})\b/is',
        ],
        'default' => [
            '/(?:c(?:ó|o)digo(?: de acceso)?(?: único)?|code|kode|pin|token).*?\b([0-9]{4,8})\b/is',
            '/(?:verificaci(?:ó|o)n|verification|bestätigung|bekræftelse).*?\b([0-9]{4,8})\b/is',
            '/(?:acceso|access).*?\b([0-9]{4,8})\b/is',
            '/\b([0-9]{6})\b/',
            '/\b([0-9]{8})\b/',
            '/\b([0-9]{4})\b/',
        ],
    ];

    /**
     * Extraer código de verificación del cuerpo del email
     */
    public static function extract(string $body, string $platform = 'default'): ?string
    {
        $patterns = self::$patterns[$platform] ?? self::$patterns['default'];
        
        // Primero remover contenido de <style> y <script> completamente
        $cleanBody = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $body);
        $cleanBody = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $cleanBody);
        
        // Limpiar el resto del HTML
        $cleanBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</div>', '</p>', '</tr>', '</td>'], " \n ", $cleanBody));

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanBody, $matches)) {
                $code = trim($matches[1]);

                // Validar que el código parezca válido (no vacío, formato correcto)
                if (!empty($code) && strlen($code) >= 4 && strlen($code) <= 12) {
                    return $code;
                }
            }
        }

        // Si no encontró con patrones específicos, intentar con patrones genéricos
        foreach (self::$patterns['default'] as $pattern) {
            if (preg_match($pattern, $cleanBody, $matches)) {
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
