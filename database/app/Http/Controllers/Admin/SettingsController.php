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
            'web_url' => Setting::get(Setting::KEY_WEB_URL, ''),
            'telegram_url' => Setting::get(Setting::KEY_TELEGRAM_URL, ''),
            'whatsapp_url' => Setting::get(Setting::KEY_WHATSAPP_URL, ''),
            'whatsapp_message' => Setting::get(Setting::KEY_WHATSAPP_MESSAGE, ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->all();

        // Update site name
        Setting::set(Setting::KEY_SITE_NAME, $validated['site_name'] ?? 'WinicSistem');

        // Handle logo upload
        if ($request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
            try {
                // Delete old logo if exists
                $oldLogo = Setting::get(Setting::KEY_SITE_LOGO, '');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                // Store new logo
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

        // Update other settings
        Setting::set(Setting::KEY_EMAIL_FILTER_ENABLED, $validated['email_filter_enabled'] ?? false);
        Setting::set(Setting::KEY_QUERY_COOLDOWN_MINUTES, $validated['query_cooldown_minutes'] ?? 30);
        Setting::set(Setting::KEY_SEO_TITLE, $validated['seo_title'] ?? 'Code Verification System');
        Setting::set(Setting::KEY_SEO_DESCRIPTION, $validated['seo_description'] ?? '');
        Setting::set(Setting::KEY_WEB_URL, $validated['web_url'] ?? '');
        Setting::set(Setting::KEY_TELEGRAM_URL, $validated['telegram_url'] ?? '');
        Setting::set(Setting::KEY_WHATSAPP_URL, $validated['whatsapp_url'] ?? '');
        Setting::set(Setting::KEY_WHATSAPP_MESSAGE, $validated['whatsapp_message'] ?? '');
        Setting::set(Setting::KEY_VENDOR_ID, $validated['vendor_id'] ?? '');

        return redirect()->route('admin.settings')
            ->with('success', 'Configuración actualizada exitosamente.');
    }
}
