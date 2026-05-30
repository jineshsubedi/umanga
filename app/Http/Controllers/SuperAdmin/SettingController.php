<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return \Inertia\Inertia::render('SuperAdmin/Settings/Index', [
            'app_name' => $settings['app_name'] ?? config('app.name'),
            'mail_host' => $settings['mail_host'] ?? '',
            'mail_port' => $settings['mail_port'] ?? '',
            'mail_username' => $settings['mail_username'] ?? '',
            'mail_password' => $settings['mail_password'] ?? '',
            'mail_encryption' => $settings['mail_encryption'] ?? '',
            'mail_from_address' => $settings['mail_from_address'] ?? '',
            'mail_from_name' => $settings['mail_from_name'] ?? '',
        ]);
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:255',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|string|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $keys = [
            'app_name', 'mail_host', 'mail_port', 'mail_username', 
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->$key]
                );
            }
        }

        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('logos', 'public');
            
            // Delete old logo if exists
            $oldLogo = \App\Models\Setting::where('key', 'app_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo->value);
            }

            \App\Models\Setting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
