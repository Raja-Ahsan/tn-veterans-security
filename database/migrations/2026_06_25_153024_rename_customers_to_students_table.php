<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $tablesWithCustomerId = [
        'service_bookings',
        'payments',
        'waitlist_entries',
        'student_module_progress',
        'module_quiz_attempts',
        'in_person_test_results',
        'course_certificates',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        foreach ($this->tablesWithCustomerId as $table) {
            if (Schema::hasColumn($table, 'customer_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['customer_id']);
                });
            }
        }

        if (Schema::hasTable('waitlist_entries')) {
            Schema::table('waitlist_entries', function (Blueprint $blueprint) {
                $blueprint->dropUnique(['class_schedule_id', 'customer_id']);
            });
        }

        if (Schema::hasTable('payments') && $this->indexExists('payments', 'payments_customer_id_payment_date_index')) {
            Schema::table('payments', function (Blueprint $blueprint) {
                $blueprint->dropIndex(['customer_id', 'payment_date']);
            });
        }

        Schema::rename('customers', 'students');

        foreach ($this->tablesWithCustomerId as $table) {
            if (Schema::hasColumn($table, 'customer_id')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `customer_id` `student_id` BIGINT UNSIGNED NOT NULL");

                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                });
            }
        }

        if (Schema::hasTable('waitlist_entries')) {
            Schema::table('waitlist_entries', function (Blueprint $blueprint) {
                $blueprint->unique(['class_schedule_id', 'student_id']);
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $blueprint) {
                $blueprint->index(['student_id', 'payment_date']);
            });
        }

        if (Schema::hasTable('student_module_progress')) {
            Schema::table('student_module_progress', function (Blueprint $blueprint) {
                $blueprint->dropUnique(['customer_id', 'course_module_id']);
                $blueprint->unique(['student_id', 'course_module_id']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('student_module_progress')) {
            Schema::table('student_module_progress', function (Blueprint $blueprint) {
                $blueprint->dropUnique(['student_id', 'course_module_id']);
            });
        }

        if (Schema::hasTable('waitlist_entries')) {
            Schema::table('waitlist_entries', function (Blueprint $blueprint) {
                $blueprint->dropUnique(['class_schedule_id', 'student_id']);
            });
        }

        if (Schema::hasTable('payments') && $this->indexExists('payments', 'payments_student_id_payment_date_index')) {
            Schema::table('payments', function (Blueprint $blueprint) {
                $blueprint->dropIndex(['student_id', 'payment_date']);
            });
        }

        foreach ($this->tablesWithCustomerId as $table) {
            if (Schema::hasColumn($table, 'student_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['student_id']);
                });
            }
        }

        Schema::rename('students', 'customers');

        foreach ($this->tablesWithCustomerId as $table) {
            if (Schema::hasColumn($table, 'student_id')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `student_id` `customer_id` BIGINT UNSIGNED NOT NULL");

                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
                });
            }
        }

        if (Schema::hasTable('waitlist_entries')) {
            Schema::table('waitlist_entries', function (Blueprint $blueprint) {
                $blueprint->unique(['class_schedule_id', 'customer_id']);
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $blueprint) {
                $blueprint->index(['customer_id', 'payment_date']);
            });
        }

        if (Schema::hasTable('student_module_progress')) {
            Schema::table('student_module_progress', function (Blueprint $blueprint) {
                $blueprint->unique(['customer_id', 'course_module_id']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);

        foreach ($indexes as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }
};
