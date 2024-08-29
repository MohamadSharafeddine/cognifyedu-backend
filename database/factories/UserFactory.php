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
            'type' => $isStudent ? 'student' : $this->faker->randomElement(['teacher', 'admin']),
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address(),
            'profile_picture' => $this->faker->imageUrl(640, 480, 'people', true),
            'parent_id' => $isStudent ? null : $this->faker->optional()->randomElement(User::where('type', 'student')->pluck('id')->toArray()),
            'remember_token' => Str::random(10),
        ];
    }
}
