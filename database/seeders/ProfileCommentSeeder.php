<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfileComment;

class ProfileCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfileComment::factory()->count(30)->create();
    }
}
