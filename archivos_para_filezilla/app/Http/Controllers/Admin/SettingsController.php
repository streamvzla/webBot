<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingsController
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = [
            'site_name' => Setting::get(Setting::KEY_SITE_NAME, 'WinicSistem'),
            'site_logo' => Setting::get(Setting::KEY_SITE_LOGO, ''),
            'seo_title' => Setting::get(Setting::KEY_SEO_TITLE, 'Code Verification System'),
            'seo_description' => Setting::get(Setting::KEY_SEO_DESCRIPTION, ''),
            'vendor_id' => Setting::get(Setting::KEY_VENDOR_ID, ''),
            'email_filter_enabled' => Setting::get(Setting::KEY_EMAIL_FILTER_ENABLED, false),
            'query_cooldown_minutes' => Setting::get(Setting::KEY_QUERY_COOLDOWN_MINUTES, 30),
            
            // Colores de Marca Blanca (Fase 6)
            'theme_color_primary' => Setting::get('theme_color_primary', '#6366f1'),
            'theme_color_secondary' => Setting::get('theme_color_secondary', '#8b5cf6'),
            'theme_bg_start' => Setting::get('theme_bg_start', '#1e1b4b'),
            'theme_bg_end' => Setting::get('theme_bg_end', '#020617'),
        ];

        // Redes sociales ahora se obtienen por administrador (Fase 6 Multi-tenant)
        $user = auth()->user();

        return view('admin.settings', compact('settings', 'user'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->all();

        $user = auth()->user();

        if ($user && $user->id === 1) {
            // Update site name
            if (isset($validated['site_name'])) Setting::set(Setting::KEY_SITE_NAME, $validated['site_name']);

            // Handle logo upload
            if ($request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
                try {
                    $oldLogo = Setting::get(Setting::KEY_SITE_LOGO, '');
                    if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                        Storage::disk('public')->delete($oldLogo);
                    }
                    $logoPath = $request->file('site_logo')->store('logos', 'public');
                    Setting::set(Setting::KEY_SITE_LOGO, $logoPath);
                    Log::info('Logo uploaded successfully', ['path' => $logoPath]);
                } catch (\Exception $e) {
                    Log::error('Error uploading logo', ['error' => $e->getMessage()]);
                    return redirect()->back()
                        ->with('error', 'Error al subir el logo: ' . $e->getMessage())
                        ->withInput();
                }
            }

            // Delete logo if checkbox is checked
            if (isset($validated['delete_logo'])) {
                $oldLogo = Setting::get(Setting::KEY_SITE_LOGO, '');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                Setting::set(Setting::KEY_SITE_LOGO, '');
            }

            // Update global settings
            if (isset($validated['email_filter_enabled'])) Setting::set(Setting::KEY_EMAIL_FILTER_ENABLED, $validated['email_filter_enabled']);
            if (isset($validated['query_cooldown_minutes'])) Setting::set(Setting::KEY_QUERY_COOLDOWN_MINUTES, $validated['query_cooldown_minutes']);
            if (isset($validated['seo_title'])) Setting::set(Setting::KEY_SEO_TITLE, $validated['seo_title']);
            if (isset($validated['seo_description'])) Setting::set(Setting::KEY_SEO_DESCRIPTION, $validated['seo_description']);
            if (isset($validated['vendor_id'])) Setting::set(Setting::KEY_VENDOR_ID, $validated['vendor_id']);

            // Actualizar colores (Fase 6)
            if (isset($validated['theme_color_primary'])) Setting::set('theme_color_primary', $validated['theme_color_primary']);
            if (isset($validated['theme_color_secondary'])) Setting::set('theme_color_secondary', $validated['theme_color_secondary']);
            if (isset($validated['theme_bg_start'])) Setting::set('theme_bg_start', $validated['theme_bg_start']);
            if (isset($validated['theme_bg_end'])) Setting::set('theme_bg_end', $validated['theme_bg_end']);
        }

        // Guardar redes sociales PER-ADMIN
        if ($user) {
            $user->whatsapp = $validated['whatsapp'] ?? null;
            $user->telegram = $validated['telegram'] ?? null;
            $user->website = $validated['website'] ?? null;
            // Para mantener compatibilidad con whatsapp_message (opcional), 
            // aunque podemos integrar el mensaje en el propio link the whatsapp (wa.me/numero?text=hola)
            // Aquí lo guardaremos como un json si queremos, pero dejémoslo simple por ahora.
            $user->save();
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Configuración actualizada exitosamente.');
    }
}
