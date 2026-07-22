@extends('admin.layouts.master')

@section('title', 'Create Class')
@section('page-title', 'Create New Class')

@section('content')
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-900">Create New Class</h3>
    <p class="mt-1 text-sm text-gray-500">Fill the sections below. You can add bookable date/time sessions in step 4, or later from Class Schedules.</p>
</div>

<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
    <div class="flex gap-3">
        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
        <div class="space-y-1">
            <p class="font-semibold">Quick guide</p>
            <ul class="list-disc space-y-0.5 pl-4 text-blue-800">
                <li><span class="font-medium">Basics</span> — name, image, and where the class appears on the website.</li>
                <li><span class="font-medium">Pricing & capacity</span> — student price and default seat limits.</li>
                <li><span class="font-medium">Sessions</span> — each session is one date/time students can book (optional now).</li>
            </ul>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.classes.store') }}" enctype="multipart/form-data" class="space-y-6 pb-24">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @include('admin.classes.partials.section-header', ['step' => '1', 'title' => 'Basics', 'hint' => 'Title and short info shown on the website and in the admin list.'])

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Class title *</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   value="{{ old('title') }}"
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
                   value="{{ old('slug') }}"
                   placeholder="e.g. asp, active-shooter, dallas-law"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">Leave empty if using categories. If set, this service will have a direct page at <strong>/service/{slug}</strong> (e.g. /service/asp). Use lowercase letters, numbers and hyphens only. No category needed for direct-page services.</p>
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
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('short_description') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
            @error('short_description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="requirements" class="block text-gray-700 text-sm font-bold mb-2">Requirements</label>
            <div id="requirements-editor" class="bg-white border rounded">
                {!! old('requirements') !!}
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
                @foreach(old('sub_titles', []) as $idx => $st)
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
                {!! old('description') !!}
            </div>
            <textarea id="description" name="description" style="display:none;">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
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
                   value="{{ old('order', 0) }}"
                   min="0"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('order')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
            <div>
                <label for="min_students" class="block text-gray-700 text-sm font-bold mb-2">Default min students</label>
                <input type="number"
                       id="min_students"
                       name="min_students"
                       value="{{ old('min_students', 1) }}"
                       min="1"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">Minimum needed for a session to run (used as default for new sessions).</p>
            </div>
            <div>
                <label for="max_students" class="block text-gray-700 text-sm font-bold mb-2">Default max students</label>
                <input type="number"
                       id="max_students"
                       name="max_students"
                       value="{{ old('max_students', 10) }}"
                       min="1"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">Seat capacity per session (used as default for new sessions).</p>
            </div>
        </div>
        </div>{{-- end section 1 --}}

        <!-- Category Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @include('admin.classes.partials.section-header', ['step' => '2', 'title' => 'Category & Organization', 'hint' => 'Optional. Use categories for listing pages, or a direct slug for a standalone page.'])
            <p class="text-sm text-gray-600 mb-3">If you leave all categories unchecked and set a <strong>Direct page slug</strong> above, this class appears only at <strong>/service/{slug}</strong>.</p>
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
                        @foreach(config('service_categories', []) as $slug => $label)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-green-50 p-2 rounded transition-colors category-checkbox-label" data-slug="{{ $slug }}" data-label="{{ $label }}">
                                <input type="checkbox"
                                       name="categories[]"
                                       value="{{ $slug }}"
                                       {{ in_array($slug, old('categories', [])) ? 'checked' : '' }}
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
                           value="{{ old('subcategory') }}"
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
                        <option value="Location A" {{ old('location') === 'Location A' ? 'selected' : '' }}>Shooter's Guns, Ammo, and Range 575  Murfreesboro Pike, Nashville, Tn 37210</option>
                        <option value="Location B" {{ old('location') === 'Location B' ? 'selected' : '' }}>Guns and Leather 2216 US-41, Greenbrier, Tn 37073</option>
                    </select>
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2">
                <label for="requires_dallas_law" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-purple-300 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                    <input type="checkbox"
                           id="requires_dallas_law"
                           name="requires_dallas_law"
                           value="1"
                           {{ old('requires_dallas_law') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-400 text-purple-600 focus:ring-purple-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Requires Dallas Law Training</span>
                        <p class="mt-0.5 text-xs text-gray-500">Students should complete Dallas Law before this class.</p>
                    </div>
                </label>

                <label for="requires_active_shooter" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-rose-300 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                    <input type="checkbox"
                           id="requires_active_shooter"
                           name="requires_active_shooter"
                           value="1"
                           {{ old('requires_active_shooter') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-400 text-rose-600 focus:ring-rose-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Requires Active Shooter Training</span>
                        <p class="mt-0.5 text-xs text-gray-500">Students should complete Active Shooter before this class.</p>
                    </div>
                </label>
            </div>
        </div>{{-- end section 2 --}}

        <!-- Pricing Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @include('admin.classes.partials.section-header', ['step' => '3', 'title' => 'Pricing & Class Setup', 'hint' => 'What students pay and how the class is delivered.'])

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-start">
                <div>
                    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Full price ($)</label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price') }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deposit_amount" class="block text-gray-700 text-sm font-bold mb-2">Deposit amount ($)</label>
                    <input type="number" 
                           id="deposit_amount" 
                           name="deposit_amount" 
                           value="{{ old('deposit_amount') }}"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-xs text-gray-500 mt-1">Optional at booking; remainder later.</p>
                    @error('deposit_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_type" class="block text-gray-700 text-sm font-bold mb-2">Class Type</label>
                    <select id="class_type" 
                            name="class_type" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="group" {{ old('class_type', 'group') === 'group' ? 'selected' : '' }}>Group</option>
                        <option value="one-on-one" {{ old('class_type') === 'one-on-one' ? 'selected' : '' }}>One-on-One</option>
                    </select>
                    @error('class_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @include('admin.classes.partials.blended-delivery-options')

            @include('admin.classes._extended_fields')
        </div>{{-- end section 3 --}}

        <!-- Class sessions (saved to class_schedules) -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <input type="hidden" name="sync_schedules" value="1">
            @include('admin.classes.partials.section-header', ['step' => '4', 'title' => 'Class Sessions', 'hint' => 'Each session = one bookable date/time. Leave empty if you will add sessions later.'])
            <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                <i class="fas fa-lightbulb mr-1 text-amber-600"></i>
                <strong>Sessions</strong> are what show on Class Schedules and the public booking form. Min/max on each session can override the class defaults above.
            </div>
            <div id="schedules-container" class="space-y-4">
                @php
                    $schedRows = old('schedules');
                    if (!is_array($schedRows)) {
                        $schedRows = [[]];
                    }
                @endphp
                @foreach($schedRows as $idx => $sch)
                    <div class="schedule-row rounded-lg border border-slate-200 bg-slate-50 p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-700 text-xs text-white"><span class="schedule-num">{{ $loop->iteration }}</span></span>
                                Session
                            </span>
                            <button type="button" class="schedule-remove text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Date *</label>
                                <input type="date" name="schedules[{{ $idx }}][class_date]" value="{{ $sch['class_date'] ?? '' }}" min="{{ date('Y-m-d') }}"
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
                                <input type="text" name="schedules[{{ $idx }}][room]" value="{{ $sch['room'] ?? '' }}" maxlength="255" placeholder="Room / range"
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
                            <input type="text" name="schedules[{{ $idx }}][notes]" value="{{ $sch['notes'] ?? '' }}" maxlength="2000" placeholder="Optional"
                                class="shadow border rounded w-full py-2 px-2 text-sm text-gray-700">
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="schedules[{{ $idx }}][can_overlap]" value="1" {{ !empty($sch['can_overlap']) ? 'checked' : '' }} class="rounded">
                            Allow overlapping sessions
                        </label>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-schedule-row" class="mt-3 inline-flex items-center px-4 py-2 bg-green-50 text-green-800 ring-1 ring-inset ring-green-200 rounded hover:bg-green-100 text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add another session
            </button>
            @error('schedules')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>{{-- end section 4 --}}

        <!-- Linked Services (Related Trainings) -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @include('admin.classes.partials.section-header', ['step' => '5', 'title' => 'Related Trainings', 'hint' => 'Optional. Other classes to suggest on this class’s public page.'])
            <p class="text-sm text-gray-600 mb-3">Select trainings to show as related (e.g. Unarmed → Less Lethal, Dallas Law).</p>
            <div class="max-h-48 overflow-y-auto border rounded p-3 bg-gray-50 space-y-2">
                @forelse(($allServices ?? collect()) as $s)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                        <input type="checkbox"
                               name="linked_services[]"
                               value="{{ $s->id }}"
                               {{ in_array($s->id, old('linked_services', [])) ? 'checked' : '' }}
                               class="rounded">
                        <span class="text-sm text-gray-800">{{ $s->title }}</span>
                        @if($s->subcategory)
                            <span class="text-xs text-gray-500">({{ $s->subcategory }})</span>
                        @endif
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No other active services. Create more services to link.</p>
                @endforelse
            </div>
            @error('linked_services.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <div class="mt-6 border-t border-gray-100 pt-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                    <span class="text-sm font-medium text-gray-800">Active — show this class on the website</span>
                </label>
            </div>
        </div>{{-- end section 5 --}}

        <div class="fixed bottom-0 inset-x-0 z-20 border-t border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg sm:static sm:inset-auto sm:z-auto sm:mt-2 sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none sm:backdrop-blur-none">
            <div class="mx-auto flex max-w-full flex-wrap items-center justify-end gap-3 sm:justify-start">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-5 rounded shadow-sm">
                    <i class="fas fa-save mr-2"></i> Create Class
                </button>
                <a href="{{ route('admin.classes.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-5 rounded border border-gray-300">
                    Cancel
                </a>
            </div>
        </div>
</form>

<!-- Quill Rich Text Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                    var label = document.querySelector('.category-checkbox-label[data-slug="' + cb.value + '"]');
                    var chip = document.createElement('span');
                    chip.className = 'selected-tag inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-600 text-white';
                    chip.textContent = label ? label.getAttribute('data-label') : cb.value;
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
        if (!subTitlesContainer.querySelector('.sub-title-row') && addSubTitleBtn) {
            addSubTitleBtn.click();
        }
        addSubTitleBtn.addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'sub-title-row flex gap-2 items-center';
            row.innerHTML = '<input type="text" name="sub_titles[]" maxlength="255" placeholder="e.g. Flashlight" class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">' +
                '<button type="button" class="sub-title-remove px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">Remove</button>';
            subTitlesContainer.appendChild(row);
            row.querySelector('.sub-title-remove').addEventListener('click', function() { row.remove(); });
        });
        subTitlesContainer.querySelectorAll('.sub-title-remove').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.closest('.sub-title-row').remove(); });
        });

        // Class schedule rows
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
                div.className = 'schedule-row rounded-lg border border-slate-200 bg-slate-50 p-4 space-y-3';
                div.innerHTML = '<div class="flex justify-between items-center">' +
                    '<span class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800">' +
                    '<span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-700 text-xs text-white"><span class="schedule-num">0</span></span> Session</span>' +
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
