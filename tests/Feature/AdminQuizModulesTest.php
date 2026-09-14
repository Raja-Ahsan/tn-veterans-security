<?php

namespace Tests\Feature;

use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\Service;
use App\Models\User;
use App\Support\QuizQuestionPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuizModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_classes_index_shows_modules_only_for_blended_classes(): void
    {
        $admin = User::factory()->create();
        $blended = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 0,
        ]);
        $inPerson = Service::query()->create([
            'title' => 'First AID CPR AED',
            'is_active' => true,
            'has_online_parts' => false,
            'order' => 1,
        ]);

        $html = $this->actingAs($admin)
            ->get(route('admin.classes.index'))
            ->assertOk()
            ->assertSee('Modules')
            ->assertSee('Build module')
            ->assertSee('In-person test')
            ->getContent();

        $this->assertStringContainsString(route('admin.classes.course-modules.index', $blended), $html);
        $this->assertStringNotContainsString(route('admin.classes.course-modules.index', $inPerson), $html);
    }

    public function test_quiz_modules_hub_lists_only_blended_classes_by_default(): void
    {
        $admin = User::factory()->create();
        Service::query()->create([
            'title' => 'First AID CPR AED',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Online Firearms Safety',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => false,
            'order' => 1,
        ]);
        Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index'))
            ->assertOk()
            ->assertSee('Quiz Modules')
            ->assertSee('Handgun Carry Permit')
            ->assertSee('Add module')
            ->assertDontSee('First AID CPR AED')
            ->assertDontSee('Online Firearms Safety');
    }

    public function test_quiz_modules_tabs_filter_by_delivery_and_show_counts(): void
    {
        $admin = User::factory()->create();
        Service::query()->create([
            'title' => 'In Person First Aid',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Online Firearms Safety',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => false,
            'order' => 1,
        ]);
        Service::query()->create([
            'title' => 'Blended Handgun Carry',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index', ['delivery' => 'online']))
            ->assertOk()
            ->assertSee('Online Firearms Safety')
            ->assertSee('Add module')
            ->assertDontSee('Blended Handgun Carry')
            ->assertDontSee('In Person First Aid');

        $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertSee('In Person First Aid')
            ->assertSee('No online quiz')
            ->assertSee('Edit class')
            ->assertDontSee('Online Firearms Safety')
            ->assertDontSee('Blended Handgun Carry');

        $html = $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index', ['delivery' => 'blended']))
            ->assertOk()
            ->assertSee('Blended Handgun Carry')
            ->getContent();

        $this->assertStringContainsString(route('admin.quiz-modules.index', ['delivery' => 'online']), $html);
        $this->assertStringContainsString(route('admin.quiz-modules.index', ['delivery' => 'blended']), $html);
        $this->assertStringContainsString(route('admin.quiz-modules.index', ['delivery' => 'in-person']), $html);
    }

    public function test_in_person_class_cannot_open_module_builder(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'First AID CPR AED',
            'is_active' => true,
            'has_online_parts' => false,
            'order' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.index', $service))
            ->assertNotFound();
    }

    public function test_manage_modules_page_loads_when_a_class_has_multiple_modules(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 0,
        ]);

        CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 1',
            'order' => 1,
            'is_active' => true,
        ]);
        CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 2',
            'order' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.index', $service))
            ->assertOk()
            ->assertSee('Online Modules')
            ->assertSee('Module 1')
            ->assertSee('Module 2')
            ->assertSee(route('admin.classes.course-modules.reorder', $service), false)
            ->assertSee(route('admin.classes.course-modules.create', $service), false);
    }

    public function test_admin_can_reorder_modules(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 0,
        ]);

        $first = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 1',
            'order' => 1,
            'is_active' => true,
        ]);
        $second = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 2',
            'order' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.classes.course-modules.reorder', $service), [
                'positions' => [
                    $first->id => 2,
                    $second->id => 1,
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(2, $first->fresh()->order);
        $this->assertSame(1, $second->fresh()->order);
    }

    public function test_creating_a_module_on_an_in_person_class_is_forbidden(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'BLS Renewal',
            'is_active' => true,
            'has_online_parts' => false,
            'order' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.classes.course-modules.store', $service), [
                'title' => 'Chapter 1',
                'quiz_time_limit_minutes' => 15,
                'passing_score' => 90,
                'max_attempts' => 1,
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('course_modules', 0);
        $this->assertFalse((bool) $service->fresh()->has_online_parts);
    }

    public function test_creating_a_blended_module_without_a_quiz_succeeds(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'BLS Renewal',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.classes.course-modules.store', $service), [
                'title' => 'Chapter 1',
                'quiz_time_limit_minutes' => 15,
                'passing_score' => 90,
                'max_attempts' => 1,
                'questions' => [
                    [
                        'question' => '',
                        'options' => ['', ''],
                        'correct_answer' => [''],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('course_modules', [
            'service_id' => $service->id,
            'title' => 'Chapter 1',
        ]);
        $this->assertDatabaseCount('module_quiz_questions', 0);
    }

    public function test_video_update_skips_incomplete_optional_quiz(): void
    {
        $admin = User::factory()->create();
        [$service, $module, $video] = $this->seedVideo();

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.videos.update', [$service, $module, $video]), [
                'title' => 'Updated video title',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'questions' => [
                    [
                        'question' => 'What is this?',
                        'options' => ['', ''],
                        'correct_answer' => [],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));

        $this->assertSame('Updated video title', $video->fresh()->title);
        $this->assertDatabaseCount('module_quiz_questions', 0);
    }

    public function test_video_update_saves_a_complete_quiz_question(): void
    {
        $admin = User::factory()->create();
        [$service, $module, $video] = $this->seedVideo();

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.videos.update', [$service, $module, $video]), [
                'title' => 'Video 1',
                'questions' => [
                    [
                        'question' => 'When should you call 911?',
                        'options' => ['Immediately', 'Never'],
                        'correct_answer' => ['Immediately'],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));

        $this->assertDatabaseHas('module_quiz_questions', [
            'course_module_id' => $module->id,
            'course_module_video_id' => $video->id,
            'question' => 'When should you call 911?',
        ]);
    }

    public function test_quiz_payload_drops_incomplete_rows_and_defaults_correct_answer(): void
    {
        $this->assertSame([], QuizQuestionPayload::normalize([
            [
                'question' => '',
                'options' => ['', ''],
                'correct_answer' => [''],
            ],
            [
                'question' => 'Incomplete options',
                'options' => ['Only one'],
                'correct_answer' => ['Only one'],
            ],
        ]));

        $normalized = QuizQuestionPayload::normalize([
            [
                'question' => 'Pick one',
                'options' => ['A', 'B'],
                'correct_answer' => [],
            ],
        ]);

        $this->assertSame(['A'], $normalized[0]['correct_answer']);
    }

    /**
     * @return array{0: Service, 1: CourseModule, 2: CourseModuleVideo}
     */
    private function seedVideo(): array
    {
        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 0,
        ]);

        $module = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 1',
            'order' => 1,
            'is_active' => true,
            'quiz_time_limit_minutes' => 15,
            'passing_score' => 90,
            'max_attempts' => 1,
        ]);

        $video = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 1',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order' => 1,
        ]);

        return [$service, $module, $video];
    }
}
