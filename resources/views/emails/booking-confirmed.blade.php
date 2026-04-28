<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Booking Confirmed</h2>
    <p>Hi {{ $booking->customer->name }},</p>
    <p>We have received your payment and your booking is now confirmed.</p>

    <p><strong>Booking Details</strong></p>
    <ul>
        <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
        <li><strong>Service:</strong> {{ $booking->service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</li>
        <li><strong>Time:</strong> {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') : 'TBD' }}</li>
        <li><strong>Students:</strong> {{ $booking->number_of_students }}</li>
        <li><strong>Paid Amount:</strong> ${{ number_format((float) $payment->amount, 2) }}</li>
        <li><strong>Payment Status:</strong> {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}</li>
    </ul>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
