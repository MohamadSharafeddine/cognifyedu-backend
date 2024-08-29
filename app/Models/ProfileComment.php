<?php

namespace Database\Factories;

use App\Models\ProfileComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileCommentFactory extends Factory
{
    protected $model = ProfileComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::where('type', 'student')->inRandomOrder()->first()->id,
            'teacher_id' => User::where('type', 'teacher')->inRandomOrder()->first()->id,
            'comment' => $this->faker->sentence(),
        ];
    }
}
