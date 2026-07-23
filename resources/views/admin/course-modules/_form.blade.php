@php
    $defaultQuestion = [
        'question' => '',
        'options' => ['', ''],
        'allow_multiple' => false,
        'correct_answer' => [],
    ];
    $questions = old('questions', $courseModule?->quizQuestions?->map(function ($q) {
        return [
            'question' => $q->question,
            'options' => $q->options ?? ['', ''],
            'allow_multiple' => (bool) $q->allow_multiple,
            'correct_answer' => $q->correctAnswers(),
        ];
    })->toArray() ?? [$defaultQuestion]);
    if (! is_array($questions) || count($questions) === 0) {
        $questions = [$defaultQuestion];
    }
@endphp

@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues before saving:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-start">
{{-- Module details --}}
<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm space-y-4 {{ $errors->has('title') || $errors->has('video_url') || $errors->has('quiz_time_limit_minutes') ? 'ring-1 ring-red-200' : '' }}">
    <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">1</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Module details</h3>
            <p class="text-sm text-gray-500">Only <strong>Title</strong> is required. Content, video, and quiz are optional.</p>
        </div>
    </div>

    <div>
        <label for="module_title" class="block text-sm font-bold text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
        <input type="text" id="module_title" name="title" value="{{ old('title', $courseModule->title ?? '') }}" required
               placeholder="e.g. Module 1 — Safety Basics"
               class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('title') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
        @error('title')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="module_content" class="block text-sm font-bold text-gray-700 mb-1.5">Content</label>
        <textarea id="module_content" name="content" rows="8"
                  placeholder="Lesson text, instructions, or reading material…"
                  class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('content', $courseModule->content ?? '') }}</textarea>
        @error('content')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="module_video" class="block text-sm font-bold text-gray-700 mb-1.5">Video URL <span class="font-normal text-gray-400">(optional)</span></label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-video text-sm"></i>
            </span>
            <input type="text" id="module_video" name="video_url" value="{{ old('video_url', $courseModule->video_url ?? '') }}"
                   placeholder="https://www.youtube.com/watch?v=… or Vimeo link"
                   class="w-full rounded-md border py-2.5 pl-9 pr-3 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('video_url') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
        </div>
        <p class="mt-1 text-xs text-gray-500">Use a YouTube or Vimeo link for in-page video. Other links open in a new tab (they won’t embed).</p>
        @error('video_url')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="module_order" class="block text-sm font-bold text-gray-700 mb-1.5">Order</label>
            <input type="number" id="module_order" name="order" value="{{ old('order', $courseModule->order ?? '') }}" min="0"
                   placeholder="Auto"
                   class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            <p class="mt-1 text-[11px] text-gray-500">Leave blank for next number.</p>
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1.5">Status</label>
            <input type="hidden" name="is_active" value="0">
            <label for="module_is_active" class="flex h-[42px] cursor-pointer items-center gap-2 rounded-md border border-gray-200 bg-white px-3 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                <input type="checkbox" id="module_is_active" name="is_active" value="1"
                       {{ (string) old('is_active', ($courseModule->is_active ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}
                       class="rounded border-gray-400 text-green-600 focus:ring-green-500">
                <span class="text-sm font-semibold text-gray-900">Active</span>
            </label>
        </div>
    </div>

    <div>
        <label for="quiz_time_limit_minutes" class="block text-sm font-bold text-gray-700 mb-1.5">
            Quiz time limit (minutes) <span class="text-red-500">*</span>
        </label>
        <input type="number" id="quiz_time_limit_minutes" name="quiz_time_limit_minutes" min="1" max="180"
               value="{{ old('quiz_time_limit_minutes', $courseModule->quiz_time_limit_minutes ?? 15) }}" required
               class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('quiz_time_limit_minutes') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
        <p class="mt-1 text-xs text-gray-500">Countdown starts when the student clicks Start Quiz. Unanswered questions are submitted when time runs out.</p>
        @error('quiz_time_limit_minutes')
            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="passing_score" class="block text-sm font-bold text-gray-700 mb-1.5">Passing score (%) <span class="text-red-500">*</span></label>
            <input type="number" id="passing_score" name="passing_score" min="1" max="100"
                   value="{{ old('passing_score', $courseModule->passing_score ?? 90) }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        </div>
        <div>
            <label for="max_attempts" class="block text-sm font-bold text-gray-700 mb-1.5">Max quiz attempts <span class="text-red-500">*</span></label>
            <input type="number" id="max_attempts" name="max_attempts" min="1" max="20"
                   value="{{ old('max_attempts', $courseModule->max_attempts ?? 1) }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        </div>
    </div>
    <p class="text-xs text-gray-500 -mt-2">After max attempts are used without passing, admin must reset the student to allow another try.</p>

    <div>
        <label for="materials_files" class="block text-sm font-bold text-gray-700 mb-1.5">Materials / PDFs <span class="font-normal text-gray-400">(optional)</span></label>
        @if(! empty($courseModule?->materials))
            <ul class="mb-2 space-y-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                @foreach($courseModule->materialFiles() as $file)
                    <li class="flex items-center gap-2">
                        <i class="fas fa-paperclip text-gray-400"></i>
                        <a href="{{ $file['url'] }}" target="_blank" class="font-medium text-blue-700 hover:underline">{{ $file['original_name'] }}</a>
                    </li>
                @endforeach
            </ul>
            <label class="mb-2 inline-flex items-center gap-2 text-sm text-red-700">
                <input type="checkbox" name="remove_materials" value="1" class="rounded border-gray-400 text-red-600 focus:ring-red-500">
                Remove all existing materials
            </label>
        @endif
        <input type="file" id="materials_files" name="materials_files[]" multiple
               accept=".pdf,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg"
               class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-green-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
        <p class="mt-1 text-xs text-gray-500">Upload up to 5 files (PDF/Office/images), max 10MB each. New uploads are added to existing files.</p>
    </div>
</div>

{{-- Quiz --}}
<div class="rounded-lg border bg-white p-5 shadow-sm space-y-4 {{ $errors->has('questions') || collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'questions.')) ? 'border-red-300 ring-1 ring-red-200' : 'border-blue-200' }}">
    <div class="flex items-start gap-3 border-b border-blue-100 pb-3">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Quiz questions <span class="text-sm font-normal text-gray-400">(optional)</span></h3>
            <p class="text-sm text-gray-500">Drag questions by the handle to reorder (like a kanban list). Enable multi-select when more than one option is correct.</p>
        </div>
    </div>

    @if(collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'questions')))
        <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
            <p class="font-semibold mb-1">Quiz needs fixing:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->getMessages() as $key => $messages)
                    @if(str_starts_with($key, 'questions'))
                        @foreach($messages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <div id="questions-container" class="space-y-3 max-h-[32rem] overflow-y-auto pr-1">
        @foreach($questions as $qi => $q)
            @php
                $opts = $q['options'] ?? ['', ''];
                if (is_string($opts)) {
                    $opts = json_decode($opts, true) ?? ['', ''];
                }
                $opts = array_values($opts);
                while (count($opts) < 2) {
                    $opts[] = '';
                }
                $correct = $q['correct_answer'] ?? [];
                if (! is_array($correct)) {
                    $correct = filled($correct) ? [(string) $correct] : [];
                }
                $correct = array_map('strval', $correct);
                $allowMultiple = (bool) ($q['allow_multiple'] ?? false);
            @endphp
            <div class="question-block rounded-lg border border-slate-200 bg-slate-50/80 p-3 space-y-2.5" data-q-index="{{ $qi }}">
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800">
                        <button type="button" class="question-drag-handle cursor-grab active:cursor-grabbing rounded px-1.5 py-1 text-slate-400 hover:bg-slate-200 hover:text-slate-700" title="Drag to reorder" aria-label="Drag to reorder">
                            <i class="fas fa-grip-vertical"></i>
                        </button>
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-700 text-xs text-white question-num">{{ $qi + 1 }}</span>
                        Question
                    </span>
                    <button type="button" class="question-remove text-xs font-medium text-red-600 hover:text-red-800 {{ count($questions) <= 1 ? 'invisible' : '' }}">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>

                <input type="text" name="questions[{{ $qi }}][question]" value="{{ $q['question'] ?? '' }}"
                       placeholder="Type the question… (or leave empty to skip)"
                       class="w-full rounded-md border bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has("questions.$qi.question") ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}">

                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="questions[{{ $qi }}][allow_multiple]" value="1"
                           class="allow-multiple-toggle rounded border-gray-400 text-blue-600 focus:ring-blue-500"
                           {{ $allowMultiple ? 'checked' : '' }}>
                    Allow multiple correct answers
                </label>

                <div class="correct-answers-container"></div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="options-hint text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            {{ $allowMultiple ? 'Options — check all correct answers' : 'Options — pick the correct one' }}
                        </p>
                        <button type="button" class="add-option-btn text-xs font-medium text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus mr-0.5"></i> Add option
                        </button>
                    </div>
                    <div class="options-grid grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach($opts as $oi => $opt)
                            @php $isCorrect = in_array(trim((string) $opt), array_map('trim', $correct), true) && $opt !== ''; @endphp
                            <label class="option-row flex cursor-pointer items-center gap-2 rounded-md border border-gray-200 bg-white px-2.5 py-2 transition-colors hover:border-blue-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="{{ $allowMultiple ? 'checkbox' : 'radio' }}"
                                       name="questions_correct_{{ $qi }}{{ $allowMultiple ? '[]' : '' }}"
                                       value="{{ $oi }}"
                                       class="correct-picker shrink-0 border-gray-400 text-green-600 focus:ring-green-500"
                                       {{ $isCorrect ? 'checked' : '' }}
                                       @if(! $allowMultiple && $oi === 0 && $correct === []) checked @endif>
                                <span class="option-letter flex h-6 w-6 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-600">{{ chr(65 + $oi) }}</span>
                                <input type="text"
                                       name="questions[{{ $qi }}][options][{{ $oi }}]"
                                       value="{{ $opt }}"
                                       placeholder="Option {{ $oi + 1 }}"
                                       class="option-text min-w-0 flex-1 border-0 bg-transparent p-0 text-sm focus:outline-none focus:ring-0">
                                <button type="button" class="option-remove shrink-0 text-gray-300 hover:text-red-500 {{ count($opts) <= 2 ? 'invisible' : '' }}" title="Remove option">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-question-btn"
            class="inline-flex w-full items-center justify-center gap-1.5 rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 sm:w-auto">
        <i class="fas fa-plus"></i> Add Question
    </button>
</div>
</div>{{-- end two-column grid --}}

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
(function () {
    var container = document.getElementById('questions-container');
    var addBtn = document.getElementById('add-question-btn');
    if (!container || !addBtn) return;

    var qIndex = {{ max(count($questions), 1) }};
    var MAX_OPTIONS = 6;

    function letter(i) {
        return String.fromCharCode(65 + i);
    }

    function getQKey(block) {
        var questionInput = block.querySelector('input[name*="[question]"]');
        if (!questionInput || !questionInput.name) return block.getAttribute('data-q-index') || '0';
        var m = questionInput.name.match(/questions\[(\d+)\]/);
        return m ? m[1] : (block.getAttribute('data-q-index') || '0');
    }

    function isMulti(block) {
        var toggle = block.querySelector('.allow-multiple-toggle');
        return !!(toggle && toggle.checked);
    }

    function syncCorrectAnswers(block) {
        var wrap = block.querySelector('.correct-answers-container');
        if (!wrap) return;
        wrap.innerHTML = '';
        var qKey = getQKey(block);
        block.querySelectorAll('.option-row').forEach(function (row) {
            var picker = row.querySelector('.correct-picker');
            var textInput = row.querySelector('.option-text');
            if (!picker || !picker.checked || !textInput) return;
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'questions[' + qKey + '][correct_answer][]';
            hidden.value = textInput.value.trim();
            wrap.appendChild(hidden);
        });
    }

    function renumberOptions(block) {
        var rows = block.querySelectorAll('.option-row');
        var qKey = getQKey(block);
        var multi = isMulti(block);
        var hint = block.querySelector('.options-hint');
        if (hint) {
            hint.textContent = multi ? 'Options — check all correct answers' : 'Options — pick the correct one';
        }

        rows.forEach(function (row, oi) {
            var picker = row.querySelector('.correct-picker');
            var letterEl = row.querySelector('.option-letter');
            var text = row.querySelector('.option-text');
            var removeBtn = row.querySelector('.option-remove');
            if (picker) {
                picker.type = multi ? 'checkbox' : 'radio';
                picker.name = multi ? ('questions_correct_' + qKey + '[]') : ('questions_correct_' + qKey);
                picker.value = String(oi);
            }
            if (letterEl) letterEl.textContent = letter(oi);
            if (text) {
                text.name = 'questions[' + qKey + '][options][' + oi + ']';
                text.placeholder = 'Option ' + (oi + 1);
            }
            if (removeBtn) {
                removeBtn.classList.toggle('invisible', rows.length <= 2);
            }
        });

        var addOpt = block.querySelector('.add-option-btn');
        if (addOpt) {
            addOpt.classList.toggle('invisible', rows.length >= MAX_OPTIONS);
            addOpt.disabled = rows.length >= MAX_OPTIONS;
        }

        if (!multi) {
            var checked = block.querySelectorAll('.correct-picker:checked');
            if (checked.length === 0) {
                var first = block.querySelector('.correct-picker');
                if (first) first.checked = true;
            } else if (checked.length > 1) {
                checked.forEach(function (el, idx) { if (idx > 0) el.checked = false; });
            }
        }

        syncCorrectAnswers(block);
    }

    function bindOptionEvents(block, row) {
        var text = row.querySelector('.option-text');
        var picker = row.querySelector('.correct-picker');
        var removeBtn = row.querySelector('.option-remove');
        if (text) {
            text.addEventListener('input', function () { syncCorrectAnswers(block); });
            text.addEventListener('click', function (e) { e.stopPropagation(); });
        }
        if (picker) {
            picker.addEventListener('change', function () { syncCorrectAnswers(block); });
        }
        if (removeBtn) {
            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var rows = block.querySelectorAll('.option-row');
                if (rows.length <= 2) return;
                row.remove();
                renumberOptions(block);
            });
        }
    }

    function createOptionRow(qKey, oi, checked, multi) {
        var label = document.createElement('label');
        label.className = 'option-row flex cursor-pointer items-center gap-2 rounded-md border border-gray-200 bg-white px-2.5 py-2 transition-colors hover:border-blue-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50';
        label.innerHTML =
            '<input type="' + (multi ? 'checkbox' : 'radio') + '" name="questions_correct_' + qKey + (multi ? '[]' : '') + '" value="' + oi + '" class="correct-picker shrink-0 border-gray-400 text-green-600 focus:ring-green-500"' + (checked ? ' checked' : '') + '>' +
            '<span class="option-letter flex h-6 w-6 shrink-0 items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-600">' + letter(oi) + '</span>' +
            '<input type="text" name="questions[' + qKey + '][options][' + oi + ']" placeholder="Option ' + (oi + 1) + '" class="option-text min-w-0 flex-1 border-0 bg-transparent p-0 text-sm focus:outline-none focus:ring-0">' +
            '<button type="button" class="option-remove shrink-0 text-gray-300 hover:text-red-500" title="Remove option"><i class="fas fa-times text-xs"></i></button>';
        return label;
    }

    function bindAddOption(block) {
        var btn = block.querySelector('.add-option-btn');
        if (!btn || btn.dataset.bound) return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', function () {
            var grid = block.querySelector('.options-grid');
            var rows = block.querySelectorAll('.option-row');
            if (!grid || rows.length >= MAX_OPTIONS) return;
            var qKey = getQKey(block);
            var oi = rows.length;
            var row = createOptionRow(qKey, oi, false, isMulti(block));
            grid.appendChild(row);
            bindOptionEvents(block, row);
            renumberOptions(block);
        });
    }

    function bindMultiToggle(block) {
        var toggle = block.querySelector('.allow-multiple-toggle');
        if (!toggle || toggle.dataset.bound) return;
        toggle.dataset.bound = '1';
        toggle.addEventListener('change', function () {
            renumberOptions(block);
        });
    }

    function syncAllInBlock(block) {
        block.querySelectorAll('.option-row').forEach(function (row) {
            bindOptionEvents(block, row);
        });
        bindAddOption(block);
        bindMultiToggle(block);
        renumberOptions(block);
    }

    function renumber() {
        var blocks = container.querySelectorAll('.question-block');
        blocks.forEach(function (block, i) {
            var num = block.querySelector('.question-num');
            if (num) num.textContent = String(i + 1);
            var removeBtn = block.querySelector('.question-remove');
            if (removeBtn) {
                removeBtn.classList.toggle('invisible', blocks.length <= 1);
            }
        });
    }

    /** Keep POST field indices in visual order after drag / remove. */
    function reindexQuestions() {
        container.querySelectorAll('.question-block').forEach(function (block, i) {
            block.setAttribute('data-q-index', String(i));
            var qInput = block.querySelector('input[name*="[question]"]');
            if (qInput) {
                qInput.name = 'questions[' + i + '][question]';
            }
            var multi = block.querySelector('.allow-multiple-toggle');
            if (multi) {
                multi.name = 'questions[' + i + '][allow_multiple]';
            }
            renumberOptions(block);
        });
        renumber();
    }

    function bindRemove(block) {
        var btn = block.querySelector('.question-remove');
        if (!btn || btn.dataset.bound) return;
        btn.dataset.bound = '1';
        btn.addEventListener('click', function () {
            if (container.querySelectorAll('.question-block').length <= 1) return;
            block.remove();
            reindexQuestions();
        });
    }

    container.querySelectorAll('.question-block').forEach(function (block) {
        bindRemove(block);
        syncAllInBlock(block);
    });

    if (typeof Sortable !== 'undefined') {
        Sortable.create(container, {
            handle: '.question-drag-handle',
            animation: 180,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2',
            dragClass: 'shadow-lg',
            onEnd: function () {
                reindexQuestions();
            }
        });
    }

    var form = container.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            reindexQuestions();
            container.querySelectorAll('.question-block').forEach(syncCorrectAnswers);
        });
    }

    addBtn.addEventListener('click', function () {
        var i = qIndex++;
        var div = document.createElement('div');
        div.className = 'question-block rounded-lg border border-slate-200 bg-slate-50/80 p-3 space-y-2.5';
        div.setAttribute('data-q-index', String(i));
        div.innerHTML =
            '<div class="flex items-center justify-between gap-2">' +
            '<span class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800">' +
            '<button type="button" class="question-drag-handle cursor-grab active:cursor-grabbing rounded px-1.5 py-1 text-slate-400 hover:bg-slate-200 hover:text-slate-700" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-vertical"></i></button>' +
            '<span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-700 text-xs text-white question-num">0</span> Question</span>' +
            '<button type="button" class="question-remove text-xs font-medium text-red-600 hover:text-red-800"><i class="fas fa-trash mr-1"></i> Remove</button></div>' +
            '<input type="text" name="questions[' + i + '][question]" placeholder="Type the question…" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">' +
            '<label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="questions[' + i + '][allow_multiple]" value="1" class="allow-multiple-toggle rounded border-gray-400 text-blue-600 focus:ring-blue-500"> Allow multiple correct answers</label>' +
            '<div class="correct-answers-container"></div>' +
            '<div class="space-y-1.5">' +
            '<div class="flex items-center justify-between gap-2">' +
            '<p class="options-hint text-[11px] font-semibold uppercase tracking-wide text-gray-500">Options — pick the correct one</p>' +
            '<button type="button" class="add-option-btn text-xs font-medium text-blue-600 hover:text-blue-800"><i class="fas fa-plus mr-0.5"></i> Add option</button></div>' +
            '<div class="options-grid grid grid-cols-1 gap-2 sm:grid-cols-2"></div></div>';
        container.appendChild(div);
        var grid = div.querySelector('.options-grid');
        grid.appendChild(createOptionRow(String(i), 0, true, false));
        grid.appendChild(createOptionRow(String(i), 1, false, false));
        bindRemove(div);
        syncAllInBlock(div);
        reindexQuestions();
    });
})();
</script>
