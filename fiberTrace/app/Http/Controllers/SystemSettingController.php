<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Super-admin: view all platform settings.
     * Settings are key-value pairs stored in system_settings table.
     */
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get()->keyBy('key');

        return view('super-admin.settings', compact('settings'));
    }

    /**
     * Super-admin: update one or multiple settings at once.
     * Form sends an array: settings[key] = value
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = SystemSetting::firstOrNew(['key' => $key]);
            $setting->value = $value;
            $setting->updated_by = auth()->id();
            $setting->save();

            ActivityLog::create([
                'user_id'      => auth()->id(),
                'action'       => 'setting_updated',
                'subject_type' => SystemSetting::class,
                'subject_id'   => $setting->id,
                'description'  => "Updated setting '{$key}' to '{$value}'",
                'ip_address'   => $request->ip(),
            ]);
        }

        return back()->with('success', 'Platform settings saved successfully.');
    }
}
