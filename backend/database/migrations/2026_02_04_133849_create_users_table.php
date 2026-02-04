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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->integer('age');
            $table->string('email')->unique();
            $table->string('cin', 50)->unique();
            $table->string('phone', 50)->unique();
            $table->string('password');
            $table->enum('role', ['ADMIN','STUDENT','TEACHER']);
            $table->foreignId('id_class')->nullable()->constrained('class_groups')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
