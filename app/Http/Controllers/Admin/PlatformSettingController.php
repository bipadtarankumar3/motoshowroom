<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    /**
     * Display the platform configuration panel.
     */
    public function index(): View
    {
        $settings = [
            // General
            'platform_name' => PlatformSetting::get('platform_name', config('app.name', 'MotoShowroom SaaS')),
            'support_email' => PlatformSetting::get('support_email', 'support@motoshowroom.example'),
            'platform_currency' => PlatformSetting::get('platform_currency', 'INR'),
            'currency_symbol' => PlatformSetting::get('currency_symbol', '₹'),

            // Subscription Defaults
            'default_trial_days' => PlatformSetting::get('default_trial_days', '14'),
            'trial_requires_card' => PlatformSetting::get('trial_requires_card', '0'),

            // Feature Flags
            'allow_custom_domains' => PlatformSetting::get('allow_custom_domains', '1'),
            'allow_360_viewer' => PlatformSetting::get('allow_360_viewer', '1'),
            'allow_ai_assistant' => PlatformSetting::get('allow_ai_assistant', '1'),

            // System Security & Maintenance
            'allow_registration' => PlatformSetting::get('allow_registration', '1'),
            'maintenance_mode' => PlatformSetting::get('maintenance_mode', '0'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Save platform configuration updates.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'platform_currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'default_trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'trial_requires_card' => ['nullable'],
            'allow_custom_domains' => ['nullable'],
            'allow_360_viewer' => ['nullable'],
            'allow_ai_assistant' => ['nullable'],
            'allow_registration' => ['nullable'],
            'maintenance_mode' => ['nullable'],
        ]);

        $settingsMap = [
            'platform_name' => ['general', 'Public platform brand name'],
            'support_email' => ['general', 'Central platform contact/support email'],
            'platform_currency' => ['general', 'Default ISO currency code'],
            'currency_symbol' => ['general', 'Currency symbol used on storefronts'],
            'default_trial_days' => ['subscriptions', 'Standard free trial duration in days'],
            'trial_requires_card' => ['subscriptions', 'Require payment card on trial signup (0 or 1)'],
            'allow_custom_domains' => ['features', 'Enable custom domains for premium tiers (0 or 1)'],
            'allow_360_viewer' => ['features', 'Enable interactive 360-degree viewer (0 or 1)'],
            'allow_ai_assistant' => ['features', 'Enable virtual showroom assistant (0 or 1)'],
            'allow_registration' => ['system', 'Allow public user self-registration (0 or 1)'],
            'maintenance_mode' => ['system', 'Emergency maintenance mode toggle (0 or 1)'],
        ];

        $oldSettings = [];
        $newSettings = [];

        foreach ($settingsMap as $key => [$group, $desc]) {
            $oldSettings[$key] = PlatformSetting::get($key);

            $val = match ($key) {
                'trial_requires_card',
                'allow_custom_domains',
                'allow_360_viewer',
                'allow_ai_assistant',
                'allow_registration',
                'maintenance_mode' => $request->has($key) ? '1' : '0',
                default => (string) ($validated[$key] ?? ''),
            };

            PlatformSetting::set($key, $val, $group, $desc);
            $newSettings[$key] = $val;
        }

        PlatformSetting::flushCache();

        AuditLogger::log(
            action: 'platform.settings_updated',
            auditable: null,
            oldValues: $oldSettings,
            newValues: $newSettings,
            tenantId: null
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'Platform settings saved successfully.');
    }
}
