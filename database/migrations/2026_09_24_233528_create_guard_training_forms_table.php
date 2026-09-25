<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guard_training_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('form_number')->unique();
            $table->string('status')->default('draft'); // draft|published

            $table->string('registration_type')->nullable();

            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_initial', 10)->nullable();
            $table->text('ssn')->nullable(); // encrypted via cast
            $table->string('registration_number')->nullable();

            $table->boolean('initial_general')->default(false);
            $table->date('initial_general_date')->nullable();
            $table->unsignedTinyInteger('initial_general_score')->nullable();

            $table->boolean('initial_firearms')->default(false);
            $table->date('initial_firearms_date')->nullable();
            $table->unsignedTinyInteger('initial_firearms_score')->nullable();

            $table->boolean('initial_marksmanship')->default(false);
            $table->date('initial_marksmanship_date')->nullable();
            $table->unsignedTinyInteger('initial_marksmanship_score')->nullable();

            $table->json('weapons')->nullable();

            $table->boolean('classroom_renewal')->default(false);
            $table->date('classroom_renewal_date')->nullable();
            $table->unsignedTinyInteger('classroom_renewal_score')->nullable();

            $table->boolean('range_renewal')->default(false);
            $table->date('range_renewal_date')->nullable();
            $table->unsignedTinyInteger('range_renewal_score')->nullable();

            $table->boolean('classification_cpr')->default(false);
            $table->date('classification_cpr_date')->nullable();
            $table->boolean('classification_first_aid')->default(false);
            $table->date('classification_first_aid_date')->nullable();
            $table->boolean('classification_active_shooter')->default(false);
            $table->date('classification_active_shooter_date')->nullable();
            $table->boolean('classification_de_escalation')->default(false);
            $table->date('classification_de_escalation_date')->nullable();
            $table->boolean('classification_safe_restraint')->default(false);
            $table->date('classification_safe_restraint_date')->nullable();

            $table->string('trainer_name')->nullable();
            $table->string('trainer_certification_number')->nullable();
            $table->string('trainer_email')->nullable();
            $table->string('trainer_phone')->nullable();
            $table->string('assistant_trainer_name')->nullable();
            $table->string('assistant_trainer_certification_number')->nullable();
            $table->text('comments')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_training_forms');
    }
};
