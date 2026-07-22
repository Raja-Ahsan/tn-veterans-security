<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Payment as QuickBooksPayment;

class QuickBooksService
{
    protected $dataService;

    protected $settings;

    public function __construct()
    {
        $this->settings = SiteSetting::first();
    }

    /**
     * Initialize QuickBooks DataService
     */
    protected function initializeDataService()
    {
        if (! $this->settings || ! $this->settings->quickbooks_enabled) {
            throw new \Exception('QuickBooks integration is not enabled.');
        }

        if (empty($this->settings->quickbooks_client_id) || empty($this->settings->quickbooks_client_secret)) {
            throw new \Exception('QuickBooks API credentials are not configured.');
        }

        $environment = $this->settings->quickbooks_environment ?? 'sandbox';

        // Get redirect URI from settings or use default
        $redirectUri = config('app.url').'/admin/quickbooks/callback';

        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => $this->settings->quickbooks_client_id,
            'ClientSecret' => $this->settings->quickbooks_client_secret,
            'RedirectURI' => $redirectUri,
            'scope' => 'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment',
            'baseUrl' => $environment === 'production' ? 'production' : 'development',
        ]);

        // Set access token if available (SDK expects OAuth2AccessToken object, not a string)
        if (! empty($this->settings->quickbooks_access_token) && ! empty($this->settings->quickbooks_refresh_token) && ! empty($this->settings->quickbooks_company_id)) {
            // Refresh token first (access tokens expire in 1 hour); use standalone helper
            $token = $this->refreshAndPersistToken();
            $token->setRealmID($this->settings->quickbooks_company_id);
            $dataService->updateOAuth2Token($token);
        }

        $this->dataService = $dataService;

        return $dataService;
    }

    /**
     * Refresh OAuth2 access token and save new tokens to settings. Returns the new token.
     */
    protected function refreshAndPersistToken(): OAuth2AccessToken
    {
        $oauth2Helper = new OAuth2LoginHelper(
            $this->settings->quickbooks_client_id,
            $this->settings->quickbooks_client_secret
        );
        try {
            $newToken = $oauth2Helper->refreshAccessTokenWithRefreshToken($this->settings->quickbooks_refresh_token);
        } catch (\Exception $e) {
            Log::warning('QuickBooks token refresh failed', ['error' => $e->getMessage()]);
            $this->clearStoredTokens();
            throw new \Exception(
                'QuickBooks connection expired. Admin must reconnect in Site Settings → QuickBooks (Connect with QuickBooks).'
            );
        }
        // Persist new tokens (QuickBooks may return a new refresh token)
        $this->settings->update([
            'quickbooks_access_token' => $newToken->getAccessToken(),
            'quickbooks_refresh_token' => $newToken->getRefreshToken(),
        ]);
        $this->settings->refresh();

        return $newToken;
    }

    /**
     * Clear expired/invalid OAuth tokens so Connect must be run again.
     */
    public function clearStoredTokens(): void
    {
        if (! $this->settings) {
            return;
        }

        $this->settings->update([
            'quickbooks_access_token' => null,
            'quickbooks_refresh_token' => null,
        ]);
        $this->settings->refresh();
    }

    /**
     * Whether QuickBooks is connected with a refreshable access token.
     */
    public function hasValidConnection(): bool
    {
        try {
            if (! $this->settings || ! $this->settings->quickbooks_enabled) {
                return false;
            }
            if (
                empty($this->settings->quickbooks_access_token)
                || empty($this->settings->quickbooks_refresh_token)
                || empty($this->settings->quickbooks_company_id)
            ) {
                return false;
            }
            $this->getValidAccessToken();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Sync payment to QuickBooks
     */
    public function syncPayment(Payment $payment): array
    {
        try {
            if (! $this->settings || ! $this->settings->quickbooks_enabled) {
                return [
                    'success' => false,
                    'message' => 'QuickBooks integration is not enabled.',
                ];
            }

            // Check if already synced
            if ($payment->synced_to_quickbooks) {
                return [
                    'success' => true,
                    'message' => 'Payment already synced to QuickBooks.',
                    'invoice_id' => $payment->quickbooks_invoice_id,
                    'payment_id' => $payment->quickbooks_payment_id,
                ];
            }

            $this->initializeDataService();

            // Get booking details
            $booking = $payment->booking;
            if (! $booking) {
                throw new \Exception('Booking not found for payment.');
            }

            $service = $booking->service;
            $student = $payment->student;

            // Create or get customer in QuickBooks
            $qbCustomer = $this->getOrCreateCustomer($student);

            // Create invoice
            $invoice = $this->createInvoice($payment, $booking, $service, $qbCustomer);

            // Create payment record
            $qbPayment = $this->createPaymentRecord($payment, $invoice);

            // Update payment record
            $payment->markSyncedToQuickBooks(
                $invoice->Id ?? null,
                $qbPayment->Id ?? null
            );

            Log::info('Payment synced to QuickBooks', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->Id ?? null,
                'qb_payment_id' => $qbPayment->Id ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Payment synced to QuickBooks successfully.',
                'invoice_id' => $invoice->Id ?? null,
                'payment_id' => $qbPayment->Id ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('QuickBooks sync error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to sync payment to QuickBooks: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get or create customer in QuickBooks
     */
    protected function getOrCreateCustomer($student)
    {
        // Search for existing student by email
        $queryString = "SELECT * FROM Customer WHERE PrimaryEmailAddr = '{$student->email}'";
        $students = $this->dataService->Query($queryString);

        if (! empty($students) && count($students) > 0) {
            return $students[0];
        }

        // Create new customer
        $customerObj = \QuickBooksOnline\API\Facades\Customer::create([
            'DisplayName' => $student->name ?? $student->email,
            'PrimaryEmailAddr' => [
                'Address' => $student->email,
            ],
            'GivenName' => $student->name ?? 'Customer',
            'FamilyName' => '',
        ]);

        $resultingCustomerObj = $this->dataService->Add($customerObj);

        return $resultingCustomerObj;
    }

    /**
     * Create invoice in QuickBooks
     */
    protected function createInvoice(Payment $payment, ServiceBooking $booking, $service, $qbCustomer)
    {
        $invoice = Invoice::create([
            'Line' => [
                [
                    'Amount' => $payment->amount,
                    'DetailType' => 'SalesItemLineDetail',
                    'SalesItemLineDetail' => [
                        'ItemRef' => [
                            'value' => $this->getOrCreateServiceItem($service)->Id,
                            'name' => $service->title,
                        ],
                        'Qty' => $booking->number_of_students ?? 1,
                    ],
                ],
            ],
            'CustomerRef' => [
                'value' => $qbCustomer->Id,
            ],
            'TxnDate' => $payment->payment_date->format('Y-m-d'),
            'DueDate' => $payment->payment_date->format('Y-m-d'),
            'TotalAmt' => $payment->amount,
            'PrivateNote' => "Booking #{$booking->id} - {$service->title}",
        ]);

        $resultingInvoice = $this->dataService->Add($invoice);

        return $resultingInvoice;
    }

    /**
     * Get a valid income account Id for the connected company (sandbox or production).
     * Required for creating Service items; avoids hardcoded "1" which may not exist.
     */
    protected function getDefaultIncomeAccountId(): string
    {
        try {
            $queryString = "SELECT * FROM Account WHERE AccountType = 'Income' OR AccountType = 'Other Income'";
            $accounts = $this->dataService->Query($queryString, null, 1);
            $accountsArray = is_array($accounts) ? $accounts : ($accounts ? [$accounts] : []);
            if (! empty($accountsArray) && isset($accountsArray[0]->Id)) {
                return (string) $accountsArray[0]->Id;
            }
        } catch (\Exception $e) {
            Log::warning('QuickBooks could not query income account', ['error' => $e->getMessage()]);
        }

        return '1';
    }

    /**
     * Get or create service item in QuickBooks
     */
    protected function getOrCreateServiceItem($service)
    {
        // Search for existing item
        $queryString = "SELECT * FROM Item WHERE Name = '{$service->title}'";
        $items = $this->dataService->Query($queryString);

        if (! empty($items) && count($items) > 0) {
            return $items[0];
        }

        $incomeAccountId = $this->getDefaultIncomeAccountId();

        // Create new item
        $item = \QuickBooksOnline\API\Facades\Item::create([
            'Name' => $service->title,
            'Type' => 'Service',
            'IncomeAccountRef' => [
                'value' => $incomeAccountId,
            ],
        ]);

        $resultingItem = $this->dataService->Add($item);

        return $resultingItem;
    }

    /**
     * Create payment record in QuickBooks
     */
    protected function createPaymentRecord(Payment $payment, $invoice)
    {
        $customerRefValue = is_object($invoice->CustomerRef) && isset($invoice->CustomerRef->value)
            ? $invoice->CustomerRef->value
            : (string) $invoice->CustomerRef;

        $qbPayment = QuickBooksPayment::create([
            'CustomerRef' => [
                'value' => $customerRefValue,
            ],
            'TotalAmt' => $payment->amount,
            'TxnDate' => $payment->payment_date->format('Y-m-d'),
            'Line' => [
                [
                    'Amount' => $payment->amount,
                    'LinkedTxn' => [
                        [
                            'TxnId' => $invoice->Id,
                            'TxnType' => 'Invoice',
                        ],
                    ],
                ],
            ],
        ]);

        $resultingPayment = $this->dataService->Add($qbPayment);

        return $resultingPayment;
    }

    /**
     * Refresh token and return the access token string (for Payments API).
     */
    public function getValidAccessToken(): string
    {
        $this->initializeDataService();
        $this->settings->refresh();

        return $this->settings->quickbooks_access_token ?? '';
    }

    /**
     * Test QuickBooks connection
     */
    public function testConnection(): array
    {
        try {
            $this->initializeDataService();
            $companyInfo = $this->dataService->getCompanyInfo();

            return [
                'success' => true,
                'message' => 'Connection successful.',
                'company_name' => $companyInfo->CompanyName ?? 'N/A',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Fetch Payment entities from QuickBooks (sandbox or production).
     * Use this to verify test payments in sandbox or view synced payments in QB.
     *
     * @param  int  $maxResults  Maximum number of payments to return (default 50)
     * @return array ['success' => bool, 'message' => string, 'payments' => array, 'environment' => string]
     */
    public function fetchPaymentsFromQuickBooks(int $maxResults = 50): array
    {
        try {
            $this->initializeDataService();
            $environment = $this->settings->quickbooks_environment ?? 'sandbox';

            // Query Payment entities; SDK uses QBO query syntax
            $queryString = 'SELECT * FROM Payment ORDER BY TxnDate DESC';
            $payments = $this->dataService->Query($queryString, null, max(1, min(100, $maxResults)));

            $list = [];
            $paymentsArray = is_array($payments) ? $payments : ($payments ? [$payments] : []);
            foreach ($paymentsArray as $qbPayment) {
                $list[] = [
                    'id' => $qbPayment->Id ?? null,
                    'total_amt' => $qbPayment->TotalAmt ?? 0,
                    'txn_date' => $qbPayment->TxnDate ?? null,
                    'customer_ref' => isset($qbPayment->CustomerRef->value) ? $qbPayment->CustomerRef->value : ($qbPayment->CustomerRef ?? null),
                    'customer_name' => isset($qbPayment->CustomerRef->name) ? $qbPayment->CustomerRef->name : null,
                    'payment_method' => isset($qbPayment->PaymentMethodRef->name) ? $qbPayment->PaymentMethodRef->name : null,
                    'unapplied_amt' => $qbPayment->UnappliedAmt ?? 0,
                ];
            }

            return [
                'success' => true,
                'message' => count($list).' payment(s) found in QuickBooks.',
                'payments' => $list,
                'environment' => $environment,
            ];
        } catch (\Exception $e) {
            Log::error('QuickBooks fetch payments error', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to fetch payments from QuickBooks: '.$e->getMessage(),
                'payments' => [],
                'environment' => $this->settings->quickbooks_environment ?? 'sandbox',
            ];
        }
    }
}
