<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Welcome, {{ $student->name }}!</h2>
    <p>Your student account has been created successfully.</p>

    <p>You can now:</p>
    <ul>
        <li>Browse and book training classes</li>
        <li>View upcoming and past enrollments</li>
        <li>Update your profile and registration details</li>
        <li>Access online course modules (when enrolled)</li>
    </ul>

    <p>
        <a href="{{ $dashboardUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;padding:10px 16px;text-decoration:none;border-radius:6px;font-weight:bold;">
            Go to your dashboard
        </a>
    </p>

    <p style="margin-top:24px;font-size:14px;color:#4b5563;">
        Login anytime at <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
        using <strong>{{ $student->email }}</strong>.
    </p>

    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
