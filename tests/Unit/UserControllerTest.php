<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /** @test */
    public function it_returns_all_users()
    {
        $users = User::factory()->count(3)->create();
        
        $this->actingAs($users->first(), 'api');
    
        $response = $this->getJson('/api/users');
    
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'email',
                    'profile_picture',
                ],
            ]);
    }    
}
