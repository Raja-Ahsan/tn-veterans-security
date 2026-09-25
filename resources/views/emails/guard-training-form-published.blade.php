<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Guard Training Form Ready</title></head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2>Your completion form is ready</h2>
    <p>Hi {{ $student->name }},</p>
    <p>
        Your <strong>Certificate of Successful Completion of Guard Training</strong>
        (Form IN-1144) is now available on your student dashboard.
    </p>
    <p><strong>Form #:</strong> {{ $form->form_number }}</p>
    @if($form->service)
        <p><strong>Class:</strong> {{ $form->service->title }}</p>
    @endif
    <p>
        <a href="{{ route('student.guard-training-forms.show', $form) }}"
           style="display:inline-block;background:#3AA62C;color:#fff;padding:10px 16px;border-radius:6px;text-decoration:none;font-weight:bold;">
            View / Download Form
        </a>
    </p>
    <p style="color:#6b7280;font-size:14px;">You can also open it anytime from <strong>Certificates</strong> in your student menu.</p>
</body>
</html>
