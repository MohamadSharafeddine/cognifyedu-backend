<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(4), 
            'description' => $this->faker->paragraph(), 
            'attachment' => $this->faker->optional()->url(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
