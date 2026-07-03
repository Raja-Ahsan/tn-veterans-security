<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'student_id',
        'amount',
        'payment_type',
        'payment_method',
        'status',
        'transaction_id',
        'payment_gateway',
        'gateway_response',
        'synced_to_quickbooks',
        'quickbooks_invoice_id',
        'quickbooks_payment_id',
        'quickbooks_synced_at',
        'synced_to_bank',
        'bank_synced_at',
        'notes',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'synced_to_quickbooks' => 'boolean',
        'synced_to_bank' => 'boolean',
        'quickbooks_synced_at' => 'datetime',
        'bank_synced_at' => 'datetime',
        'payment_date' => 'date',
        'gateway_response' => 'array',
    ];

    /**
     * Get the booking that owns this payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'booking_id');
    }

    /**
     * Get the student that made this payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Mark payment as synced to QuickBooks.
     */
    public function markSyncedToQuickBooks($invoiceId = null, $paymentId = null): void
    {
        $this->update([
            'synced_to_quickbooks' => true,
            'quickbooks_invoice_id' => $invoiceId,
            'quickbooks_payment_id' => $paymentId,
            'quickbooks_synced_at' => now(),
        ]);
    }

    /**
     * Mark payment as synced to bank.
     */
    public function markSyncedToBank(): void
    {
        $this->update([
            'synced_to_bank' => true,
            'bank_synced_at' => now(),
        ]);
    }
}
