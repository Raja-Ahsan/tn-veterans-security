<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset your password</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Reset your password</h2>
    <p>Hi {{ $student->name }},</p>
    <p>We received a request to reset the password for your student account ({{ $student->email }}).</p>

    <p>
        <a href="{{ $resetUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;padding:10px 16px;text-decoration:none;border-radius:6px;font-weight:bold;">
            Choose a new password
        </a>
    </p>

    <p style="margin-top:24px;font-size:14px;color:#4b5563;">
        This link expires in 60 minutes. If you did not request a reset, you can ignore this email.
    </p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
