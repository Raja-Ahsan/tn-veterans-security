<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    @if($forAdmin)
        <h2>New contact form submission</h2>
        <p>A visitor submitted the website contact form.</p>
    @else
        <h2>We received your message</h2>
        <p>Hi {{ $submission->first_name }},</p>
        <p>Thanks for contacting {{ config('app.name') }}. We will reply within 24 hours.</p>
        <p>Here is a copy of what you sent:</p>
    @endif

    <ul>
        <li><strong>Name:</strong> {{ $submission->first_name }} {{ $submission->last_name }}</li>
        <li><strong>Email:</strong> {{ $submission->email }}</li>
        @if($submission->phone)
            <li><strong>Phone:</strong> {{ $submission->phone }}</li>
        @endif
        <li><strong>Subject:</strong> {{ $submission->subject ?: 'General Inquiry' }}</li>
    </ul>

    <p><strong>Message:</strong></p>
    <p style="white-space:pre-wrap;background:#f9fafb;padding:12px;border-radius:6px;">{!! nl2br(e($contactBody)) !!}</p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
