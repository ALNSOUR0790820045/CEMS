<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    public function test_user_can_get_their_notifications()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        // Create some notifications for the user
        Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'notification_type',
                        'title',
                        'message',
                        'priority',
                        'is_read',
                        'created_at',
                    ]
                ]
            ]);
    }

    public function test_user_can_mark_notification_as_read()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/notifications/{$notification->id}/mark-read");

        $response->assertStatus(200);
        
        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        Notification::factory()->count(5)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/notifications/mark-all-read');

        $response->assertStatus(200)
            ->assertJson(['count' => 5]);

        $this->assertEquals(0, Notification::where('user_id', $user->id)->unread()->count());
    }

    public function test_user_can_filter_notifications_by_unread_only()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'is_read' => false,
        ]);
        
        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'is_read' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications?unread_only=1');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_notifications_require_authentication()
    {
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(401);
    }
}
