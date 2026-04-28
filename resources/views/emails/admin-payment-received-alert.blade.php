<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Received Alert</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Payment Received</h2>
    <p>A payment has been received for a booking.</p>
    <ul>
        <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
        <li><strong>Customer:</strong> {{ $booking->customer->name }} ({{ $booking->customer->email }})</li>
        <li><strong>Service:</strong> {{ $booking->service->title }}</li>
        <li><strong>Paid Amount:</strong> ${{ number_format((float) $payment->amount, 2) }}</li>
        <li><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</li>
        <li><strong>Payment Gateway:</strong> {{ $payment->payment_gateway ?? 'N/A' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($payment->status) }}</li>
    </ul>
</body>
</html>
