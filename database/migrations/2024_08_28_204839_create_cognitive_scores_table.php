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
        Schema::create('cognitive_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            // $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            $table->integer('critical_thinking');
            $table->integer('logical_thinking');
            $table->integer('linguistic_ability');
            $table->integer('memory');
            $table->integer('attention_to_detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cognitive_scores');
    }
};
