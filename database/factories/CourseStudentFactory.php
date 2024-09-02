<?php

namespace Database\Factories;


use App\Models\CourseStudent;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseStudent>
 */
class CourseStudentFactory extends Factory
{
    protected $model = CourseStudent::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::where('type', 'student')->inRandomOrder()->first()->id,
            'course_id' => Course::inRandomOrder()->first()->id
        ];
    }
}
