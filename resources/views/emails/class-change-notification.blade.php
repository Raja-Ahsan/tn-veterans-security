<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Class Update: {{ $notificationType }}</h2>
    <p>Hi {{ $student->name }},</p>

    <p><strong>Class:</strong> {{ $schedule->service->title }}</p>
    <p><strong>Date:</strong> {{ $schedule->class_date->format('l, M d, Y') }}</p>
    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}</p>
    @if($schedule->location_name)
        <p><strong>Location:</strong> {{ $schedule->location_name }}</p>
    @endif

    <p>{!! nl2br(e($message)) !!}</p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
