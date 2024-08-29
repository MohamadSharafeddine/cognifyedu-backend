<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            AssignmentSeeder::class,
            SubmissionSeeder::class,
            CognitiveScoreSeeder::class,
            BehavioralScoreSeeder::class,
            ProfileCommentSeeder::class,
            InsightSeeder::class,
        ]);
    }
}