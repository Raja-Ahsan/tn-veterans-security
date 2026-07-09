<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Class Reminder</h2>
    <p>Hi {{ $booking->student->name }},</p>
    <p>This is a reminder about your upcoming class.</p>

    <h3>Class Details</h3>
    <ul>
        <li><strong>Class:</strong> {{ $service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('l, M d, Y') ?? 'TBD' }}</li>
        <li><strong>Time:</strong> {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') : 'TBD' }}</li>
        <li><strong>Location:</strong> {{ $location ?? 'TBD' }}</li>
        <li><strong>Instructor:</strong> {{ $instructor ?? 'TBD' }}</li>
    </ul>

    @if($travelNotes)
        <h3>Travel Notes</h3>
        <p>{!! nl2br(e($travelNotes)) !!}</p>
    @endif

    @if($lodgingInstructions)
        <h3>Lodging Instructions</h3>
        <p>{!! nl2br(e($lodgingInstructions)) !!}</p>
    @endif

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
