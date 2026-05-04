<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuickBooksController extends Controller
{
    public function connect()
    {
        $clientId = SiteSetting::first()->quickbooks_client_id;
        $redirectUri = $this->quickBooksRedirectUri();

        $url = 'https://appcenter.intuit.com/connect/oauth2?'.http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'scope' => 'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment',
            'redirect_uri' => $redirectUri,
            'state' => csrf_token(),
        ]);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        $settings = SiteSetting::first();
        if (! $settings) {
            return redirect()->route('admin.settings.index')->with('error', 'Site settings not found.');
        }

        $code = $request->query('code') ?? $request->code;
        $realmId = $request->query('realmId') ?? $request->realmId;
        $error = $request->query('error');

        if ($error) {
            $errorDesc = $request->query('error_description', $error);

            return redirect()->route('admin.settings.index')->with('error', 'QuickBooks: '.$errorDesc);
        }

        if (! $code) {
            $callbackUrl = $this->quickBooksRedirectUri();
            $hint = 'QuickBooks did not return an authorization code. Add this exact Redirect URI in your Intuit app (and ensure APP_URL / QUICKBOOKS_REDIRECT_URI matches it): '.$callbackUrl;

            return redirect()->route('admin.settings.index')->with('error', $hint);
        }

        $clientId = $settings->quickbooks_client_id;
        $clientSecret = $settings->quickbooks_client_secret;

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('admin.settings.index')->with('error', 'QuickBooks Client ID and Client Secret must be set in Site Settings first.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.base64_encode($clientId.':'.$clientSecret),
            'Accept' => 'application/json',
        ])->asForm()->post(
            'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->quickBooksRedirectUri(),
            ]
        );

        $data = $response->json();

        if (! $response->successful()) {
            $message = $data['error_description'] ?? $data['error'] ?? $response->body();

            return redirect()->route('admin.settings.index')->with('error', 'QuickBooks connection failed: '.$message);
        }

        if (empty($data['access_token']) || empty($data['refresh_token'])) {
            return redirect()->route('admin.settings.index')->with('error', 'QuickBooks did not return access token. Please try connecting again.');
        }

        $settings->update([
            'quickbooks_access_token' => $data['access_token'],
            'quickbooks_refresh_token' => $data['refresh_token'],
            'quickbooks_company_id' => $realmId,
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'QuickBooks connected successfully.');
    }

    private function quickBooksRedirectUri(): string
    {
        $override = config('services.quickbooks.redirect_uri');

        if (is_string($override) && trim($override) !== '') {
            return trim($override);
        }

        return url('/admin/quickbooks/callback');
    }
}
