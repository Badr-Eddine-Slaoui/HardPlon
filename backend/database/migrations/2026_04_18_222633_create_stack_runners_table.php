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
        Schema::create('stack_runners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_id')->constrained('stacks')->cascadeOnDelete();
            $table->foreignId('runner_version_id')->constrained('runner_versions')->cascadeOnDelete();
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stack_runners');
    }
};
