<?php

namespace Database\Factories;

use App\Models\CognitiveScore;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class CognitiveScoreFactory extends Factory
{
    protected $model = CognitiveScore::class;

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
            'critical_thinking' => $this->faker->numberBetween(1, 100),
            'logical_thinking' => $this->faker->numberBetween(1, 100),
            'linguistic_ability' => $this->faker->numberBetween(1, 100),
            'memory' => $this->faker->numberBetween(1, 100),
            'attention_to_detail' => $this->faker->numberBetween(1, 100),
        ];
    }
}
