<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate — {{ $certificate->service->title }}</title>
    <style>
        body { font-family: Georgia, serif; margin: 0; padding: 40px; background: #f3f4f6; }
        .cert { max-width: 900px; margin: 0 auto; background: #fff; border: 8px double #1a5632; padding: 60px; text-align: center; }
        h1 { color: #1a5632; font-size: 2.5rem; margin-bottom: 0.5rem; }
        h2 { font-size: 1.5rem; font-weight: normal; margin: 1rem 0 2rem; }
        .name { font-size: 2rem; border-bottom: 2px solid #1a5632; display: inline-block; padding: 0 2rem 0.5rem; margin: 1rem 0; }
        .meta { margin-top: 2rem; color: #555; font-size: 0.95rem; }
        @media print { body { background: #fff; padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <p class="no-print" style="text-align:center;margin-bottom:20px;"><button onclick="window.print()">Print / Save as PDF</button></p>
    <div class="cert" role="document" aria-label="Course completion certificate">
        <h1>TN Veterans Security</h1>
        <h2>Certificate of Completion</h2>
        <p>This certifies that</p>
        <div class="name">{{ $certificate->student->name }}</div>
        <p>has successfully completed</p>
        <p><strong>{{ $certificate->service->title }}</strong></p>
        <div class="meta">
            <p>Certificate Number: {{ $certificate->certificate_number }}</p>
            <p>Date Issued: {{ $certificate->issued_at->format('F j, Y') }}</p>
        </div>
    </div>
</body>
</html>
