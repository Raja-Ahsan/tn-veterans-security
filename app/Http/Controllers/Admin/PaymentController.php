<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\BankIntegrationService;
use App\Services\QuickBooksService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.service', 'student']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->has('payment_type') && $request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by QuickBooks sync status
        if ($request->has('quickbooks_synced') && $request->quickbooks_synced !== '') {
            $query->where('synced_to_quickbooks', $request->quickbooks_synced);
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate totals
        $totalAmount = Payment::where('status', 'completed')->sum('amount');
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $todayAmount = Payment::whereDate('payment_date', today())
            ->where('status', 'completed')
            ->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalAmount', 'pendingAmount', 'todayAmount'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.service', 'student', 'booking.classSchedule']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Sync payment to QuickBooks
     */
    public function syncQuickBooks(Payment $payment, QuickBooksService $quickBooksService)
    {
        try {
            $result = $quickBooksService->syncPayment($payment);

            if ($result['success']) {
                return redirect()->route('admin.payments.show', $payment->id)
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('admin.payments.show', $payment->id)
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Failed to sync to QuickBooks: '.$e->getMessage());
        }
    }

    /**
     * Sync payment to bank
     */
    public function syncBank(Payment $payment, BankIntegrationService $bankService)
    {
        try {
            $result = $bankService->syncPayment($payment);

            if ($result['success']) {
                return redirect()->route('admin.payments.show', $payment->id)
                    ->with('success', $result['message']);
            } else {
                return redirect()->route('admin.payments.show', $payment->id)
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Failed to sync to bank: '.$e->getMessage());
        }
    }

    /**
     * Sync all pending payments to QuickBooks
     */
    public function syncAllQuickBooks(QuickBooksService $quickBooksService)
    {
        try {
            $payments = Payment::where('status', 'completed')
                ->where('synced_to_quickbooks', false)
                ->get();

            $synced = 0;
            $failed = 0;
            $firstError = null;

            foreach ($payments as $payment) {
                $result = $quickBooksService->syncPayment($payment);
                if ($result['success']) {
                    $synced++;
                } else {
                    $failed++;
                    if ($firstError === null && ! empty($result['message'])) {
                        $firstError = $result['message'];
                    }
                }
            }

            $message = "Synced {$synced} payments to QuickBooks. {$failed} failed.";
            if ($firstError !== null) {
                $message .= ' '.$firstError;
            }

            return redirect()->route('admin.payments.index')
                ->with($failed > 0 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Failed to sync payments: '.$e->getMessage());
        }
    }

    /**
     * Sync all pending payments to bank
     */
    public function syncAllBank(BankIntegrationService $bankService)
    {
        try {
            $payments = Payment::where('status', 'completed')
                ->where('synced_to_bank', false)
                ->get();

            $synced = 0;
            $failed = 0;

            foreach ($payments as $payment) {
                $result = $bankService->syncPayment($payment);
                if ($result['success']) {
                    $synced++;
                } else {
                    $failed++;
                }
            }

            return redirect()->route('admin.payments.index')
                ->with('success', "Synced {$synced} payments to bank. {$failed} failed.");
        } catch (\Exception $e) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Failed to sync payments: '.$e->getMessage());
        }
    }

    /**
     * Show payments fetched from QuickBooks (sandbox or production).
     * Use this to verify test payments in sandbox.
     */
    public function quickbooksPayments(QuickBooksService $quickBooksService)
    {
        $result = $quickBooksService->fetchPaymentsFromQuickBooks(50);

        return view('admin.payments.quickbooks-payments', [
            'success' => $result['success'],
            'message' => $result['message'],
            'payments' => $result['payments'],
            'environment' => $result['environment'],
        ]);
    }
}
