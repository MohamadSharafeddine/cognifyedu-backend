<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

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
        $filePath = 'assignments/' . $this->faker->unique()->word . '.txt';
        Storage::disk('public')->put($filePath, $this->faker->text());

        return [
            'course_id' => Course::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(4), 
            'description' => $this->faker->paragraph(), 
            'attachment' => $filePath,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
