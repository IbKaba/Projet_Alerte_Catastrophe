<?php

namespace Tests\Feature;

use App\Enums\AlertStatus;
use App\Models\Alert;
use App\Models\Disaster;
use App\Models\DisasterCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_with_six_characters_is_accepted_for_registration(): void
    {
        $this->post('/inscription', [
            'name' => 'Citoyen Test',
            'email' => 'six@example.com',
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
            'terms' => '1',
        ])->assertRedirect(route('citizen.home'));

        $this->assertDatabaseHas('users', ['email' => 'six@example.com']);
    }

    public function test_inactive_disaster_cannot_be_used_for_new_alert(): void
    {
        $category = DisasterCategory::create(['name' => 'Test', 'is_active' => true]);
        $disaster = Disaster::create([
            'category_id' => $category->id,
            'name' => 'Type inactif',
            'is_active' => false,
        ]);
        $citizen = User::factory()->create();

        $this->actingAs($citizen)->post('/alertes', [
            'disaster_id' => $disaster->id,
            'latitude' => 9.6,
            'longitude' => -13.6,
            'description' => 'Description suffisamment longue pour être validée par le formulaire.',
        ])->assertSessionHasErrors('disaster_id');

        $this->assertDatabaseCount('alerts', 0);
    }

    public function test_pending_alert_cannot_be_marked_resolved_directly(): void
    {
        [$alert, $moderator] = $this->makeAlertWithModerator(AlertStatus::PENDING);

        $this->actingAs($moderator)->patch(route('moderation.alerts.update', $alert), [
            'status' => AlertStatus::RESOLVED->value,
        ])->assertSessionHasErrors('status');

        $this->assertDatabaseHas('alerts', [
            'id' => $alert->id,
            'status' => AlertStatus::PENDING->value,
        ]);
    }

    public function test_validated_alert_can_be_resolved(): void
    {
        [$alert, $moderator] = $this->makeAlertWithModerator(AlertStatus::VALIDATED);

        $this->actingAs($moderator)->patch(route('moderation.alerts.update', $alert), [
            'status' => AlertStatus::RESOLVED->value,
        ])->assertRedirect();

        $this->assertDatabaseHas('alerts', [
            'id' => $alert->id,
            'status' => AlertStatus::RESOLVED->value,
        ]);
    }

    public function test_resolved_alert_cannot_be_moderated_again(): void
    {
        [$alert, $moderator] = $this->makeAlertWithModerator(AlertStatus::RESOLVED);

        $this->actingAs($moderator)->patch(route('moderation.alerts.update', $alert), [
            'status' => AlertStatus::REJECTED->value,
            'rejection_reason' => 'Tentative de réouverture',
        ])->assertForbidden();
    }

    public function test_admin_cannot_demote_the_only_active_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), [
            'role' => 'citizen',
            'is_active' => '1',
        ])->assertSessionHasErrors('user');

        $admin->refresh();
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->is_active);
    }

    /**
     * @return array{0: Alert, 1: User}
     */
    private function makeAlertWithModerator(AlertStatus $status): array
    {
        $category = DisasterCategory::create(['name' => 'Test '.uniqid(), 'is_active' => true]);
        $disaster = Disaster::create([
            'category_id' => $category->id,
            'name' => 'Inondation',
            'is_active' => true,
        ]);
        $citizen = User::factory()->create();
        $moderator = User::factory()->moderator()->create();

        $alert = Alert::create([
            'user_id' => $citizen->id,
            'disaster_id' => $disaster->id,
            'latitude' => 9.641185,
            'longitude' => -13.578401,
            'description' => 'Une situation de test est signalée avec une description suffisamment longue.',
            'status' => $status,
            'validated_by' => $status !== AlertStatus::PENDING ? $moderator->id : null,
            'validated_at' => $status === AlertStatus::VALIDATED || $status === AlertStatus::RESOLVED ? now() : null,
            'resolved_at' => $status === AlertStatus::RESOLVED ? now() : null,
        ]);

        return [$alert, $moderator];
    }
}
