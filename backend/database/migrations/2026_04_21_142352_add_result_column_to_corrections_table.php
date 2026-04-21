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
        Schema::table('corrections', function (Blueprint $table) {
            $table->enum('result', ['Pending', 'Valid', 'Invalid'])->default('Pending')->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corrections', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }
};
