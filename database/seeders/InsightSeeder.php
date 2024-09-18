<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Insight;

class InsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Insight::factory()->count(30)->create();
    }
}
