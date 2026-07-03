<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:512',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            // QuickBooks API Configuration
            'quickbooks_client_id' => 'nullable|string|max:255',
            'quickbooks_client_secret' => 'nullable|string|max:255',
            'quickbooks_company_id' => 'nullable|string|max:255',
            'quickbooks_environment' => 'nullable|in:sandbox,production',
            'quickbooks_access_token' => 'nullable|string',
            'quickbooks_refresh_token' => 'nullable|string',
            'quickbooks_enabled' => 'nullable|boolean',
            // Bank API Configuration
            'bank_api_provider' => 'nullable|string|max:255',
            'bank_api_key' => 'nullable|string|max:255',
            'bank_api_secret' => 'nullable|string|max:255',
            'bank_account_id' => 'nullable|string|max:255',
            'bank_sync_enabled' => 'nullable|boolean',
            // Square Payment
            'square_application_id' => 'nullable|string|max:255',
            'square_access_token' => 'nullable|string|max:255',
            'square_location_id' => 'nullable|string|max:255',
            'square_environment' => 'nullable|in:sandbox,production',
            'square_enabled' => 'nullable|boolean',
            // Instructor Bios
            'jayson_bio' => 'nullable|string',
            'kenny_bio' => 'nullable|string',
            'sms_enabled' => 'nullable|boolean',
            'twilio_sid' => 'nullable|string|max:255',
            'twilio_token' => 'nullable|string|max:255',
            'twilio_from_number' => 'nullable|string|max:50',
        ]);

        $settings = SiteSetting::first();

        // Handle file uploads
        if ($request->hasFile('header_logo')) {
            if ($settings && $settings->header_logo) {
                Storage::disk('public')->delete($settings->header_logo);
            }
            $validated['header_logo'] = $request->file('header_logo')->store('settings', 'public');
        }

        if ($request->hasFile('footer_logo')) {
            if ($settings && $settings->footer_logo) {
                Storage::disk('public')->delete($settings->footer_logo);
            }
            $validated['footer_logo'] = $request->file('footer_logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings && $settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        // Handle boolean fields
        $validated['quickbooks_enabled'] = $request->has('quickbooks_enabled') ? true : false;
        $validated['bank_sync_enabled'] = $request->has('bank_sync_enabled') ? true : false;
        $validated['square_enabled'] = $request->has('square_enabled') ? true : false;
        $validated['sms_enabled'] = $request->has('sms_enabled') ? true : false;

        // Remove null values to avoid overwriting with null (except for boolean fields)
        $validated = array_filter($validated, function ($value, $key) {
            // Keep boolean false values
            if (in_array($key, ['quickbooks_enabled', 'bank_sync_enabled', 'square_enabled', 'sms_enabled'])) {
                return true;
            }
            if ($value === null) {
                return false;
            }
            // Don't overwrite secrets/tokens with empty string (user left password field blank)
            $secretKeys = [
                'quickbooks_client_secret', 'quickbooks_access_token', 'quickbooks_refresh_token',
                'bank_api_secret', 'square_access_token', 'twilio_token',
            ];
            if (in_array($key, $secretKeys) && trim((string) $value) === '') {
                return false;
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        if ($settings) {
            $settings->update($validated);
        } else {
            SiteSetting::create($validated);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
