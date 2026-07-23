<?php

use App\Models\ClassNotification;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schedules = ClassSchedule::query()->with('service')->limit(5)->get();
$adminId = User::query()->value('id');
$studentIds = Student::query()->limit(8)->pluck('id')->all();

if ($schedules->isEmpty()) {
    fwrite(STDERR, "No class schedules found. Create a schedule first.\n");
    exit(1);
}

if ($studentIds === []) {
    fwrite(STDERR, "No students found.\n");
    exit(1);
}

$samples = [
    [
        'notification_type' => 'reschedule',
        'delivery_method' => 'both',
        'message' => "Your class has been rescheduled.\n\nNew date/time will appear in your student portal. Please arrive 15 minutes early with a valid photo ID.",
        'sent_count' => 5,
        'failed_count' => 0,
        'days_ago' => 1,
        'hours' => 10,
    ],
    [
        'notification_type' => 'time_change',
        'delivery_method' => 'email',
        'message' => "Important update: class start time has changed to 9:00 AM.\n\nPlease update your calendar and contact us if you cannot attend.",
        'sent_count' => 4,
        'failed_count' => 1,
        'days_ago' => 2,
        'hours' => 14,
    ],
    [
        'notification_type' => 'location_change',
        'delivery_method' => 'sms',
        'message' => 'Class location moved. Check your email/portal for the updated address. Reply STOP to opt out of SMS.',
        'sent_count' => 3,
        'failed_count' => 0,
        'days_ago' => 3,
        'hours' => 9,
    ],
    [
        'notification_type' => 'instructor_change',
        'delivery_method' => 'email',
        'message' => "Your instructor has been updated for this scheduled class.\n\nTraining content and certification requirements remain the same.",
        'sent_count' => 6,
        'failed_count' => 0,
        'days_ago' => 5,
        'hours' => 16,
    ],
    [
        'notification_type' => 'cancellation',
        'delivery_method' => 'both',
        'message' => "Unfortunately this class session has been cancelled due to instructor availability.\n\nOur team will contact you with make-up options or a refund path.",
        'sent_count' => 2,
        'failed_count' => 1,
        'days_ago' => 7,
        'hours' => 11,
    ],
    [
        'notification_type' => 'general',
        'delivery_method' => 'email',
        'message' => "Reminder: please bring your security registration paperwork and a government-issued ID to class.\n\nParking is available behind the training building.",
        'sent_count' => 7,
        'failed_count' => 0,
        'days_ago' => 0,
        'hours' => 8,
    ],
];

$created = 0;

foreach ($samples as $index => $sample) {
    $schedule = $schedules[$index % $schedules->count()];
    $ids = array_slice($studentIds, 0, min(count($studentIds), max(2, $sample['sent_count'])));

    $log = ClassNotification::query()->create([
        'class_schedule_id' => $schedule->id,
        'sent_by' => $adminId,
        'notification_type' => $sample['notification_type'],
        'delivery_method' => $sample['delivery_method'],
        'message' => $sample['message'],
        'student_ids' => $ids,
        'sent_count' => $sample['sent_count'],
        'failed_count' => $sample['failed_count'],
    ]);

    $createdAt = Carbon::now()
        ->subDays($sample['days_ago'])
        ->setTime($sample['hours'], 15 + ($index * 3), 0);

    $log->forceFill([
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ])->save();

    $created++;
    echo "Created #{$log->id} {$sample['notification_type']} for schedule {$schedule->id}\n";
}

echo "Done. Inserted {$created} communication logs. Total now: ".ClassNotification::query()->count()."\n";
