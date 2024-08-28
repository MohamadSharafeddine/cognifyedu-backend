<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $isStudent = $this->faker->randomElement(['teacher', 'student', 'admin']) === 'student';
    
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'type' => $isStudent ? 'student' : $this->faker->randomElement(['teacher', 'admin']),
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address(),
            'parent_id' => $isStudent ? null : $this->faker->optional()->randomElement(User::where('type', 'student')->pluck('id')->toArray()),
        ];
    }
}
