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
            'mail_password' => '',
            'mail_encryption' => $settings['mail_encryption'] ?? '',
            'mail_from_address' => $settings['mail_from_address'] ?? '',
            'mail_from_name' => $settings['mail_from_name'] ?? '',
            
            'gmail_host' => $settings['gmail_host'] ?? '',
            'gmail_port' => $settings['gmail_port'] ?? '',
            'gmail_username' => $settings['gmail_username'] ?? '',
            'gmail_password' => '',
            'gmail_encryption' => $settings['gmail_encryption'] ?? '',
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
            'gmail_host' => 'nullable|string|max:255',
            'gmail_port' => 'nullable|string|max:255',
            'gmail_username' => 'nullable|string|max:255',
            'gmail_password' => 'nullable|string|max:255',
            'gmail_encryption' => 'nullable|string|max:255',
        ]);

        $keys = [
            'app_name', 'mail_host', 'mail_port', 'mail_username', 
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
            'gmail_host', 'gmail_port', 'gmail_username', 'gmail_password', 'gmail_encryption'
        ];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                $value = $request->$key;
                
                // Don't update passwords if they are empty
                if (in_array($key, ['mail_password', 'gmail_password']) && ($value === '' || is_null($value))) {
                    continue;
                }

                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
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
