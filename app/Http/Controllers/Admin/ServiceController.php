<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Service::query()
            ->withCount(['classSchedules', 'bookings'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        $search = $request->string('q')->trim()->value();
        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        $services = $query->get();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'html' => view('admin.services.partials.table-rows', [
                    'services' => $services,
                    'search' => $search,
                ])->render(),
            ]);
        }

        return view('admin.services.index', compact('services', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allServices = Service::where('is_active', true)->orderBy('order')->orderBy('title')->get();

        return view('admin.services.create', compact('allServices'));
    }

    /**
     * Store a newly created service. Images are saved under public/assets/images/classes.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:services,slug|regex:/^[a-z0-9\-]+$/',
            'short_description' => 'nullable|string|max:500',
            'requirements' => 'nullable|string',
            'sub_titles' => 'nullable|array',
            'sub_titles.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
            'min_students' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            // Category fields (multiple)
            'categories' => 'nullable|array',
            'categories.*' => 'string|in:'.implode(',', array_keys(config('service_categories', []))),
            'subcategory' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'requires_dallas_law' => 'boolean',
            'requires_active_shooter' => 'boolean',
            // Pricing fields
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            // Class configuration
            'class_type' => 'nullable|in:group,one-on-one',
            'has_online_parts' => 'boolean',
            'testing_in_person' => 'boolean',
            'linked_services' => 'nullable|array',
            'linked_services.*' => 'integer|exists:services,id',
            'schedules' => 'nullable|array',
            'schedules.*.id' => 'nullable|integer',
            'schedules.*.class_date' => 'nullable|date',
            'schedules.*.start_time' => 'nullable',
            'schedules.*.duration_hours' => 'nullable|integer|min:1|max:48',
            'schedules.*.max_students' => 'nullable|integer|min:1|max:100',
            'schedules.*.min_students' => 'nullable|integer|min:1|max:100',
            'schedules.*.location' => 'nullable|string|max:255',
            'schedules.*.room' => 'nullable|string|max:255',
            'schedules.*.instructor' => 'nullable|string|max:255',
            'schedules.*.notes' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeUploadedClassImage($request->file('image'));
        }

        $validated['slug'] = $request->filled('slug') ? strtolower(trim($request->slug)) : null;
        $validated['is_active'] = $request->has('is_active');
        $validated['has_online_parts'] = $request->has('has_online_parts');
        $validated['testing_in_person'] = $request->has('testing_in_person', true); // Default true
        $validated['requires_dallas_law'] = $request->has('requires_dallas_law');
        $validated['requires_active_shooter'] = $request->has('requires_active_shooter');

        $linkedIds = $request->input('linked_services', []);
        unset($validated['linked_services']);

        $validated['categories'] = array_values(array_filter($request->input('categories', [])));
        $validated['sub_titles'] = array_values(array_filter(array_map('trim', $request->input('sub_titles', []))));

        $service = Service::create($validated);

        $this->syncClassSchedulesFromRequest($service, $request, true);

        if (! empty($linkedIds)) {
            $sync = [];
            foreach (array_values(array_filter($linkedIds)) as $i => $id) {
                $sync[$id] = ['order' => $i];
            }
            $service->linkedServices()->sync($sync);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $service->load(['linkedServices', 'classSchedules' => function ($q) {
            $q->orderBy('class_date')->orderBy('start_time');
        }]);
        $allServices = Service::where('is_active', true)->where('id', '!=', $service->id)->orderBy('order')->orderBy('title')->get();

        return view('admin.services.edit', compact('service', 'allServices'));
    }

    /**
     * Update the specified service. New images are saved under public/assets/images/classes.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:services,slug,'.$service->id.'|regex:/^[a-z0-9\-]+$/',
            'short_description' => 'nullable|string|max:500',
            'requirements' => 'nullable|string',
            'sub_titles' => 'nullable|array',
            'sub_titles.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
            'min_students' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            // Category fields (multiple)
            'categories' => 'nullable|array',
            'categories.*' => 'string|in:'.implode(',', array_keys(config('service_categories', []))),
            'subcategory' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'requires_dallas_law' => 'boolean',
            'requires_active_shooter' => 'boolean',
            // Pricing fields
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            // Class configuration
            'class_type' => 'nullable|in:group,one-on-one',
            'has_online_parts' => 'boolean',
            'testing_in_person' => 'boolean',
            'linked_services' => 'nullable|array',
            'linked_services.*' => 'integer|exists:services,id',
            'schedules' => 'nullable|array',
            'schedules.*.id' => 'nullable|integer',
            'schedules.*.class_date' => 'nullable|date',
            'schedules.*.start_time' => 'nullable',
            'schedules.*.duration_hours' => 'nullable|integer|min:1|max:48',
            'schedules.*.max_students' => 'nullable|integer|min:1|max:100',
            'schedules.*.min_students' => 'nullable|integer|min:1|max:100',
            'schedules.*.location' => 'nullable|string|max:255',
            'schedules.*.room' => 'nullable|string|max:255',
            'schedules.*.instructor' => 'nullable|string|max:255',
            'schedules.*.notes' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStoredServiceImage($service->image);
            $validated['image'] = $this->storeUploadedClassImage($request->file('image'));
        }

        $validated['slug'] = $request->filled('slug') ? strtolower(trim($request->slug)) : null;
        $validated['is_active'] = $request->has('is_active');
        $validated['has_online_parts'] = $request->has('has_online_parts');
        $validated['testing_in_person'] = $request->has('testing_in_person', true); // Default true
        $validated['requires_dallas_law'] = $request->has('requires_dallas_law');
        $validated['requires_active_shooter'] = $request->has('requires_active_shooter');

        $linkedIds = $request->input('linked_services', []);
        unset($validated['linked_services']);

        $validated['categories'] = array_values(array_filter($request->input('categories', [])));
        $validated['sub_titles'] = array_values(array_filter(array_map('trim', $request->input('sub_titles', []))));

        $service->update($validated);

        $this->syncClassSchedulesFromRequest($service, $request, false);

        $linkedIds = array_filter(array_map('intval', $linkedIds));
        $sync = [];
        foreach (array_values($linkedIds) as $i => $id) {
            if ($id !== (int) $service->id) {
                $sync[$id] = ['order' => $i];
            }
        }
        $service->linkedServices()->sync($sync);

        return redirect()->route('admin.services.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $this->deleteStoredServiceImage($service->image);

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Class deleted successfully.');
    }

    /**
     * Create, update, or remove class_schedules rows submitted with the service form.
     */
    protected function syncClassSchedulesFromRequest(Service $service, Request $request, bool $isNewService): void
    {
        if (! $request->has('sync_schedules')) {
            return;
        }

        $rows = $request->input('schedules', []);
        if (! is_array($rows)) {
            return;
        }

        $rows = array_values(array_filter($rows, function ($row) {
            if (! is_array($row)) {
                return false;
            }

            return ! empty($row['class_date']) && $row['start_time'] !== null && $row['start_time'] !== '';
        }));

        if ($rows === []) {
            foreach ($service->classSchedules as $existing) {
                if ($existing->bookings()->exists()) {
                    throw ValidationException::withMessages([
                        'schedules' => ['Cannot remove all sessions: some have bookings. Edit or cancel those bookings first.'],
                    ]);
                }
                $existing->delete();
            }

            return;
        }

        foreach ($rows as $i => $row) {
            if (! empty($row['id'])) {
                $exists = ClassSchedule::where('id', (int) $row['id'])
                    ->where('service_id', $service->id)
                    ->exists();
                if (! $exists) {
                    throw ValidationException::withMessages([
                        "schedules.{$i}.id" => ['Invalid session for this class.'],
                    ]);
                }
            } elseif (! $isNewService && Carbon::parse($row['class_date'])->lt(now()->startOfDay())) {
                throw ValidationException::withMessages([
                    "schedules.{$i}.class_date" => ['New sessions must be on or after today.'],
                ]);
            } elseif ($isNewService && Carbon::parse($row['class_date'])->lt(now()->startOfDay())) {
                throw ValidationException::withMessages([
                    "schedules.{$i}.class_date" => ['Session date must be on or after today.'],
                ]);
            }
        }

        $seenSlots = [];

        foreach ($rows as $i => $row) {
            $location = $row['location'] ?? null;
            $location = $location === '' ? null : $location;
            $fingerprint = ClassSchedule::slotFingerprint(
                $service->id,
                $row['class_date'],
                $row['start_time'],
                $location
            );

            if (isset($seenSlots[$fingerprint])) {
                throw ValidationException::withMessages([
                    'schedules' => ['You cannot add duplicate sessions on the same date, time, and location.'],
                ]);
            }

            $seenSlots[$fingerprint] = true;

            $ignoreId = ! empty($row['id']) ? (int) $row['id'] : null;

            if (ClassSchedule::duplicateExists(
                $service->id,
                $row['class_date'],
                $row['start_time'],
                $location,
                $ignoreId
            )) {
                throw ValidationException::withMessages([
                    "schedules.{$i}.class_date" => ['A session with the same date, time, and location already exists for this class.'],
                ]);
            }
        }

        $submittedIds = collect($rows)->pluck('id')->filter()->map(fn ($id) => (int) $id);

        foreach ($service->classSchedules()->get() as $existing) {
            if ($submittedIds->contains($existing->id)) {
                continue;
            }
            if ($existing->bookings()->exists()) {
                throw ValidationException::withMessages([
                    'schedules' => ['Cannot remove a session that has bookings ('
                        .$existing->class_date->format('M j, Y').'). Remove bookings first or leave the session in the list.'],
                ]);
            }
            $existing->delete();
        }

        foreach ($rows as $row) {
            $durationHours = (int) ($row['duration_hours'] ?? 8);
            $maxStudents = (int) ($row['max_students'] ?? 10);
            $minStudents = (int) ($row['min_students'] ?? 1);
            if ($minStudents > $maxStudents) {
                throw ValidationException::withMessages([
                    'schedules' => ['Minimum students cannot exceed maximum for a session.'],
                ]);
            }

            $startTime = Carbon::createFromFormat('Y-m-d H:i', $row['class_date'].' '.$row['start_time']);
            $endTime = $startTime->copy()->addHours($durationHours);

            $location = $row['location'] ?? null;
            $location = $location === '' ? null : $location;

            $payload = [
                'class_date' => $row['class_date'],
                'start_time' => Carbon::parse($row['start_time'])->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'duration_hours' => $durationHours,
                'max_students' => $maxStudents,
                'min_students' => $minStudents,
                'room' => $row['room'] ?? null,
                'location' => $location,
                'instructor' => $row['instructor'] ?? null,
                'can_overlap' => ! empty($row['can_overlap']),
                'notes' => $row['notes'] ?? null,
            ];

            if (! empty($row['id'])) {
                $schedule = ClassSchedule::where('id', (int) $row['id'])
                    ->where('service_id', $service->id)
                    ->firstOrFail();

                if ($schedule->current_students > $maxStudents) {
                    throw ValidationException::withMessages([
                        'schedules' => ['Max students cannot be below current enrollment ('.$schedule->current_students.') for a session.'],
                    ]);
                }

                $status = $schedule->status;
                if ($schedule->current_students >= $maxStudents) {
                    $status = 'full';
                } elseif ($status === 'full' && $schedule->current_students < $maxStudents) {
                    $status = 'scheduled';
                }
                $payload['status'] = $status;
                $schedule->update($payload);
            } else {
                ClassSchedule::create(array_merge($payload, [
                    'service_id' => $service->id,
                    'current_students' => 0,
                    'status' => 'scheduled',
                ]));
            }
        }
    }

    /**
     * Save an uploaded class image under public/assets/images/classes. Returns the filename for the DB column.
     */
    protected function storeUploadedClassImage(UploadedFile $file): string
    {
        $directory = public_path(Service::CLASS_IMAGE_PUBLIC_PATH);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $filename = now()->format('Y-m-d-His').'-'.Str::random(8).'.'.$extension;
        $file->move($directory, $filename);

        return $filename;
    }

    /**
     * Remove a service image from disk (filename in public class folder, full assets/ path, or legacy storage).
     */
    protected function deleteStoredServiceImage(?string $storedPath): void
    {
        if ($storedPath === null || $storedPath === '') {
            return;
        }

        if (str_starts_with($storedPath, 'assets/')) {
            $fullPath = public_path($storedPath);
            if (is_file($fullPath)) {
                unlink($fullPath);
            }

            return;
        }

        if (str_contains($storedPath, '/')) {
            Storage::disk('public')->delete($storedPath);

            return;
        }

        $fullPath = public_path(Service::CLASS_IMAGE_PUBLIC_PATH.'/'.basename($storedPath));
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
