<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Booking Received</h2>
    <p>Hi {{ $booking->customer->name }},</p>
    <p>Your booking has been created. Please complete your deposit payment to confirm your seat.</p>

    <p><strong>Booking Details</strong></p>
    <ul>
        <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
        <li><strong>Service:</strong> {{ $booking->service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</li>
        <li><strong>Time:</strong> {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') : 'TBD' }}</li>
        <li><strong>Students:</strong> {{ $booking->number_of_students }}</li>
        <li><strong>Deposit Due:</strong> ${{ number_format((float) $booking->deposit_amount, 2) }}</li>
    </ul>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
