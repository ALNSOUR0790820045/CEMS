<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_preferences(): void
    {
        NotificationPreference::factory()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/notification-preferences');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'notification_type', 'is_enabled']
                     ]
                 ]);
    }

    public function test_can_update_preferences(): void
    {
        $data = [
            'preferences' => [
                [
                    'notification_type' => 'budget_exceeded',
                    'channel_email' => true,
                    'channel_sms' => false,
                    'channel_push' => true,
                    'channel_in_app' => true,
                    'is_enabled' => true,
                ],
                [
                    'notification_type' => 'deadline_approaching',
                    'channel_email' => false,
                    'channel_sms' => false,
                    'channel_push' => true,
                    'channel_in_app' => true,
                    'is_enabled' => true,
                ],
            ],
        ];

        $response = $this->putJson('/api/notification-preferences', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->user->id,
            'notification_type' => 'budget_exceeded',
            'channel_email' => true,
        ]);
    }

    public function test_can_update_single_preference(): void
    {
        $data = [
            'channel_email' => true,
            'channel_sms' => true,
            'is_enabled' => true,
        ];

        $response = $this->putJson('/api/notification-preferences/low_stock', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->user->id,
            'notification_type' => 'low_stock',
            'channel_email' => true,
            'channel_sms' => true,
        ]);
    }

    public function test_can_set_quiet_hours(): void
    {
        $data = [
            'notification_type' => 'all',
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
            'is_enabled' => true,
        ];

        $response = $this->putJson('/api/notification-preferences/all', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->user->id,
            'notification_type' => 'all',
            'quiet_hours_start' => '22:00:00',
            'quiet_hours_end' => '08:00:00',
        ]);
    }
}
