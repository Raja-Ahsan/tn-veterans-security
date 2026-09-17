<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('initials', 10);
            $table->string('url', 500);
            $table->string('blurb', 500)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_nav')->default(false);
            $table->boolean('show_in_nra_nav')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
