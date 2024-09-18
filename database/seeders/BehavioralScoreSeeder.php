<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BehavioralScore;

class BehavioralScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BehavioralScore::factory()->count(50)->create(); 
    }
}
