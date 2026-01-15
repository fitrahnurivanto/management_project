<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $settings = [
            'company_logo' => Setting::get('company_logo'),
            'company_name' => Setting::get('company_name', 'Creativemu'),
            'company_director' => Setting::get('company_director', 'Agus Susanto'),
            'company_address' => Setting::get('company_address'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_director' => 'required|string',
            'company_address' => 'required|string',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Update text settings
        Setting::set('company_name', $validated['company_name']);
        Setting::set('company_director', $validated['company_director']);
        Setting::set('company_address', $validated['company_address']);

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo) {
                Storage::delete('public/' . $oldLogo);
            }

            // Store new logo
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $path);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
