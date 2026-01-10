<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ScheduledNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduledNotificationTest extends TestCase
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

    public function test_can_list_scheduled_notifications(): void
    {
        ScheduledNotification::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/scheduled-notifications');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title', 'scheduled_at', 'status']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_scheduled_notification(): void
    {
        $data = [
            'title' => 'Scheduled Message',
            'body' => 'This is a scheduled message',
            'scheduled_at' => now()->addDay()->toIso8601String(),
            'repeat_type' => 'once',
            'recipients_type' => 'user',
            'recipients_ids' => [$this->user->id],
        ];

        $response = $this->postJson('/api/scheduled-notifications', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'title' => 'Scheduled Message',
                         'status' => 'pending',
                     ]
                 ]);

        $this->assertDatabaseHas('scheduled_notifications', [
            'title' => 'Scheduled Message',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_show_scheduled_notification(): void
    {
        $notification = ScheduledNotification::factory()->create([
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/scheduled-notifications/{$notification->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $notification->id,
                         'title' => $notification->title,
                     ]
                 ]);
    }

    public function test_can_update_scheduled_notification(): void
    {
        $notification = ScheduledNotification::factory()->create([
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $data = [
            'title' => 'Updated Title',
            'body' => 'Updated body',
        ];

        $response = $this->putJson("/api/scheduled-notifications/{$notification->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'title' => 'Updated Title',
                     ]
                 ]);
    }

    public function test_can_delete_scheduled_notification(): void
    {
        $notification = ScheduledNotification::factory()->create([
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/scheduled-notifications/{$notification->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('scheduled_notifications', ['id' => $notification->id]);
    }

    public function test_can_cancel_scheduled_notification(): void
    {
        $notification = ScheduledNotification::factory()->create([
            'company_id' => $this->company->id,
            'created_by_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/scheduled-notifications/{$notification->id}/cancel");

        $response->assertStatus(200);

        $this->assertEquals('cancelled', $notification->fresh()->status);
    }

    public function test_scheduled_notification_requires_validation(): void
    {
        $response = $this->postJson('/api/scheduled-notifications', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'title', 
                     'body', 
                     'scheduled_at', 
                     'repeat_type', 
                     'recipients_type'
                 ]);
    }

    public function test_scheduled_at_must_be_future(): void
    {
        $data = [
            'title' => 'Test',
            'body' => 'Test body',
            'scheduled_at' => now()->subDay()->toIso8601String(),
            'repeat_type' => 'once',
            'recipients_type' => 'user',
        ];

        $response = $this->postJson('/api/scheduled-notifications', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['scheduled_at']);
    }
}
