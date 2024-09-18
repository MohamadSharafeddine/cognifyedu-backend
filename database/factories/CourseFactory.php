<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->bothify('?#?#?#')),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'teacher_id' => User::where('type', 'teacher')->inRandomOrder()->first()->id,
        ];
    }
}
