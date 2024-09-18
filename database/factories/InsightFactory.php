<?php

namespace Database\Factories;

use App\Models\Insight;
use App\Models\CognitiveScore;
use App\Models\BehavioralScore;
use App\Models\ProfileComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsightFactory extends Factory
{
    protected $model = Insight::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::where('type', 'student')->inRandomOrder()->first()->id, 
            'cognitive_score_id' => CognitiveScore::inRandomOrder()->first()->id, 
            'behavioral_score_id' => BehavioralScore::inRandomOrder()->first()->id,
            'profile_comment_id' => ProfileComment::inRandomOrder()->first()->id,
            'summary' => $this->faker->paragraph(), 
            'detailed_analysis' => $this->faker->text(),
            'recommendations' => $this->faker->text(),
            'progress_tracking' => $this->faker->text(),
        ];
    }
}
