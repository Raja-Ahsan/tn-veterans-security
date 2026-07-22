<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuickBooksPaymentsService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = SiteSetting::first();
    }

    /**
     * Check if QuickBooks Payments is enabled and configured.
     */
    public function isEnabled(): bool
    {
        if (! $this->settings || ! $this->settings->quickbooks_enabled) {
            return false;
        }
        $clientId = trim((string) ($this->settings->quickbooks_client_id ?? ''));
        $clientSecret = trim((string) ($this->settings->quickbooks_client_secret ?? ''));
        $companyId = trim((string) ($this->settings->quickbooks_company_id ?? ''));

        return $clientId !== '' && $clientSecret !== '' && $companyId !== '';
    }

    /**
     * Whether card charges can be processed right now (valid OAuth token).
     */
    public function isReady(): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        return app(QuickBooksService::class)->hasValidConnection();
    }

    /**
     * Human-readable connection state for payment UI.
     *
     * @return array{ready: bool, status: string, message: string}
     */
    public function connectionState(): array
    {
        if (! $this->isEnabled()) {
            return [
                'ready' => false,
                'status' => 'disabled',
                'message' => 'Card payments are not configured yet. Please contact the training office.',
            ];
        }

        $token = trim((string) ($this->settings->quickbooks_access_token ?? ''));
        $refresh = trim((string) ($this->settings->quickbooks_refresh_token ?? ''));
        if ($token === '' || $refresh === '') {
            return [
                'ready' => false,
                'status' => 'not_connected',
                'message' => 'Card payments are temporarily unavailable. An administrator must reconnect QuickBooks in Site Settings.',
            ];
        }

        if (! app(QuickBooksService::class)->hasValidConnection()) {
            return [
                'ready' => false,
                'status' => 'expired',
                'message' => 'Card payments are temporarily unavailable because the QuickBooks connection expired. An administrator must reconnect QuickBooks in Site Settings.',
            ];
        }

        return [
            'ready' => true,
            'status' => 'ready',
            'message' => 'QuickBooks Payments is ready.',
        ];
    }

    /**
     * Get a valid access token (refreshes if needed).
     */
    public function getAccessToken(): string
    {
        $token = $this->settings->quickbooks_access_token ?? '';
        $refreshToken = $this->settings->quickbooks_refresh_token ?? '';
        if (empty($token) || empty($refreshToken)) {
            throw new \Exception('QuickBooks is not connected. Please connect in Site Settings.');
        }
        // Token may be expired - use QuickBooksService to refresh
        $qbService = app(QuickBooksService::class);

        return $qbService->getValidAccessToken();
    }

    /**
     * Get base URL for QuickBooks Payments API.
     */
    protected function getBaseUrl(): string
    {
        $env = $this->settings->quickbooks_environment ?? 'sandbox';

        return $env === 'production'
            ? 'https://api.intuit.com'
            : 'https://sandbox.api.intuit.com';
    }

    /**
     * Create a token from card data (server-side) then create charge.
     *
     * @param  string  $cardNumber  Card number (digits only)
     * @param  string  $expMonth  Expiration month (01-12)
     * @param  string  $expYear  Expiration year (4 digits)
     * @param  string  $cvc  Card CVC
     * @param  float  $amount  Amount in dollars (e.g. 20.00)
     * @return array ['success' => bool, 'message' => string, 'charge_id' => ?string]
     */
    public function createChargeFromCard(string $cardNumber, string $expMonth, string $expYear, string $cvc, float $amount = 20.00): array
    {
        $tokenResult = $this->createToken($cardNumber, $expMonth, $expYear, $cvc);
        if (! $tokenResult['success']) {
            return $tokenResult;
        }

        return $this->createCharge($tokenResult['token'], $amount, 'USD');
    }

    /**
     * Create a payment token from card data via QuickBooks Payments Tokens API.
     */
    public function createToken(string $cardNumber, string $expMonth, string $expYear, string $cvc): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'message' => 'QuickBooks Payments is not configured.', 'token' => null];
        }
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        $expMonth = str_pad((string) (int) $expMonth, 2, '0', STR_PAD_LEFT);
        $expYear = strlen($expYear) === 2 ? '20'.$expYear : $expYear;
        try {
            $accessToken = $this->getAccessToken();
            $baseUrl = $this->getBaseUrl();
            $url = $baseUrl.'/quickbooks/v4/payments/tokens';
            $body = [
                'card' => [
                    'number' => $cardNumber,
                    'expMonth' => $expMonth,
                    'expYear' => $expYear,
                    'cvc' => $cvc,
                    'address' => [
                        'streetAddress' => 'Online Payment',
                        'city' => 'N/A',
                        'region' => 'N/A',
                        'country' => 'US',
                        'postalCode' => '00000',
                    ],
                ],
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $body);
            $data = $response->json();
            if (! $response->successful()) {
                Log::error('QuickBooks Payments token failed', ['status' => $response->status(), 'body' => $data]);
                $msg = $data['errors'][0]['message'] ?? $data['message'] ?? $response->body() ?? 'Token creation failed.';

                return ['success' => false, 'message' => is_string($msg) ? $msg : json_encode($msg), 'token' => null];
            }
            $token = $data['value'] ?? $data['token'] ?? $data['id'] ?? null;
            if (! $token) {
                return ['success' => false, 'message' => 'Invalid token response from QuickBooks.', 'token' => null];
            }

            return ['success' => true, 'token' => $token];
        } catch (\Exception $e) {
            Log::error('QuickBooks Payments token exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $this->friendlyAuthError($e->getMessage()),
                'token' => null,
            ];
        }
    }

    protected function friendlyAuthError(string $raw): string
    {
        if (
            str_contains($raw, 'invalid_grant')
            || str_contains(strtolower($raw), 'token invalid')
            || str_contains(strtolower($raw), 'token expired')
            || str_contains(strtolower($raw), 'reconnect')
        ) {
            return 'Card payments are temporarily unavailable. An administrator must reconnect QuickBooks in Site Settings, or mark your deposit as paid from Bookings.';
        }

        return $raw;
    }

    /**
     * Create a charge using QuickBooks Payments API.
     *
     * @param  string  $paymentToken  Token from tokenization
     * @param  float  $amount  Amount in dollars (e.g. 20.00)
     * @param  string  $currency  Currency code (default USD)
     * @return array ['success' => bool, 'message' => string, 'charge_id' => ?string]
     */
    public function createCharge(string $paymentToken, float $amount, string $currency = 'USD'): array
    {
        if (! $this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'QuickBooks Payments is not configured.',
                'charge_id' => null,
            ];
        }

        try {
            $accessToken = $this->getAccessToken();
            $companyId = $this->settings->quickbooks_company_id;
            $baseUrl = $this->getBaseUrl();
            $url = $baseUrl.'/quickbooks/v4/payments/charges';

            $body = [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => $currency,
                'token' => $paymentToken,
                'context' => [
                    'realmId' => $companyId,
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $body);

            $data = $response->json();

            if (! $response->successful()) {
                Log::error('QuickBooks Payments charge failed', [
                    'status' => $response->status(),
                    'body' => $data,
                ]);
                $message = $data['errors'][0]['message'] ?? $data['message'] ?? $response->body() ?? 'Payment failed.';

                return [
                    'success' => false,
                    'message' => is_string($message) ? $message : json_encode($message),
                    'charge_id' => null,
                ];
            }

            $chargeId = $data['id'] ?? $data['chargeId'] ?? $data['charge_id']
                ?? $data['charges'][0]['id'] ?? $data['charges'][0]['chargeId'] ?? null;

            return [
                'success' => true,
                'message' => 'Payment successful.',
                'charge_id' => $chargeId,
            ];
        } catch (\Exception $e) {
            Log::error('QuickBooks Payments exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'charge_id' => null,
            ];
        }
    }
}
