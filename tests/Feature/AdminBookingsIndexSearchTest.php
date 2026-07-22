<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingsIndexSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_json_search_filters_bookings_by_student_name(): void
    {
        $user = User::factory()->create();

        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
        ]);

        $matchStudent = Student::query()->create([
            'name' => 'Unique Search Student',
            'email' => 'unique.search@example.com',
            'password' => 'password',
        ]);

        $otherStudent = Student::query()->create([
            'name' => 'Other Person',
            'email' => 'other@example.com',
            'password' => 'password',
        ]);

        ServiceBooking::query()->create([
            'student_id' => $matchStudent->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
            'payment_status' => 'deposit_paid',
            'booking_type' => 'group',
            'number_of_students' => 1,
            'total_amount' => 150,
        ]);

        ServiceBooking::query()->create([
            'student_id' => $otherStudent->id,
            'service_id' => $service->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_type' => 'group',
            'number_of_students' => 1,
            'total_amount' => 200,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.bookings.index', ['q' => 'Unique Search']));

        $response->assertOk();
        $response->assertJsonStructure(['html', 'pagination', 'total']);
        $this->assertSame(1, $response->json('total'));

        $html = $response->json('html');
        $this->assertStringContainsString('Unique Search Student', $html);
        $this->assertStringNotContainsString('Other Person', $html);
    }

    public function test_authenticated_json_search_filters_bookings_by_class_title(): void
    {
        $user = User::factory()->create();

        $matchService = Service::query()->create([
            'title' => 'Blended Security Training Demo',
            'is_active' => true,
        ]);
        $otherService = Service::query()->create([
            'title' => 'Unarmed Guard Class',
            'is_active' => true,
        ]);

        $student = Student::query()->create([
            'name' => 'Demo Student',
            'email' => 'demo.student@example.com',
            'password' => 'password',
        ]);

        ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $matchService->id,
            'status' => 'confirmed',
            'payment_status' => 'fully_paid',
            'booking_type' => 'group',
            'number_of_students' => 1,
            'total_amount' => 150,
        ]);

        ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $otherService->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_type' => 'group',
            'number_of_students' => 1,
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.bookings.index', ['q' => 'Blended Security']));

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertStringContainsString('Blended Security Training Demo', $response->json('html'));
        $this->assertStringNotContainsString('Unarmed Guard Class', $response->json('html'));
    }
}
