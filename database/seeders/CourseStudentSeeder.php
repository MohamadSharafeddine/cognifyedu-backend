<?php

namespace Database\Seeders;

use App\Models\CourseStudent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseStudent::factory()->count(10)->create();
    }
}
