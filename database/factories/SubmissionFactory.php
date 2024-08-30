<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filePath = 'submissions/' . $this->faker->unique()->word . '.txt';
        Storage::put($filePath, $this->faker->text());
        return [
            'assignment_id' => Assignment::inRandomOrder()->first()->id, 
            'student_id' => User::where('type', 'student')->inRandomOrder()->first()->id,
            'deliverable' => $filePath,
            'submission_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'mark' => $this->faker->numberBetween(0, 100),
            'teacher_comment' => $this->faker->optional()->sentence(),
        ];
    }
}
