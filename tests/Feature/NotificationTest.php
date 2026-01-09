<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    public function test_can_list_notifications(): void
    {
        Notification::factory()->count(3)->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title', 'body', 'type', 'category']
                         ]
                     ]
                 ]);
    }

    public function test_can_get_unread_notifications(): void
    {
        Notification::factory()->count(2)->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
            'read_at' => null,
        ]);

        Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
            'read_at' => now(),
        ]);

        $response = $this->getJson('/api/notifications/unread');

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data.data')));
    }

    public function test_can_get_unread_count(): void
    {
        Notification::factory()->count(5)->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
            'read_at' => null,
        ]);

        $response = $this->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'count' => 5,
                 ]);
    }

    public function test_can_mark_notification_as_read(): void
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
            'read_at' => null,
        ]);

        $response = $this->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_can_mark_all_as_read(): void
    {
        Notification::factory()->count(3)->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
            'read_at' => null,
        ]);

        $response = $this->postJson('/api/notifications/read-all');

        $response->assertStatus(200);

        $unreadCount = Notification::where('notifiable_id', $this->user->id)
            ->whereNull('read_at')
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    public function test_can_delete_notification(): void
    {
        $notification = Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_can_send_notification(): void
    {
        $recipient = User::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'title' => 'Test Notification',
            'body' => 'This is a test notification',
            'type' => 'info',
            'user_ids' => [$recipient->id],
        ];

        $response = $this->postJson('/api/notifications/send', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('notifications', [
            'title' => 'Test Notification',
            'notifiable_id' => $recipient->id,
        ]);
    }

    public function test_can_broadcast_notification(): void
    {
        User::factory()->count(3)->create(['company_id' => $this->company->id]);

        $data = [
            'title' => 'Broadcast Message',
            'body' => 'This is a broadcast message',
            'type' => 'info',
        ];

        $response = $this->postJson('/api/notifications/broadcast', $data);

        $response->assertStatus(201);

        // Should create notifications for all users in company (including the auth user)
        $count = Notification::where('company_id', $this->company->id)->count();
        $this->assertGreaterThanOrEqual(3, $count);
    }

    public function test_user_cannot_see_others_notifications(): void
    {
        $otherUser = User::factory()->create(['company_id' => $this->company->id]);

        Notification::factory()->create([
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200);
        $this->assertEquals(0, count($response->json('data.data')));
    }
}
