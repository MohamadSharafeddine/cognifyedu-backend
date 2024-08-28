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
        $type = $this->faker->randomElement(['teacher', 'student', 'admin']);
        $isStudent = $type === 'student';
    
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'type' => $type,
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address(),
            'parent_id' => $isStudent ? null : $this->faker->optional()->randomElement(User::where('type', 'student')->pluck('id')->toArray()),
        ];
    }  
}
