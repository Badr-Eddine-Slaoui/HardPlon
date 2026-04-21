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
        Schema::create('runner_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('runner_id')->constrained('runners')->cascadeOnDelete();
            $table->string('version');
            $table->string('docker_image');
            $table->json('default_config');
            $table->enum('status', ["active", "deprecated", "disabled"])->default('active');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runner_versions');
    }
};
