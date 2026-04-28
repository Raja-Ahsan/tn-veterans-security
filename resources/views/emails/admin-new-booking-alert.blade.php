<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Alert</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">New Booking Created</h2>
    <p>A new booking has been created and is awaiting payment confirmation.</p>
    <ul>
        <li><strong>Booking ID:</strong> #{{ $booking->id }}</li>
        <li><strong>Customer:</strong> {{ $booking->customer->name }} ({{ $booking->customer->email }})</li>
        <li><strong>Service:</strong> {{ $booking->service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</li>
        <li><strong>Students:</strong> {{ $booking->number_of_students }}</li>
        <li><strong>Deposit Due:</strong> ${{ number_format((float) $booking->deposit_amount, 2) }}</li>
    </ul>
</body>
</html>
