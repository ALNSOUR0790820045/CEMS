<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\NotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_user_can_get_their_preferences()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        NotificationPreference::create([
            'user_id' => $user->id,
            'notification_type' => 'approval_request',
            'in_app_enabled' => true,
            'email_enabled' => false,
            'sms_enabled' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/notification-preferences');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_user_can_update_preferences()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/notification-preferences', [
                'preferences' => [
                    [
                        'notification_type' => 'approval_request',
                        'in_app_enabled' => true,
                        'email_enabled' => true,
                        'sms_enabled' => false,
                    ],
                    [
                        'notification_type' => 'task_assignment',
                        'in_app_enabled' => true,
                        'email_enabled' => false,
                        'sms_enabled' => true,
                    ]
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Preferences updated successfully'
            ]);

        $this->assertEquals(2, NotificationPreference::where('user_id', $user->id)->count());
    }

    public function test_preferences_require_authentication()
    {
        $response = $this->getJson('/api/notification-preferences');
        $response->assertStatus(401);
    }
}
