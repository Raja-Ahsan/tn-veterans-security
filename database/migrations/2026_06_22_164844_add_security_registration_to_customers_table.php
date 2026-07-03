<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('has_security_registration')->default(false)->after('address');
            $table->string('security_registration_number')->nullable()->after('has_security_registration');
            $table->date('security_registration_expiration')->nullable()->after('security_registration_number');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'has_security_registration',
                'security_registration_number',
                'security_registration_expiration',
            ]);
        });
    }
};
