<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userType = $this->faker->randomElement(['teacher', 'student', 'parent', 'admin']);
        $isStudent = $userType === 'student';
        $parent = $isStudent ? User::where('type', 'parent')->inRandomOrder()->first() : null;

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'type' => $userType,
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address(),
            'profile_picture' => $this->faker->imageUrl(640, 480, 'people', true),
            'parent_id' => $parent ? $parent->id : null,
            'remember_token' => Str::random(10),
        ];
    }
}
