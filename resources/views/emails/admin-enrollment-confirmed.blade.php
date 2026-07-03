<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Enrollment</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>New Student Enrollment</h2>

    <h3>Student</h3>
    <ul>
        <li><strong>Name:</strong> {{ $booking->student->name }}</li>
        <li><strong>Email:</strong> {{ $booking->student->email }}</li>
        <li><strong>Phone:</strong> {{ $booking->student->phone ?? 'N/A' }}</li>
    </ul>

    <h3>Enrollment</h3>
    <ul>
        <li><strong>Class:</strong> {{ $booking->service->title }}</li>
        <li><strong>Date:</strong> {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</li>
        <li><strong>Students:</strong> {{ $booking->number_of_students }}</li>
        <li><strong>Deposit Amount:</strong> ${{ number_format((float) $payment->amount, 2) }}</li>
        <li><strong>Payment Confirmation ID:</strong> {{ $payment->transaction_id ?? '#'.$payment->id }}</li>
    </ul>
</body>
</html>
