<?php

use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->text('ssn')->nullable()->after('phone');
            $table->string('ssn_last_four', 4)->nullable()->after('ssn');
        });

        DB::table('students')->orderBy('id')->chunkById(100, function ($students): void {
            foreach ($students as $student) {
                [$first, $middle, $last] = Student::splitDisplayName((string) $student->name);

                DB::table('students')->where('id', $student->id)->update([
                    'first_name' => $first,
                    'middle_name' => $middle,
                    'last_name' => $last,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'ssn', 'ssn_last_four']);
        });
    }
};
