<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_cannot_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/administration/utilisateurs')
            ->assertForbidden();
    }

    public function test_citizen_cannot_open_moderation_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/tableau-de-bord')
            ->assertForbidden();
    }

    public function test_citizen_can_open_dedicated_home(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/mon-espace')
            ->assertOk();
    }

    public function test_admin_can_open_user_management(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/administration/utilisateurs')
            ->assertOk();
    }
}
