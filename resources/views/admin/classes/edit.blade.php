@extends('admin.layouts.master')

@section('title', 'Edit Class')
@section('page-title', 'Edit Class')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.classes.update', $service) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title *</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   value="{{ old('title', $service->title) }}"
                   required 
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Direct page slug (optional)</label>
            <input type="text" 
                   id="slug" 
                   name="slug" 
                   value="{{ old('slug', $service->slug) }}"
                   placeholder="e.g. asp, active-shooter, dallas-law"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">If set, this service has a direct page at <strong>/service/{slug}</strong>. Use lowercase letters, numbers and hyphens only.</p>
            @error('slug')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="short_description" class="block text-gray-700 text-sm font-bold mb-2">Short Description</label>
            <textarea id="short_description" 
                      name="short_description" 
                      rows="3"
                      maxlength="500"
                      placeholder="Brief description (max 500 characters)"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('short_description', $service->short_description) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
            @error('short_description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="requirements" class="block text-gray-700 text-sm font-bold mb-2">Requirements</label>
            <div id="requirements-editor" class="bg-white border rounded">
                {!! old('requirements', $service->requirements) !!}
            </div>
            <textarea id="requirements" name="requirements" style="display:none;">{{ old('requirements') }}</textarea>
            @error('requirements')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Sub titles (show below banner on class page)</label>
            <p class="text-xs text-gray-500 mb-2">Add items like "Flashlight", "OC Spray", "Baton", "Restraints". These appear in a list below the hero banner on the left.</p>
            <div id="sub-titles-container" class="space-y-2">
                @php
                    $subTitlesValues = old('sub_titles', $service->sub_titles ?? []);
                    $subTitlesValues = is_array($subTitlesValues) ? $subTitlesValues : [];
                @endphp
                @foreach($subTitlesValues as $st)
                    <div class="sub-title-row flex gap-2 items-center">
                        <input type="text" name="sub_titles[]" value="{{ $st }}" maxlength="255" placeholder="e.g. Flashlight"
                            class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" class="sub-title-remove px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-sub-title" class="mt-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add sub title
            </button>
            @error('sub_titles.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <div id="description-editor" class="bg-white border rounded">
                {!! old('description', $service->description) !!}
            </div>
            <textarea id="description" name="description" style="display:none;">{{ old('description', $service->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
            @if($service->image)
                <div class="mb-2">
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="h-32 w-32 object-cover rounded">
                </div>
            @endif
            <input type="file" 
                   id="image" 
                   name="image" 
                   accept="image/*"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Order</label>
            <input type="number" 
                   id="order" 
                   name="order" 
                   value="{{ old('order', $service->order) }}"
                   min="0"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('order')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="min_students" class="block text-gray-700 text-sm font-bold mb-2">Min Students</label>
            <input type="number" 
                   id="min_students" 
                   name="min_students" 
                   value="{{ old('min_students', $service->min_students) }}"
                   min="1"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="max_students" class="block text-gray-700 text-sm font-bold mb-2">Max Students</label>
            <input type="number" 
                   id="max_students" 
                   name="max_students" 
                   value="{{ old('max_students', $service->max_students) }}"
                   max="100"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <!-- Category Section -->
        <div class="mb-6 border-t pt-4 mt-6">
            <h3 class="text-lg font-bold mb-4">Category & Organization</h3>
            <p class="text-sm text-gray-600 mb-3">Categories are optional. Use a direct page slug above for standalone services.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Categories (optional)</label>
                    <!-- Selected tags (shown at top) -->
                    <div id="selected-categories-display" class="min-h-[52px] mb-3 p-3 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 flex flex-wrap gap-2 items-center">
                        <span id="no-selection-hint" class="text-sm text-gray-400">No categories selected — choose below</span>
                    </div>
                    <!-- Checkbox list -->
                    <p class="text-xs text-gray-600 font-medium mb-2">Select categories</p>
                    <div class="max-h-36 overflow-y-auto border rounded-lg p-3 bg-white space-y-1.5">
                        @php
                            $selectedCategories = old('categories', $service->categories ?? []);
                            $selectedCategories = is_array($selectedCategories) ? $selectedCategories : [];
                        @endphp
                        @foreach(config('service_categories', []) as $slug => $label)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-green-50 p-2 rounded transition-colors category-checkbox-label" data-slug="{{ $slug }}" data-label="{{ $label }}">
                                <input type="checkbox"
                                       name="categories[]"
                                       value="{{ $slug }}"
                                       {{ in_array($slug, $selectedCategories) ? 'checked' : '' }}
                                       class="rounded border-gray-400 text-green-600 focus:ring-green-500 category-checkbox">
                                <span class="text-sm text-gray-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subcategory" class="block text-gray-700 text-sm font-bold mb-2">Subcategory</label>
                    <input type="text" 
                           id="subcategory" 
                           name="subcategory" 
                           value="{{ old('subcategory', $service->subcategory) }}"
                           placeholder="e.g., Armed Security, ASP, Force Science"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('subcategory')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                    <select id="location" 
                            name="location" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">No Specific Location</option>
                        <option value="Location A" {{ old('location', $service->location) === 'Location A' ? 'selected' : '' }}>Shooter's Guns, Ammo, and Range 575  Murfreesboro Pike, Nashville, Tn 37210</option>
                        <option value="Location B" {{ old('location', $service->location) === 'Location B' ? 'selected' : '' }}>Guns and Leather 2216 US-41, Greenbrier, Tn 37073</option>
                    </select>
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_dallas_law" 
                               value="1"
                               {{ old('requires_dallas_law', $service->requires_dallas_law) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-gray-700">Requires Dallas Law Training</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="requires_active_shooter" 
                               value="1"
                               {{ old('requires_active_shooter', $service->requires_active_shooter) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-gray-700">Requires Active Shooter Training</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div class="mb-6 border-t pt-4 mt-6">
            <h3 class="text-lg font-bold mb-4">Pricing & Class Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price ($)</label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price', $service->price) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deposit_amount" class="block text-gray-700 text-sm font-bold mb-2">Deposit Amount ($)</label>
                    <input type="number" 
                           id="deposit_amount" 
                           name="deposit_amount" 
                           value="{{ old('deposit_amount', $service->deposit_amount) }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('deposit_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="class_type" class="block text-gray-700 text-sm font-bold mb-2">Class Type</label>
                    <select id="class_type" 
                            name="class_type" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="group" {{ old('class_type', $service->class_type ?? 'group') === 'group' ? 'selected' : '' }}>Group</option>
                        <option value="one-on-one" {{ old('class_type', $service->class_type) === 'one-on-one' ? 'selected' : '' }}>One-on-One</option>
                    </select>
                    @error('class_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="has_online_parts" 
                           value="1"
                           {{ old('has_online_parts', $service->has_online_parts) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-700">Has online parts/components (blended course)</span>
                </label>
                @if($service->has_online_parts)
                    <a href="{{ route('admin.classes.course-modules.index', $service) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">
                        <i class="fas fa-book-open mr-1"></i> Manage Online Modules & Quizzes
                    </a>
                    <a href="{{ route('admin.classes.blended-progress', $service) }}" class="inline-block mt-2 ml-4 text-blue-600 hover:underline text-sm">
                        <i class="fas fa-user-graduate mr-1"></i> Student Progress & In-Person Tests
                    </a>
                @endif
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="testing_in_person" 
                           value="1"
                           {{ old('testing_in_person', $service->testing_in_person ?? true) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-700">Testing is in-person (always true)</span>
                </label>
            </div>

            @include('admin.classes._extended_fields', [
                    'refundPolicy' => $service->refund_policy,
                    'whatToBring' => $service->what_to_bring,
                    'prerequisites' => $service->prerequisites,
                    'isTravelBased' => $service->is_travel_based,
                    'travelDistanceFee' => $service->travel_distance_fee,
                    'travelLodgingFee' => $service->travel_lodging_fee,
                    'travelTimeFee' => $service->travel_time_fee,
                    'travelMinimumStudents' => $service->travel_minimum_students,
                    'travelNotes' => $service->travel_notes,
                    'lodgingInstructions' => $service->lodging_instructions,
                ])
        </div>

        <!-- Class sessions (saved to class_schedules) -->
        <div class="mb-6 border-t pt-4 mt-6">
            <input type="hidden" name="sync_schedules" value="1">
            <h3 class="text-lg font-bold mb-2">Class sessions (schedule)</h3>
            <p class="text-sm text-gray-600 mb-3">Edit upcoming sessions or add new ones. Sessions with bookings cannot be removed from this list until those bookings are cancelled.</p>
            <div id="schedules-container" class="space-y-4">
                @php
                    $schedRows = old('schedules');
                    if (!is_array($schedRows)) {
                        $schedRows = $service->classSchedules->map(function ($s) {
                            return [
                                'id' => $s->id,
                                'class_date' => $s->class_date->format('Y-m-d'),
                                'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                                'duration_hours' => $s->duration_hours,
                                'max_students' => $s->max_students,
                                'min_students' => $s->min_students,
                                'location' => $s->location ?? '',
                                'room' => $s->room,
                                'instructor' => $s->instructor,
                                'notes' => $s->notes,
                                'can_overlap' => $s->can_overlap,
                            ];
                        })->toArray();
                    }
                @endphp
                @foreach($schedRows as $idx => $sch)
                    <div class="schedule-row border rounded-lg p-4 bg-gray-50 space-y-3">
                        @if(!empty($sch['id']))
                            <input type="hidden" name="schedules[{{ $idx }}][id]" value="{{ $sch['id'] }}">
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Session <span class="schedule-num">{{ $loop->iteration }}</span></span>
                            <button type="button" class="schedule-remove text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Date *</label>
                                <input type="date" name="schedules[{{ $idx }}][class_date]" value="{{ $sch['class_date'] ?? '' }}"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Start time *</label>
                                <input type="time" name="schedules[{{ $idx }}][start_time]" value="{{ $sch['start_time'] ?? '' }}"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Duration (hours) *</label>
                                <input type="number" name="schedules[{{ $idx }}][duration_hours]" value="{{ $sch['duration_hours'] ?? 8 }}" min="1" max="48"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Location</label>
                                <select name="schedules[{{ $idx }}][location]" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                                    <option value="" {{ ($sch['location'] ?? '') === '' ? 'selected' : '' }}>No Specific Location</option>
                                    <option value="Location A" {{ ($sch['location'] ?? '') === 'Location A' ? 'selected' : '' }}>Location A (Nashville)</option>
                                    <option value="Location B" {{ ($sch['location'] ?? '') === 'Location B' ? 'selected' : '' }}>Location B (Greenbrier)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Max students *</label>
                                <input type="number" name="schedules[{{ $idx }}][max_students]" value="{{ $sch['max_students'] ?? 10 }}" min="1" max="100"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Min students *</label>
                                <input type="number" name="schedules[{{ $idx }}][min_students]" value="{{ $sch['min_students'] ?? 1 }}" min="1" max="100"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Room</label>
                                <input type="text" name="schedules[{{ $idx }}][room]" value="{{ $sch['room'] ?? '' }}" maxlength="255"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Instructor</label>
                                <input type="text" name="schedules[{{ $idx }}][instructor]" value="{{ $sch['instructor'] ?? '' }}" maxlength="255"
                                    class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1">Notes</label>
                            <input type="text" name="schedules[{{ $idx }}][notes]" value="{{ $sch['notes'] ?? '' }}" maxlength="2000"
                                class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="schedules[{{ $idx }}][can_overlap]" value="1" {{ !empty($sch['can_overlap']) ? 'checked' : '' }} class="rounded">
                            Allow overlapping sessions
                        </label>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-schedule-row" class="mt-3 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add session
            </button>
            @error('schedules')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Related Services (Linked / Related Trainings) -->
        @php
            $linkedIds = old('linked_services', $service->linkedServices->pluck('id')->toArray());
            $linkedIds = is_array($linkedIds) ? array_map('intval', $linkedIds) : [];
            $linkedOrdered = $service->linkedServices->keyBy('id');
            $others = ($allServices ?? collect())->whereNotIn('id', $linkedOrdered->keys());
            $hasAnyToShow = $service->linkedServices->isNotEmpty() || $others->isNotEmpty();
        @endphp
        <div class="mb-6 border-t pt-4 mt-6">
            <h3 class="text-lg font-bold mb-2">Related Services</h3>
            <p class="text-sm text-gray-600 mb-3">Choose which trainings to show as related on this service’s page (e.g. Unarmed → Less Lethal, Dallas Law). Order is kept: linked first, then the rest.</p>
            <div class="max-h-56 overflow-y-auto border rounded p-3 bg-gray-50 space-y-2">
                @if($hasAnyToShow)
                    @if($service->linkedServices->isNotEmpty())
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Currently linked (in order)</p>
                        @foreach($service->linkedServices as $s)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                                <input type="checkbox"
                                       name="linked_services[]"
                                       value="{{ $s->id }}"
                                       {{ in_array((int)$s->id, $linkedIds) ? 'checked' : '' }}
                                       class="rounded border-gray-400">
                                <span class="text-sm text-gray-800">{{ $s->title }}</span>
                                @if($s->subcategory)
                                    <span class="text-xs text-gray-500">({{ $s->subcategory }})</span>
                                @endif
                            </label>
                        @endforeach
                    @endif
                    @if($others->isNotEmpty())
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-3 mb-2">Other services — check to add as related</p>
                        @foreach($others as $s)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                                <input type="checkbox"
                                       name="linked_services[]"
                                       value="{{ $s->id }}"
                                       {{ in_array((int)$s->id, $linkedIds) ? 'checked' : '' }}
                                       class="rounded border-gray-400">
                                <span class="text-sm text-gray-800">{{ $s->title }}</span>
                                @if($s->subcategory)
                                    <span class="text-xs text-gray-500">({{ $s->subcategory }})</span>
                                @endif
                            </label>
                        @endforeach
                    @endif
                @else
                    <p class="text-sm text-gray-500 py-2">No other services to link. Create more services first.</p>
                @endif
            </div>
            @error('linked_services.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                       class="mr-2">
                <span class="text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-save mr-2"></i> Update Service
            </button>
            <a href="{{ route('admin.classes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Quill Rich Text Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get existing content from textarea
        var existingContent = document.getElementById('description').value;
        
        function createEditor(editorSelector, inputId, placeholderText) {
            var editor = new Quill(editorSelector, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean'],
                        [{ 'color': [] }]
                    ]
                },
                placeholder: placeholderText
            });

            editor.on('text-change', function() {
                var input = document.getElementById(inputId);
                input.value = editor.root.innerHTML;
            });

            return editor;
        }

        var descriptionQuill = createEditor('#description-editor', 'description', 'Enter service description...');
        var requirementsQuill = createEditor('#requirements-editor', 'requirements', 'Enter service requirements...');




        // Also update textarea before form submit (as backup)
        var form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            var descriptionInput = document.getElementById('description');
            var requirementsInput = document.getElementById('requirements');
            descriptionInput.value = descriptionQuill.root.innerHTML;
            requirementsInput.value = requirementsQuill.root.innerHTML;
        });

        // Category selection UI: update selected tags display
        function updateSelectedCategories() {
            var container = document.getElementById('selected-categories-display');
            var hint = document.getElementById('no-selection-hint');
            var checkboxes = document.querySelectorAll('.category-checkbox:checked');
            var existingTags = container.querySelectorAll('.selected-tag');
            existingTags.forEach(function(t) { t.remove(); });
            if (checkboxes.length === 0) {
                hint.style.display = 'inline';
            } else {
                hint.style.display = 'none';
                checkboxes.forEach(function(cb) {
                    var labelEl = document.querySelector('.category-checkbox-label[data-slug="' + cb.value + '"]');
                    var chip = document.createElement('span');
                    chip.className = 'selected-tag inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-600 text-white';
                    chip.textContent = labelEl ? labelEl.getAttribute('data-label') : cb.value;
                    container.appendChild(chip);
                });
            }
        }
        document.querySelectorAll('.category-checkbox').forEach(function(cb) {
            cb.addEventListener('change', updateSelectedCategories);
        });
        updateSelectedCategories();

        // Sub titles: add / remove rows
        var subTitlesContainer = document.getElementById('sub-titles-container');
        var addSubTitleBtn = document.getElementById('add-sub-title');
        if (addSubTitleBtn) {
            addSubTitleBtn.addEventListener('click', function() {
                var row = document.createElement('div');
                row.className = 'sub-title-row flex gap-2 items-center';
                row.innerHTML = '<input type="text" name="sub_titles[]" maxlength="255" placeholder="e.g. Flashlight" class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">' +
                    '<button type="button" class="sub-title-remove px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">Remove</button>';
                subTitlesContainer.appendChild(row);
                row.querySelector('.sub-title-remove').addEventListener('click', function() { row.remove(); });
            });
        }
        if (subTitlesContainer) {
            subTitlesContainer.querySelectorAll('.sub-title-remove').forEach(function(btn) {
                btn.addEventListener('click', function() { btn.closest('.sub-title-row').remove(); });
            });
        }

        var scheduleContainer = document.getElementById('schedules-container');
        var scheduleIndex = scheduleContainer ? scheduleContainer.querySelectorAll('.schedule-row').length : 0;
        var minDateStr = @json(now()->toDateString());

        function renumberSchedules() {
            if (!scheduleContainer) return;
            scheduleContainer.querySelectorAll('.schedule-row').forEach(function(row, i) {
                var n = row.querySelector('.schedule-num');
                if (n) n.textContent = String(i + 1);
            });
        }

        function bindScheduleRow(row) {
            var rm = row.querySelector('.schedule-remove');
            if (rm) {
                rm.addEventListener('click', function() {
                    row.remove();
                    renumberSchedules();
                });
            }
        }

        if (scheduleContainer) {
            scheduleContainer.querySelectorAll('.schedule-row').forEach(bindScheduleRow);
        }

        var addScheduleBtn = document.getElementById('add-schedule-row');
        if (addScheduleBtn && scheduleContainer) {
            addScheduleBtn.addEventListener('click', function() {
                var i = scheduleIndex++;
                var div = document.createElement('div');
                div.className = 'schedule-row border rounded-lg p-4 bg-gray-50 space-y-3';
                div.innerHTML = '<div class="flex justify-between items-center">' +
                    '<span class="text-sm font-semibold text-gray-700">Session <span class="schedule-num">0</span></span>' +
                    '<button type="button" class="schedule-remove text-red-600 hover:text-red-800 text-sm font-medium">Remove</button></div>' +
                    '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Date *</label>' +
                    '<input type="date" name="schedules[' + i + '][class_date]" min="' + minDateStr + '" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Start time *</label>' +
                    '<input type="time" name="schedules[' + i + '][start_time]" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Duration (hours) *</label>' +
                    '<input type="number" name="schedules[' + i + '][duration_hours]" value="8" min="1" max="48" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Location</label>' +
                    '<select name="schedules[' + i + '][location]" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">' +
                    '<option value="">No Specific Location</option>' +
                    '<option value="Location A">Location A (Nashville)</option>' +
                    '<option value="Location B">Location B (Greenbrier)</option></select></div></div>' +
                    '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Max students *</label>' +
                    '<input type="number" name="schedules[' + i + '][max_students]" value="10" min="1" max="100" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Min students *</label>' +
                    '<input type="number" name="schedules[' + i + '][min_students]" value="1" min="1" max="100" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Room</label>' +
                    '<input type="text" name="schedules[' + i + '][room]" maxlength="255" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Instructor</label>' +
                    '<input type="text" name="schedules[' + i + '][instructor]" maxlength="255" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div></div>' +
                    '<div><label class="block text-gray-700 text-xs font-bold mb-1">Notes</label>' +
                    '<input type="text" name="schedules[' + i + '][notes]" maxlength="2000" class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700"></div>' +
                    '<label class="inline-flex items-center gap-2 text-sm text-gray-700">' +
                    '<input type="checkbox" name="schedules[' + i + '][can_overlap]" value="1" class="rounded"> Allow overlapping sessions</label>';
                scheduleContainer.appendChild(div);
                bindScheduleRow(div);
                renumberSchedules();
            });
        }
    });
</script>
<style>
    .ql-container {
        min-height: 300px;
    }
</style>
@endsection
