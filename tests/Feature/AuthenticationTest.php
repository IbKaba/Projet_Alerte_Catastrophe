<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_register_and_reaches_citizen_home(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Test Citoyen',
            'email' => 'citoyen@example.com',
            'phone' => '+224 600 00 00 00',
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('citizen.home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'citoyen@example.com', 'role' => Role::CITIZEN->value]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post('/connexion', ['email' => $user->email, 'password' => 'Password123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
