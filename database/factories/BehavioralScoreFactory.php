<?php

namespace Database\Factories;

use App\Models\BehavioralScore;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class BehavioralScoreFactory extends Factory
{
    protected $model = BehavioralScore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::inRandomOrder()->first()->id,
            'submission_id' => Submission::inRandomOrder()->first()->id,
            'engagement' => $this->faker->numberBetween(1, 100),
            'time_management' => $this->faker->numberBetween(1, 100),
            'adaptability' => $this->faker->numberBetween(1, 100),
            'collaboration' => $this->faker->numberBetween(1, 100),
            'focus' => $this->faker->numberBetween(1, 100),
        ];
    }
}
