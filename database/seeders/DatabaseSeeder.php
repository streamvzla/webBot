<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Platform;
use App\Models\PlatformSubject;
use App\Models\AllowedEmail;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@consultor.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create test user
        $user = User::create([
            'name' => 'Usuario Test',
            'username' => 'testuser',
            'email' => 'test@consultor.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);

        // Create platforms
        $platforms = [
            [
                'name' => 'Netflix',
                'slug' => 'netflix',
                'description' => 'Plataforma de streaming de películas y series',
                'color' => '#E50914',
                'is_active' => true,
            ],
            [
                'name' => 'Prime Video',
                'slug' => 'prime-video',
                'description' => 'Servicio de streaming de Amazon',
                'color' => '#00A8E1',
                'is_active' => true,
            ],
            [
                'name' => 'Spotify',
                'slug' => 'spotify',
                'description' => 'Plataforma de música en streaming',
                'color' => '#1DB954',
                'is_active' => true,
            ],
            [
                'name' => 'Max',
                'slug' => 'max',
                'description' => 'Plataforma de HBO (anteriormente HBO Max)',
                'color' => '#5C1A7C',
                'is_active' => true,
            ],
            [
                'name' => 'Disney+',
                'slug' => 'disney-plus',
                'description' => 'Plataforma de Disney, Pixar, Marvel y Star Wars',
                'color' => '#113CCF',
                'is_active' => true,
            ],
            [
                'name' => 'Apple TV+',
                'slug' => 'apple-tv',
                'description' => 'Plataforma de Apple para series y películas originales',
                'color' => '#000000',
                'is_active' => true,
            ],
        ];

        foreach ($platforms as $platformData) {
            $platform = Platform::create($platformData);

            // Add subjects for each platform
            $subjects = $this->getSubjectsForPlatform($platform->slug);
            foreach ($subjects as $subject) {
                PlatformSubject::create([
                    'platform_id' => $platform->id,
                    'subject' => $subject,
                    'pattern' => $this->getPatternForPlatform($platform->slug),
                    'is_active' => true,
                ]);
            }
        }

        // Create allowed emails (as mentioned in the requirements)
        $allowedEmails = [
            ['email' => 'admin@netbca.com', 'description' => 'Correo corporativo netbca'],
            ['email' => 'admin@anchasa.com', 'description' => 'Correo corporativo anchasa'],
            ['email' => 'admin@primereca.xyz', 'description' => 'Correo corporativo primereca'],
            ['email' => 'admin@ipetercode.com', 'description' => 'Correo corporativo ipetercode'],
            ['email' => 'test@consultor.test', 'description' => 'Correo de prueba'],
        ];

        foreach ($allowedEmails as $emailData) {
            AllowedEmail::create($emailData);
        }

        // Create settings
        $settings = [
            ['key' => 'email_filter_enabled', 'value' => 'true', 'type' => 'boolean', 'description' => 'Activar filtro de correos autorizados'],
            ['key' => 'query_cooldown_minutes', 'value' => '30', 'type' => 'integer', 'description' => 'Tiempo mínimo entre consultas (minutos)'],
            ['key' => 'seo_title', 'value' => 'Consulta tu Código - Sistema de Verificación', 'type' => 'string', 'description' => 'Título SEO del sitio'],
            ['key' => 'seo_description', 'value' => 'Obtén tu código de verificación para servicios de streaming', 'type' => 'string', 'description' => 'Descripción SEO del sitio'],
            ['key' => 'contact_web_url', 'value' => 'https://ejemplo.com', 'type' => 'string', 'description' => 'URL del sitio web de contacto'],
            ['key' => 'contact_telegram', 'value' => 'https://t.me/ejemplo', 'type' => 'string', 'description' => 'Enlace de Telegram'],
            ['key' => 'contact_whatsapp', 'value' => 'https://wa.me/1234567890', 'type' => 'string', 'description' => 'Enlace de WhatsApp'],
            ['key' => 'whatsapp_message', 'value' => 'Hola, necesito ayuda con mi código', 'type' => 'string', 'description' => 'Mensaje predefinido para WhatsApp'],
            ['key' => 'vendor_id', 'value' => 'VND-001', 'type' => 'string', 'description' => 'ID del vendedor'],
            ['key' => 'default_imap_host', 'value' => 'imap.hostinger.com', 'type' => 'string', 'description' => 'Host IMAP por defecto'],
            ['key' => 'default_imap_port', 'value' => '993', 'type' => 'integer', 'description' => 'Puerto IMAP por defecto'],
            ['key' => 'default_imap_encryption', 'value' => 'ssl', 'type' => 'string', 'description' => 'Tipo de encriptación IMAP'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }

    private function getSubjectsForPlatform(string $slug): array
    {
        return match ($slug) {
            'netflix' => [
                'Tu código de acceso temporal de Netflix',
                'Código de verificación de Netflix',
                'Netflix - Código de inicio de sesión',
                'Recuperación de cuenta Netflix',
            ],
            'prime-video' => [
                'Código de verificación de Prime Video',
                'Prime Video - Tu código de acceso',
                'Recuperación de cuenta Prime Video',
            ],
            'spotify' => [
                'Código de verificación de Spotify',
                'Spotify - Restablece tu contraseña',
                'Código de acceso Spotify',
            ],
            'max' => [
                'Código de verificación de Max',
                'Max - Tu código de acceso',
                'Recuperación de cuenta HBO Max',
            ],
            'disney-plus' => [
                'Código de verificación de Disney+',
                'Disney+ - Tu código de acceso',
                'Recuperación de cuenta Disney+',
            ],
            'apple-tv' => [
                'Código de verificación de Apple TV',
                'Apple TV+ - Código de acceso',
                'Recuperación de cuenta Apple',
            ],
            default => [
                'Código de verificación',
                'Tu código de acceso',
            ],
        };
    }

    private function getPatternForPlatform(string $slug): ?string
    {
        return match ($slug) {
            'netflix' => '/\b(\d{4,6})\b/',
            'prime-video' => '/\b(\d{4,6})\b/',
            'spotify' => '/\b([A-Z0-9]{6,12})\b/i',
            'max' => '/\b(\d{4,6})\b/',
            'disney-plus' => '/\b(\d{4,6})\b/',
            'apple-tv' => '/\b(\d{4,6})\b/',
            default => null,
        };
    }
}
