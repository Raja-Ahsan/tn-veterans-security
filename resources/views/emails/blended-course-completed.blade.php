<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Online Course Completed</title></head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
@if($forStudent)
    <h2>Congratulations!</h2>
    <p>Hi {{ $student->name }},</p>
    <p>You have completed the online portion of <strong>{{ $service->title }}</strong>.</p>
    <p><strong>Completion:</strong> {{ $completedAt->format('l, M d, Y g:i A') }}</p>
    <p>You are now eligible for in-person hands-on testing. We will contact you with scheduling details.</p>
@else
    <h2>Student Online Completion</h2>
    <p><strong>Student:</strong> {{ $student->name }}</p>
    <p><strong>Course:</strong> {{ $service->title }}</p>
    <p><strong>Completed:</strong> {{ $completedAt->format('l, M d, Y g:i A') }}</p>
    <p>This student is eligible for in-person testing.</p>
@endif
</body>
</html>
