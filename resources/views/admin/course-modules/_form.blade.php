<div>
    <label class="block text-sm font-medium mb-1">Title *</label>
    <input type="text" name="title" value="{{ old('title', $courseModule->title ?? '') }}" required class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="block text-sm font-medium mb-1">Content</label>
    <textarea name="content" rows="8" class="w-full border rounded px-3 py-2">{{ old('content', $courseModule->content ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Video URL</label>
    <input type="url" name="video_url" value="{{ old('video_url', $courseModule->video_url ?? '') }}" class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="block text-sm font-medium mb-1">Order</label>
    <input type="number" name="order" value="{{ old('order', $courseModule->order ?? 0) }}" min="0" class="w-full border rounded px-3 py-2 max-w-xs">
</div>
<div><label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $courseModule->is_active ?? true) ? 'checked' : '' }}> Active</label></div>

<h3 class="font-bold text-lg pt-4 border-t">Quiz Questions (90% required to pass)</h3>
<div id="questions-container" class="space-y-4">
    @php $questions = old('questions', $courseModule?->quizQuestions?->toArray() ?? [['question'=>'','options'=>['',''],'correct_answer'=>'']]); @endphp
    @foreach($questions as $qi => $q)
        <div class="border rounded p-4 question-block">
            <label class="block text-sm font-medium mb-1">Question {{ $qi + 1 }}</label>
            <input type="text" name="questions[{{ $qi }}][question]" value="{{ $q['question'] ?? '' }}" class="w-full border rounded px-3 py-2 mb-2">
            @php $opts = $q['options'] ?? ['','']; if (is_string($opts)) { $opts = json_decode($opts, true) ?? ['','']; } @endphp
            @foreach($opts as $oi => $opt)
                <input type="text" name="questions[{{ $qi }}][options][{{ $oi }}]" value="{{ $opt }}" placeholder="Option {{ $oi + 1 }}" class="w-full border rounded px-3 py-2 mb-1">
            @endforeach
            <input type="text" name="questions[{{ $qi }}][correct_answer]" value="{{ $q['correct_answer'] ?? '' }}" placeholder="Correct answer (must match option text)" class="w-full border rounded px-3 py-2 mt-2">
        </div>
    @endforeach
</div>
<button type="button" onclick="addQuestion()" class="text-blue-600 text-sm">+ Add Question</button>

<script>
let qIndex = {{ count($questions) }};
function addQuestion() {
    const c = document.getElementById('questions-container');
    c.insertAdjacentHTML('beforeend', `<div class="border rounded p-4 question-block"><label class="block text-sm font-medium mb-1">Question ${qIndex+1}</label><input type="text" name="questions[${qIndex}][question]" class="w-full border rounded px-3 py-2 mb-2"><input type="text" name="questions[${qIndex}][options][0]" placeholder="Option 1" class="w-full border rounded px-3 py-2 mb-1"><input type="text" name="questions[${qIndex}][options][1]" placeholder="Option 2" class="w-full border rounded px-3 py-2 mb-1"><input type="text" name="questions[${qIndex}][correct_answer]" placeholder="Correct answer" class="w-full border rounded px-3 py-2 mt-2"></div>`);
    qIndex++;
}
</script>
