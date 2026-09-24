<?php

namespace Tests\Feature;

use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Models\User;
use App\Support\QuizQuestionPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            'title' => 'Blended Firearms Safety',
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
            ->assertSee('Course Modules')
            ->assertSee('Handgun Carry Permit')
            ->assertSee('Blended Firearms Safety')
            ->assertSee('Add module')
            ->assertDontSee('First AID CPR AED');
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
            'title' => 'Blended Firearms Safety',
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
            ->assertSee('Blended Firearms Safety')
            ->assertSee('Blended Handgun Carry')
            ->assertDontSee('In Person First Aid');

        $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertSee('In Person First Aid')
            ->assertSee('No online quiz')
            ->assertSee('Edit class')
            ->assertDontSee('Blended Firearms Safety')
            ->assertDontSee('Blended Handgun Carry');

        $html = $this->actingAs($admin)
            ->get(route('admin.quiz-modules.index', ['delivery' => 'blended']))
            ->assertOk()
            ->assertSee('Blended Handgun Carry')
            ->assertSee('Blended Firearms Safety')
            ->assertDontSee('>Online<', false)
            ->getContent();

        $this->assertStringNotContainsString(route('admin.quiz-modules.index', ['delivery' => 'online']), $html);
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
            ->assertRedirect(route('admin.classes.edit', $service))
            ->assertSessionHas('error');
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

    public function test_admin_can_reorder_videos_within_a_module(): void
    {
        $admin = User::factory()->create();
        [$service, $module] = $this->seedVideo();

        $first = CourseModuleVideo::query()->where('course_module_id', $module->id)->firstOrFail();
        $second = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 2',
            'order' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.edit', [$service, $module]))
            ->assertOk()
            ->assertSee(route('admin.classes.course-modules.videos.reorder', [$service, $module]), false)
            ->assertSee('video-drag-handle', false)
            ->assertSee('Drag the');

        $this->actingAs($admin)
            ->postJson(route('admin.classes.course-modules.videos.reorder', [$service, $module]), [
                'order' => [$second->id, $first->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(2, $first->fresh()->order);
        $this->assertSame(1, $second->fresh()->order);

        $this->actingAs($admin)
            ->post(route('admin.classes.course-modules.videos.reorder', [$service, $module]), [
                'order' => [$first->id, $second->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, $first->fresh()->order);
        $this->assertSame(2, $second->fresh()->order);
    }

    public function test_updating_multi_video_module_does_not_wipe_video_quizzes_after_reorder(): void
    {
        $admin = User::factory()->create();
        [$service, $module, $video1] = $this->seedVideo();

        $video2 = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 2',
            'order' => 2,
        ]);

        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module->id,
            'course_module_video_id' => $video1->id,
            'question' => 'Video 1 Q1',
            'options' => ['A', 'B'],
            'correct_answer' => ['A'],
            'order' => 0,
        ]);
        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module->id,
            'course_module_video_id' => $video1->id,
            'question' => 'Video 1 Q2',
            'options' => ['C', 'D'],
            'correct_answer' => ['C'],
            'order' => 1,
        ]);
        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module->id,
            'course_module_video_id' => $video2->id,
            'question' => 'Video 2 Q1',
            'options' => ['E', 'F'],
            'correct_answer' => ['E'],
            'order' => 0,
        ]);
        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module->id,
            'course_module_video_id' => $video2->id,
            'question' => 'Video 2 Q2',
            'options' => ['G', 'H'],
            'correct_answer' => ['G'],
            'order' => 1,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.classes.course-modules.videos.reorder', [$service, $module]), [
                'order' => [$video2->id, $video1->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.update', [$service, $module]), [
                'title' => $module->title,
                'quiz_time_limit_minutes' => 15,
                'passing_score' => 90,
                'max_attempts' => 1,
                'is_active' => 1,
                'questions' => [
                    [
                        'question' => 'Should not overwrite',
                        'options' => ['X', 'Y'],
                        'correct_answer' => ['X'],
                    ],
                ],
            ])
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));

        $this->assertSame(2, $video1->fresh()->quizQuestions()->count());
        $this->assertSame(2, $video2->fresh()->quizQuestions()->count());
        $this->assertDatabaseHas('module_quiz_questions', [
            'course_module_video_id' => $video1->id,
            'question' => 'Video 1 Q1',
        ]);
        $this->assertDatabaseHas('module_quiz_questions', [
            'course_module_video_id' => $video2->id,
            'question' => 'Video 2 Q2',
        ]);
        $this->assertDatabaseMissing('module_quiz_questions', [
            'course_module_id' => $module->id,
            'question' => 'Should not overwrite',
        ]);
    }

    public function test_video_upload_rejects_files_larger_than_configured_limit(): void
    {
        $admin = User::factory()->create();
        [$service, $module, $video] = $this->seedVideo();

        $oversized = UploadedFile::fake()->create(
            'too-big.mp4',
            ((int) config('filesystems.course_video_max_kb')) + 1,
            'video/mp4'
        );

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.videos.update', [$service, $module, $video]), [
                'title' => 'Video 1',
                'video_source' => 'upload',
                'video_file' => $oversized,
            ])
            ->assertSessionHasErrors('video_file');

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.update', [$service, $module]), [
                'title' => $module->title,
                'quiz_time_limit_minutes' => 15,
                'passing_score' => 90,
                'max_attempts' => 1,
                'is_active' => 1,
                'video_source' => 'upload',
                'video_file' => $oversized,
            ])
            ->assertSessionHasErrors('video_file');
    }

    public function test_video_source_cannot_be_both_upload_and_url(): void
    {
        $admin = User::factory()->create();
        [$service, $module, $video] = $this->seedVideo();

        $file = UploadedFile::fake()->create('lesson.mp4', 100, 'video/mp4');

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.videos.update', [$service, $module, $video]), [
                'title' => 'Video 1',
                'video_source' => 'upload',
                'video_file' => $file,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));

        $video->refresh();
        $this->assertNotNull($video->video_path);
        $this->assertNull($video->video_url);

        $this->actingAs($admin)
            ->put(route('admin.classes.course-modules.videos.update', [$service, $module, $video]), [
                'title' => 'Video 1',
                'video_source' => 'url',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_file' => UploadedFile::fake()->create('ignored.mp4', 100, 'video/mp4'),
            ])
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));

        $video->refresh();
        $this->assertNull($video->video_path);
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $video->video_url);
    }

    public function test_edit_form_shows_exclusive_video_source_controls(): void
    {
        $admin = User::factory()->create();
        [$service, $module] = $this->seedVideo();

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.edit', [$service, $module]))
            ->assertOk()
            ->assertSee('Video source', false)
            ->assertSee('name="video_source"', false)
            ->assertSee('value="upload"', false)
            ->assertSee('value="url"', false)
            ->assertSee('Max 5MB', false);
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
            ->assertRedirect(route('admin.classes.edit', $service))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('course_modules', 0);
        $this->assertFalse((bool) $service->fresh()->has_online_parts);
    }

    public function test_module_show_redirects_to_edit(): void
    {
        $admin = User::factory()->create();
        [$service, $module] = $this->seedVideo();

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.show', [$service, $module]))
            ->assertRedirect(route('admin.classes.course-modules.edit', [$service, $module]));
    }

    public function test_module_from_another_class_returns_not_found(): void
    {
        $admin = User::factory()->create();
        [$service, $module] = $this->seedVideo();
        $other = Service::query()->create([
            'title' => 'Other Blended Class',
            'is_active' => true,
            'has_online_parts' => true,
            'order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.classes.course-modules.edit', [$other, $module]))
            ->assertNotFound();
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
