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
        ]);
    }

    public function update(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => $request->app_name]
        );

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
