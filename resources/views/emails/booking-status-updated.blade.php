<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status Updated</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Booking Status Updated</h2>
    <p>Hi {{ $booking->customer->name }},</p>
    <p>Your booking status has been updated.</p>

    <p><strong>Booking Details</strong></p>
    <ul>
        <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
        <li><strong>Service:</strong> {{ $booking->service->title }}</li>
        <li><strong>Previous Status:</strong> {{ ucfirst($oldStatus) }}</li>
        <li><strong>Current Status:</strong> {{ ucfirst($newStatus) }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</li>
    </ul>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
