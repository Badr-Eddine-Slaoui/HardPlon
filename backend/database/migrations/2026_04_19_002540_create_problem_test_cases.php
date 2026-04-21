<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('problem_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained('problems')->cascadeOnDelete();
            $table->json('input');
            $table->json('expected_output');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_test_cases');
    }
};
