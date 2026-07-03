<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function index(Service $service)
    {
        $modules = $service->courseModules()->withCount('quizQuestions')->orderBy('order')->get();

        return view('admin.course-modules.index', compact('service', 'modules'));
    }

    public function create(Service $service)
    {
        return view('admin.course-modules.create', compact('service'));
    }

    public function store(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.correct_answer' => 'required_with:questions|string',
        ]);

        $module = $service->courseModules()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'] ?? ($service->courseModules()->max('order') + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncQuestions($module, $request->input('questions', []));

        return redirect()->route('admin.services.course-modules.index', $service)
            ->with('success', 'Module created.');
    }

    public function edit(Service $service, CourseModule $courseModule)
    {
        $courseModule->load('quizQuestions');

        return view('admin.course-modules.edit', compact('service', 'courseModule'));
    }

    public function update(Request $request, Service $service, CourseModule $courseModule)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.correct_answer' => 'required_with:questions|string',
        ]);

        $courseModule->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'] ?? $courseModule->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $courseModule->quizQuestions()->delete();
        $this->syncQuestions($courseModule, $request->input('questions', []));

        return redirect()->route('admin.services.course-modules.index', $service)
            ->with('success', 'Module updated.');
    }

    public function destroy(Service $service, CourseModule $courseModule)
    {
        $courseModule->delete();

        return redirect()->route('admin.services.course-modules.index', $service)
            ->with('success', 'Module deleted.');
    }

    public function reorder(Request $request, Service $service)
    {
        if ($request->has('positions')) {
            $validated = $request->validate([
                'positions' => 'required|array',
                'positions.*' => 'integer|min:1',
            ]);

            foreach ($validated['positions'] as $moduleId => $position) {
                CourseModule::query()
                    ->where('service_id', $service->id)
                    ->where('id', $moduleId)
                    ->update(['order' => $position]);
            }

            return back()->with('success', 'Module order updated.');
        }

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:course_modules,id',
        ]);

        foreach ($validated['order'] as $index => $moduleId) {
            CourseModule::query()
                ->where('service_id', $service->id)
                ->where('id', $moduleId)
                ->update(['order' => $index + 1]);
        }

        return back()->with('success', 'Module order updated.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function syncQuestions(CourseModule $module, array $questions): void
    {
        foreach ($questions as $index => $q) {
            if (empty($q['question'])) {
                continue;
            }

            ModuleQuizQuestion::create([
                'course_module_id' => $module->id,
                'question' => $q['question'],
                'options' => array_values($q['options'] ?? []),
                'correct_answer' => $q['correct_answer'],
                'order' => $index,
            ]);
        }
    }
}
