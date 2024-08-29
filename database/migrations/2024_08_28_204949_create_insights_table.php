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
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cognitive_score_id')->constrained('cognitive_scores')->onDelete('cascade');
            $table->foreignId('behavioral_score_id')->constrained('behavioral_scores')->onDelete('cascade');
            $table->foreignId('profile_comment_id')->nullable()->constrained('profile_comments')->onDelete('set null');
            $table->text('summary')->nullable();
            $table->text('detailed_analysis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
