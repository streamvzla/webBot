<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public $timestamps = false;

    /**
     * Constantes de clave para el modelo Setting
     */
    // Branding
    const KEY_SITE_NAME = 'site_name';
    const KEY_SITE_LOGO = 'site_logo';

    // SEO
    const KEY_SEO_TITLE = 'seo_title';
    const KEY_SEO_DESCRIPTION = 'seo_description';

    // Seguridad
    const KEY_EMAIL_FILTER_ENABLED = 'email_filter_enabled';
    const KEY_QUERY_COOLDOWN_MINUTES = 'query_cooldown_minutes';

    // Contacto
    const KEY_WEB_URL = 'web_url';
    const KEY_TELEGRAM_URL = 'telegram_url';
    const KEY_WHATSAPP_URL = 'whatsapp_url';
    const KEY_WHATSAPP_MESSAGE = 'whatsapp_message';

    // Sistema
    const KEY_VENDOR_ID = 'vendor_id';

    /**
     * Constantes de valor para el modelo Setting (alias para compatibilidad)
     */
    const SITE_NAME = 'site_name';
    const SITE_LOGO = 'site_logo';
    const SEO_TITLE = 'seo_title';
    const SEO_DESCRIPTION = 'seo_description';
    const EMAIL_FILTER_ENABLED = 'email_filter_enabled';
    const QUERY_COOLDOWN_MINUTES = 'query_cooldown_minutes';
    const WEB_URL = 'web_url';
    const TELEGRAM_URL = 'telegram_url';
    const WHATSAPP_URL = 'whatsapp_url';
    const WHATSAPP_MESSAGE = 'whatsapp_message';
    const VENDOR_ID = 'vendor_id';

    /**
     * Valores por defecto para nuevas instalaciones
     */
    public static function defaults(): array
    {
        return [
            // Branding
            self::SITE_NAME => config('app.name', 'Consultor'),
            self::SITE_LOGO => null,

            // SEO
            self::SEO_TITLE => config('app.name', 'Consultor'),
            self::SEO_DESCRIPTION => 'Plataforma de consultas inteligentes',

            // Seguridad
            self::EMAIL_FILTER_ENABLED => false,
            self::QUERY_COOLDOWN_MINUTES => 5,

            // Contacto
            self::WEB_URL => '',
            self::TELEGRAM_URL => '',
            self::WHATSAPP_URL => '',
            self::WHATSAPP_MESSAGE => '',

            // Sistema
            self::VENDOR_ID => '',
        ];
    }

    /**
     * Obtener todos los settings como un array asociativo
     */
    public static function getAllSettings(): array
    {
        $settings = self::pluck('value', 'key')->toArray();
        return array_merge(self::defaults(), $settings);
    }

    /**
     * Obtener un setting específico
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Establecer un setting
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Inicializar settings por defecto
     */
    public static function initializeDefaults(): void
    {
        foreach (self::defaults() as $key => $value) {
            self::set($key, $value);
        }
    }
}
