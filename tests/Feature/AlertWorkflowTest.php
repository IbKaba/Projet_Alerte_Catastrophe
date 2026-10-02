<?php

namespace Tests\Feature;

use App\Enums\AlertStatus;
use App\Models\Alert;
use App\Models\Disaster;
use App\Models\DisasterCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_create_alert_and_moderator_can_validate_it(): void
    {
        $category = DisasterCategory::create(['name' => 'Test', 'is_active' => true]);
        $disaster = Disaster::create([
            'category_id' => $category->id,
            'name' => 'Inondation',
            'is_active' => true,
        ]);
        $citizen = User::factory()->create();
        $moderator = User::factory()->moderator()->create();

        $this->actingAs($citizen)->post('/alertes', [
            'disaster_id' => $disaster->id,
            'latitude' => '9.64118500',
            'longitude' => '-13.57840100',
            'address' => 'Zone de test',
            'description' => 'Une montée rapide des eaux est observée dans la zone de test.',
        ])->assertRedirect();

        $alertId = (int) Alert::query()->value('id');

        $this->actingAs($moderator)->patch("/moderation/alertes/{$alertId}", [
            'status' => AlertStatus::VALIDATED->value,
        ])->assertRedirect();

        $this->assertDatabaseHas('alerts', ['id' => $alertId, 'status' => AlertStatus::VALIDATED->value]);
    }
}
