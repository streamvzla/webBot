<?php

namespace App\Services;

class EmailCodeExtractor
{
    /**
     * Extrae un código o un link de acción del contenido del correo.
     * Devuelve un array con el tipo ('code', 'link' o 'html') y el valor.
     */
    public static function extract(string $html, string $text = ''): array
    {
        $content = $html ?: $text;
        // 1. Limpiar completamente <style> y <script> para evitar capturar colores hexadecimales como #707070
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $content);
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

        // 2. Netflix - Actualizar Hogar o Reset Password
        if (preg_match('/href="(https:\/\/[a-zA-Z0-9.-]*netflix\.com\/[^\s"]*(?:update|password|account)[^\s"]*)"/i', $content, $matches)) {
            return ['type' => 'link', 'value' => html_entity_decode($matches[1])];
        }
        
        // 3. Netflix - Link genérico de botón primario (el que suelen enviar para viaje o confirmación)
        if (preg_match('/href="(https:\/\/[a-zA-Z0-9.-]*netflix\.com\/[^\s"]*)"[^>]*>(?:Actualizar|Confirmar|Reset|Configurar|Continuar|Ver|Viaje|Estoy de viaje)[\s\w]*<\/a>/i', $content, $matches)) {
            return ['type' => 'link', 'value' => html_entity_decode($matches[1])];
        }

        // 4. Netflix - Cualquier otro enlace de acción de cuenta que parezca importante (Viaje)
        if (preg_match('/href="(https:\/\/[a-zA-Z0-9.-]*netflix\.com\/[^\s"]*(?:travel|temporary|verify|account|setup)[^\s"]*)"/i', $content, $matches)) {
            return ['type' => 'link', 'value' => html_entity_decode($matches[1])];
        }

        // 5. Spotify (Extraer código de 6 dígitos explícito si existe, o link de reset)
        if (preg_match('/(?:código|code)[^\d]{0,40}(?<!\d)([0-9]{6})(?!\d)/is', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => $matches[1]];
        }
        if (preg_match('/href="(https:\/\/[a-zA-Z0-9.-]*spotify\.com\/(?:password-reset|login|action)[^\s"]*)"/i', $content, $matches)) {
            return ['type' => 'link', 'value' => html_entity_decode($matches[1])];
        }

        // 6. Disney+ Regex Específico (ignora el "15 minutos")
        if (preg_match('/(?:c(?:ó|o)digo(?: de acceso)?(?: único)?|code|kode).*?\b([0-9]{6,8})\b/is', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => trim($matches[1])];
        }

        // 7. Códigos de Google (G-XXXXXX)
        if (preg_match('/(?:G-|g-)([0-9]{6})/i', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => 'G-' . $matches[1]];
        }
        
        // 8. Códigos numéricos de 6 dígitos genéricos (aislados)
        if (preg_match('/(?<!\d)([0-9]{6})(?!\d)/', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => $matches[1]];
        }

        // 9. Códigos de 8 dígitos alfanuméricos en mayúsculas (Ej. MAX / Amazon)
        if (preg_match('/(?<!\w)([A-Z0-9]{8})(?!\w)/', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => $matches[1]];
        }

        // 6. Códigos de 4 dígitos (Netflix u otros, buscar cerca de palabras clave como código o code)
        if (preg_match('/(?:código|code)[^\d]{0,20}(?<!\d)([0-9]{4})(?!\d)/i', strip_tags($content), $matches)) {
            return ['type' => 'code', 'value' => $matches[1]];
        }
        
        // Fallback genérico de 4 dígitos (con más cuidado)
        if (preg_match('/(?<!\d)([0-9]{4})(?!\d)/', strip_tags($content), $matches)) {
             // Ignorar años comunes
             $val = (int)$matches[1];
             if ($val < 2020 || $val > 2030) {
                 return ['type' => 'code', 'value' => $matches[1]];
             }
        }

        // FALLBACK: Retorna todo el HTML para renderizarlo completo si no se detecta patrón
        return ['type' => 'html', 'value' => $html];
    }
}
