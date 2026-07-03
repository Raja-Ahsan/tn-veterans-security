@extends('student.layouts.master')

@section('title', $courseModule->title)

@section('content')
<div class="mb-4"><a href="{{ route('student.online-course.index', $service) }}" class="text-blue-600 hover:underline">← Back to modules</a></div>
<h1 class="text-2xl font-bold mb-4">{{ $courseModule->title }}</h1>

@if($courseModule->video_url)
    <div class="mb-6 aspect-video bg-black rounded-lg overflow-hidden">
        <iframe src="{{ $courseModule->video_url }}" class="w-full h-full" allowfullscreen title="Module video"></iframe>
    </div>
@endif

@if($courseModule->content)
    <div class="prose max-w-none bg-white rounded-lg shadow p-6 mb-6">{!! nl2br(e($courseModule->content)) !!}</div>
@endif

@if($moduleProgress?->is_completed)
    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg mb-6">You passed this module with {{ $moduleProgress->best_score }}%.</div>
@elseif($courseModule->quizQuestions->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Module Quiz (90% required)</h2>
        <form method="POST" action="{{ route('student.online-course.quiz', [$service, $courseModule]) }}">
            @csrf
            @foreach($courseModule->quizQuestions as $question)
                <div class="mb-6">
                    <p class="font-semibold mb-2">{{ $loop->iteration }}. {{ $question->question }}</p>
                    @foreach($question->options as $option)
                        <label class="flex items-center gap-2 mb-2">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required>
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            @endforeach
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold">Submit Quiz</button>
        </form>
    </div>
@endif
@endsection
