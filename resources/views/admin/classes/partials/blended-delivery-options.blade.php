{{--
  Blended / online delivery options.
  Pass $service when editing (enables module links). Omit on create.
--}}
@php
    $service = $service ?? null;
    $hasOnline = (bool) old('has_online_parts', $service?->has_online_parts ?? false);
    $testingInPerson = (bool) old('testing_in_person', $service?->testing_in_person ?? true);
@endphp

<div class="mb-6 space-y-3">
    <p class="text-sm font-bold text-gray-800">Delivery options</p>
    <p class="text-xs text-gray-500 -mt-1">Choose how students complete this class (in-person only, or blended with online quizzes).</p>

    {{-- Online / blended --}}
    <label for="has_online_parts" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
        <input type="checkbox"
               id="has_online_parts"
               name="has_online_parts"
               value="1"
               {{ $hasOnline ? 'checked' : '' }}
               class="mt-1 rounded border-gray-400 text-blue-600 focus:ring-blue-500"
               data-blended-toggle>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">Has online parts / quizzes</span>
                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Blended course</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">Students complete online modules &amp; quizzes before (or with) the in-person portion.</p>
        </div>
        <i class="fas fa-laptop text-blue-400 mt-1"></i>
    </label>

    {{-- Quiz / modules panel (shown when online parts checked) --}}
    <div id="online-parts-panel"
         class="rounded-lg border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-4 {{ $hasOnline ? '' : 'hidden' }}"
         @if(!$hasOnline) hidden @endif>
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="text-sm font-bold text-gray-900">Online Modules &amp; Quizzes</h4>
                <p class="mt-1 text-xs text-gray-600">
                    Blended courses need modules with quizzes (typically 90% pass). Manage content and track student progress from here.
                </p>

                @if($service)
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('admin.classes.course-modules.index', $service) }}"
                           class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="fas fa-book-open"></i>
                            Manage Modules &amp; Quizzes
                        </a>
                        <a href="{{ route('admin.classes.blended-progress', $service) }}"
                           class="inline-flex items-center gap-2 rounded-md border border-blue-200 bg-white px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">
                            <i class="fas fa-user-graduate"></i>
                            Student Progress &amp; Tests
                        </a>
                        <a href="{{ route('admin.classes.course-modules.create', $service) }}"
                           class="inline-flex items-center gap-2 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-800 hover:bg-green-100">
                            <i class="fas fa-plus"></i>
                            Add Module
                        </a>
                    </div>
                @else
                    <div class="mt-3 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                        <i class="fas fa-info-circle mr-1 text-amber-600"></i>
                        <strong>Save this class first</strong> — after you create it, you’ll go straight to <em>Modules &amp; Quizzes</em> to add online content.
                    </div>
                    <ul class="mt-3 space-y-1.5 text-xs text-gray-600">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 w-4"></i> Add modules (lessons / reading)</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 w-4"></i> Attach quiz questions per module</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 w-4"></i> Track student progress &amp; in-person tests</li>
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- In-person testing --}}
    <label for="testing_in_person" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
        <input type="checkbox"
               id="testing_in_person"
               name="testing_in_person"
               value="1"
               {{ $testingInPerson ? 'checked' : '' }}
               class="mt-1 rounded border-gray-400 text-green-600 focus:ring-green-500">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">Testing is in-person</span>
                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Recommended</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">Final / practical testing happens on site with an instructor (usually kept on).</p>
        </div>
        <i class="fas fa-user-check text-green-500 mt-1"></i>
    </label>
</div>

<script>
(function () {
    var toggle = document.querySelector('[data-blended-toggle]');
    var panel = document.getElementById('online-parts-panel');
    if (!toggle || !panel) return;

    function syncPanel() {
        if (toggle.checked) {
            panel.classList.remove('hidden');
            panel.removeAttribute('hidden');
        } else {
            panel.classList.add('hidden');
            panel.setAttribute('hidden', 'hidden');
        }
    }

    toggle.addEventListener('change', syncPanel);
    syncPanel();
})();
</script>
