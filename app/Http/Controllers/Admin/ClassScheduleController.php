<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Instructor;
use App\Models\Location;
use App\Models\Service;
use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Group schedules by service_id
        $schedules = ClassSchedule::with('service')
            ->orderBy('class_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('service_id');

        $expandServiceKey = null;
        if ($request->filled('expand_service')) {
            $wanted = (int) $request->query('expand_service');
            if ($wanted > 0) {
                $expandServiceKey = $schedules->keys()->first(fn ($key) => (int) $key === $wanted);
            }
        }

        return view('admin.class-schedules.index', compact('schedules', 'expandServiceKey'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $services = Service::where('is_active', true)
            ->orderBy('title')
            ->get();

        // Pre-select service if service_id is passed in query string
        $selectedServiceId = $request->query('service_id');
        $instructors = Instructor::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('order')->orderBy('name')->get();

        return view('admin.class-schedules.create', compact('services', 'selectedServiceId', 'instructors', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $scheduleType = $request->input('schedule_type', 'single');

        // Resolve locations from database IDs
        $locationEntries = $this->resolveLocationEntries($request);
        $baseData = $this->buildBaseScheduleData($request);

        // Common validation rules
        $commonRules = [
            'service_id' => 'required|exists:services,id',
            'duration_hours' => 'required|integer|min:1',
            'max_students' => 'required|integer|min:1|max:10',
            'min_students' => 'required|integer|min:1|max:4',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
            'location' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:instructors,id',
            'can_overlap' => 'boolean',
            'admin_override_capacity' => 'boolean',
            'travel_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Check if multiple schedules are being submitted
        if ($scheduleType === 'multiple' && $request->has('schedules') && is_array($request->input('schedules'))) {
            $schedulesArray = $request->input('schedules');

            // Filter out empty schedules (where date or time is missing)
            $schedulesArray = array_filter($schedulesArray, function ($schedule) {
                return ! empty($schedule['class_date']) && ! empty($schedule['start_time']);
            });

            // Multiple schedules validation
            if (count($schedulesArray) > 0) {
                $request->validate([
                    ...$commonRules,
                    'schedules' => 'required|array|min:1',
                    'schedules.*.class_date' => 'required|date|after_or_equal:today',
                    'schedules.*.start_time' => 'required',
                ]);

                $schedules = [];
                $durationHours = (int) $request->input('duration_hours');

                // Create schedules for each date/time combination and each location
                foreach ($schedulesArray as $scheduleData) {
                    // Skip empty schedules
                    if (empty($scheduleData['class_date']) || empty($scheduleData['start_time'])) {
                        continue;
                    }

                    $startTime = Carbon::createFromFormat('Y-m-d H:i', $scheduleData['class_date'].' '.$scheduleData['start_time']);
                    $endTime = $startTime->copy()->addHours($durationHours);

                    // Create a schedule for each selected location
                    foreach ($locationEntries as $locationEntry) {
                        $schedules[] = array_merge($baseData, [
                            'class_date' => $scheduleData['class_date'],
                            'start_time' => Carbon::parse($scheduleData['start_time'])->format('H:i:s'),
                            'end_time' => $endTime->format('H:i:s'),
                            'location_id' => $locationEntry['location_id'],
                            'location' => $locationEntry['location'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Only insert if we have valid schedules
                if (count($schedules) > 0) {
                    $this->assertSchedulesAreUnique(
                        (int) $request->input('service_id'),
                        collect($schedules)->map(fn (array $schedule) => [
                            'class_date' => $schedule['class_date'],
                            'start_time' => $schedule['start_time'],
                            'location' => $schedule['location'],
                        ])->all()
                    );

                    ClassSchedule::insert($schedules);

                    return redirect()->route('admin.class-schedules.index')
                        ->with('success', count($schedules).' class schedule(s) created successfully.');
                }
            }
        }

        // Single schedule validation (only process if not multiple or multiple failed)
        $validated = $request->validate([
            ...$commonRules,
            'class_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
        ]);

        // Calculate end_time from start_time + duration
        $durationHours = (int) $validated['duration_hours'];
        $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['class_date'].' '.$validated['start_time']);
        $endTime = $startTime->copy()->addHours($durationHours);

        $schedules = [];

        // Create a schedule for each selected location
        foreach ($locationEntries as $locationEntry) {
            $schedules[] = array_merge($baseData, [
                'class_date' => $validated['class_date'],
                'start_time' => Carbon::parse($validated['start_time'])->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'location_id' => $locationEntry['location_id'],
                'location' => $locationEntry['location'],
            ]);
        }

        // Insert all schedules
        if (count($schedules) > 0) {
            $this->assertSchedulesAreUnique(
                (int) $validated['service_id'],
                collect($schedules)->map(fn (array $schedule) => [
                    'class_date' => $schedule['class_date'],
                    'start_time' => $schedule['start_time'],
                    'location' => $schedule['location'],
                ])->all()
            );

            ClassSchedule::insert($schedules);

            $message = count($schedules) > 1
                ? count($schedules).' class schedule(s) created successfully.'
                : 'Class schedule created successfully.';

            return redirect()->route('admin.class-schedules.index')
                ->with('success', $message);
        }

        return redirect()->back()
            ->with('error', 'Please select at least one location.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $classSchedule)
    {
        $classSchedule->load([
            'service',
            'bookings.student',
            'locationRecord',
            'instructorRecord',
            'waitlistEntries.student',
        ]);

        $enrolledBookings = $classSchedule->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('student')
            ->get();

        return view('admin.class-schedules.show', compact('classSchedule', 'enrolledBookings'));
    }

    public function exportRoster(ClassSchedule $classSchedule): StreamedResponse
    {
        $classSchedule->load(['service']);

        $bookings = ServiceBooking::query()
            ->where('class_schedule_id', $classSchedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with(['student', 'payments'])
            ->orderBy('created_at')
            ->get();

        $filename = 'roster-'.$classSchedule->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Students', 'Status', 'Payment Status', 'Deposit Paid', 'Registration #', 'Reg. Expiration']);

            foreach ($bookings as $booking) {
                $student = $booking->student;
                $depositPaid = $booking->payments->where('payment_type', 'deposit')->where('status', 'completed')->sum('amount');

                fputcsv($handle, [
                    $student?->name,
                    $student?->email,
                    $student?->phone,
                    $booking->number_of_students,
                    $booking->status,
                    $booking->payment_status,
                    number_format((float) $depositPaid, 2),
                    $student?->security_registration_number,
                    $student?->security_registration_expiration?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSchedule $classSchedule)
    {
        $services = Service::where('is_active', true)
            ->orderBy('title')
            ->get();
        $instructors = Instructor::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('order')->orderBy('name')->get();

        return view('admin.class-schedules.edit', compact('classSchedule', 'services', 'instructors', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSchedule $classSchedule)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'duration_hours' => 'required|integer|min:1',
            'max_students' => 'required|integer|min:1|max:10',
            'min_students' => 'required|integer|min:1|max:4',
            'location' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'room' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:instructors,id',
            'can_overlap' => 'boolean',
            'admin_override_capacity' => 'boolean',
            'status' => 'required|in:scheduled,full,cancelled,completed',
            'notes' => 'nullable|string',
            'travel_notes' => 'nullable|string',
        ]);

        // Calculate end_time from start_time + duration
        $durationHours = (int) $validated['duration_hours']; // Ensure it's an integer
        $startTime = Carbon::createFromFormat('Y-m-d H:i', $validated['class_date'].' '.$validated['start_time']);
        $endTime = $startTime->copy()->addHours($durationHours);

        $validated['end_time'] = $endTime->format('H:i:s');
        // Store start_time as time string
        $validated['start_time'] = Carbon::parse($validated['start_time'])->format('H:i:s');
        $validated['duration_hours'] = $durationHours; // Store as integer
        $validated['can_overlap'] = $request->has('can_overlap');
        $validated['admin_override_capacity'] = $request->has('admin_override_capacity');

        if (! empty($validated['location_id'])) {
            $location = Location::find($validated['location_id']);
            $validated['location'] = $location?->display_name ?? $validated['location'] ?? null;
        }

        if (! empty($validated['instructor_id'])) {
            $instructor = Instructor::find($validated['instructor_id']);
            $validated['instructor'] = $instructor?->name ?? $validated['instructor'] ?? null;
        }

        // Don't allow updating current_students if it would exceed max_students
        if ($classSchedule->current_students > $validated['max_students']) {
            return redirect()->back()
                ->with('error', 'Cannot set max students below current enrolled students ('.$classSchedule->current_students.').');
        }

        if (ClassSchedule::duplicateExists(
            (int) $validated['service_id'],
            $validated['class_date'],
            $validated['start_time'],
            $validated['location'] ?? null,
            $classSchedule->id
        )) {
            throw ValidationException::withMessages([
                'class_date' => ['A class with the same date, time, and location already exists for this training program.'],
            ]);
        }

        // Update status to 'full' if current_students >= max_students
        if ($classSchedule->current_students >= $validated['max_students']) {
            $validated['status'] = 'full';
        }

        $classSchedule->update($validated);

        return redirect()->route('admin.class-schedules.index')
            ->with('success', 'Class schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $classSchedule)
    {
        // Check if there are any bookings
        if ($classSchedule->bookings()->count() > 0) {
            return redirect()->route('admin.class-schedules.index')
                ->with('error', 'Cannot delete class schedule with existing bookings. Please cancel bookings first.');
        }

        $classSchedule->delete();

        return redirect()->route('admin.class-schedules.index')
            ->with('success', 'Class schedule deleted successfully.');
    }

    /**
     * @param  array<int, array{class_date: string, start_time: string, location: ?string}>  $schedules
     */
    protected function assertSchedulesAreUnique(int $serviceId, array $schedules): void
    {
        $seen = [];

        foreach ($schedules as $schedule) {
            $fingerprint = ClassSchedule::slotFingerprint(
                $serviceId,
                $schedule['class_date'],
                $schedule['start_time'],
                $schedule['location'] ?? null
            );

            if (isset($seen[$fingerprint])) {
                throw ValidationException::withMessages([
                    'class_date' => ['You cannot create duplicate classes on the same date, time, and location.'],
                ]);
            }

            $seen[$fingerprint] = true;

            if (ClassSchedule::duplicateExists(
                $serviceId,
                $schedule['class_date'],
                $schedule['start_time'],
                $schedule['location'] ?? null
            )) {
                throw ValidationException::withMessages([
                    'class_date' => ['A class with the same date, time, and location already exists for this training program.'],
                ]);
            }
        }
    }

    /**
     * @return array<int, array{location_id: ?int, location: ?string}>
     */
    protected function resolveLocationEntries(Request $request): array
    {
        $locationIds = array_values(array_unique(array_filter((array) $request->input('location_ids', []))));

        if ($locationIds === []) {
            $custom = $request->input('location');

            return [['location_id' => null, 'location' => $custom ?: null]];
        }

        return Location::query()
            ->whereIn('id', $locationIds)
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(fn (Location $location) => [
                'location_id' => $location->id,
                'location' => $location->display_name,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildBaseScheduleData(Request $request): array
    {
        $instructorId = $request->input('instructor_id');
        $instructorName = $request->input('instructor');

        if ($instructorId) {
            $instructorName = Instructor::find($instructorId)?->name ?? $instructorName;
        }

        return [
            'service_id' => (int) $request->input('service_id'),
            'duration_hours' => (int) $request->input('duration_hours'),
            'max_students' => (int) $request->input('max_students'),
            'min_students' => (int) $request->input('min_students'),
            'room' => $request->input('room'),
            'instructor' => $instructorName,
            'instructor_id' => $instructorId ?: null,
            'can_overlap' => $request->has('can_overlap'),
            'admin_override_capacity' => $request->has('admin_override_capacity'),
            'travel_notes' => $request->input('travel_notes'),
            'notes' => $request->input('notes'),
            'current_students' => 0,
            'status' => 'scheduled',
        ];
    }
}
