<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receives_a_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Ada', 'email' => 'ada@example.com', 'password' => 'password123',
        ])->assertCreated()->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
    }

    public function test_registration_validates_input(): void
    {
        $this->postJson('/api/register', ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_log_in_with_correct_credentials(): void
    {
        User::factory()->create(['email' => 'ada@example.com', 'password' => 'password123']);

        $this->postJson('/api/login', ['email' => 'ada@example.com', 'password' => 'password123'])
            ->assertOk()->assertJsonStructure(['token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'ada@example.com', 'password' => 'password123']);

        $this->postJson('/api/login', ['email' => 'ada@example.com', 'password' => 'wrong'])
            ->assertUnprocessable();
    }

    public function test_protected_routes_require_a_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->getJson('/api/tasks')->assertUnauthorized();
    }
}
