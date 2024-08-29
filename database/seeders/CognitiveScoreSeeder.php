<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CognitiveScore;

class CognitiveScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CognitiveScore::factory()->count(50)->create();
    }
}
