<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StudentRegistrationFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_shows_name_phone_and_ssn_fields(): void
    {
        $this->get(route('student.register'))
            ->assertOk()
            ->assertSee('First Name')
            ->assertSee('Middle Name')
            ->assertSee('Last Name')
            ->assertSee('Phone Number')
            ->assertSee('Social Security Number');
    }

    public function test_student_can_register_with_first_middle_last_phone_and_ssn(): void
    {
        Mail::fake();

        $this->post(route('student.register'), [
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Student',
            'email' => 'jane.fields@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '615-555-0100',
            'ssn' => '456-78-9012',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $student = Student::query()->where('email', 'jane.fields@example.com')->firstOrFail();

        $this->assertSame('Jane', $student->first_name);
        $this->assertSame('Marie', $student->middle_name);
        $this->assertSame('Student', $student->last_name);
        $this->assertSame('Jane Marie Student', $student->name);
        $this->assertSame('615-555-0100', $student->phone);
        $this->assertSame('456789012', $student->ssn);
        $this->assertSame('9012', $student->ssn_last_four);
        $this->assertSame('***-**-9012', $student->maskedSsn());

        $rawSsn = DB::table('students')->where('id', $student->id)->value('ssn');
        $this->assertNotSame('456789012', $rawSsn);
        $this->assertNotSame('456-78-9012', $rawSsn);
    }

    public function test_registration_requires_phone_and_ssn(): void
    {
        $this->from(route('student.register'))
            ->post(route('student.register'), [
                'first_name' => 'Jane',
                'last_name' => 'Student',
                'email' => 'jane.required@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('student.register'))
            ->assertSessionHasErrors(['phone', 'ssn']);
    }

    public function test_registration_rejects_an_invalid_ssn(): void
    {
        $this->from(route('student.register'))
            ->post(route('student.register'), [
                'first_name' => 'Jane',
                'last_name' => 'Student',
                'email' => 'jane.ssn@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'phone' => '6155550100',
                'ssn' => '000-00-0000',
            ])
            ->assertRedirect(route('student.register'))
            ->assertSessionHasErrors('ssn');
    }
}
