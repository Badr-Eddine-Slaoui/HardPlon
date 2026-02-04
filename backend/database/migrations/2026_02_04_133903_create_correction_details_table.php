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
        Schema::create('correction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_id')->constrained('corrections')->cascadeOnDelete();
            $table->foreignId('brief_skill_level_id')->constrained('brief_skill_levels')->cascadeOnDelete();
            $table->enum('grade', ['POOR','AVERAGE','EXCELLENT']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correction_details');
    }
};
