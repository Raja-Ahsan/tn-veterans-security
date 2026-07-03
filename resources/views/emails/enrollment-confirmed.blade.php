<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Enrollment Confirmed</h2>
    <p>Hi {{ $booking->student->name }},</p>
    <p>Your deposit payment was received. You are officially enrolled!</p>

    <h3>Class Details</h3>
    <ul>
        <li><strong>Class:</strong> {{ $service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('l, M d, Y') ?? 'TBD' }}</li>
        <li><strong>Time:</strong> {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') : 'TBD' }}</li>
        <li><strong>Location:</strong> {{ $location ?? 'TBD' }}</li>
        <li><strong>Instructor:</strong> {{ $instructor ?? 'TBD' }}</li>
        <li><strong>Deposit Paid:</strong> ${{ number_format((float) $payment->amount, 2) }}</li>
        <li><strong>Remaining Balance:</strong> ${{ number_format((float) $booking->remaining_amount, 2) }}</li>
    </ul>

    @if($service->what_to_bring)
        <h3>What to Bring</h3>
        <p>{!! nl2br(e($service->what_to_bring)) !!}</p>
    @endif

    @if($service->prerequisites)
        <h3>Prerequisites</h3>
        <p>{!! nl2br(e($service->prerequisites)) !!}</p>
    @endif

    @if($service->is_travel_based)
        @if($service->travel_notes)
            <h3>Travel Notes</h3>
            <p>{!! nl2br(e($service->travel_notes)) !!}</p>
        @endif
        @if($service->lodging_instructions)
            <h3>Lodging Instructions</h3>
            <p>{!! nl2br(e($service->lodging_instructions)) !!}</p>
        @endif
    @endif

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
