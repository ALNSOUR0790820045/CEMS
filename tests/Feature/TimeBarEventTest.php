<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\TimeBarEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeBarEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_time_bar_event_can_be_created(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'project_number' => 'PRJ-2026-001',
            'name' => 'Test Project',
            'status' => 'active',
            'budget' => 1000000,
            'currency' => 'JOD',
        ]);

        $event = TimeBarEvent::create([
            'project_id' => $project->id,
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_date' => now(),
            'discovery_date' => now(),
            'event_type' => 'delay',
            'notice_period_days' => 28,
            'status' => 'identified',
            'identified_by' => $user->id,
        ]);

        $this->assertNotNull($event->event_number);
        $this->assertNotNull($event->notice_deadline);
        $this->assertEquals(28, $event->notice_period_days);
        $this->assertEquals('identified', $event->status);
    }

    public function test_event_number_is_auto_generated(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'project_number' => 'PRJ-2026-001',
            'name' => 'Test Project',
            'status' => 'active',
        ]);

        $event1 = TimeBarEvent::create([
            'project_id' => $project->id,
            'title' => 'Event 1',
            'description' => 'Description',
            'event_date' => now(),
            'discovery_date' => now(),
            'event_type' => 'delay',
            'identified_by' => $user->id,
        ]);

        $event2 = TimeBarEvent::create([
            'project_id' => $project->id,
            'title' => 'Event 2',
            'description' => 'Description',
            'event_date' => now(),
            'discovery_date' => now(),
            'event_type' => 'disruption',
            'identified_by' => $user->id,
        ]);

        $this->assertMatchesRegularExpression('/^TBE-\d{4}-\d{4}$/', $event1->event_number);
        $this->assertMatchesRegularExpression('/^TBE-\d{4}-\d{4}$/', $event2->event_number);
        $this->assertNotEquals($event1->event_number, $event2->event_number);
    }

    public function test_days_remaining_is_calculated(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'project_number' => 'PRJ-2026-001',
            'name' => 'Test Project',
            'status' => 'active',
        ]);

        $event = TimeBarEvent::create([
            'project_id' => $project->id,
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_date' => now()->subDays(5),
            'discovery_date' => now()->subDays(3),
            'event_type' => 'delay',
            'notice_period_days' => 28,
            'identified_by' => $user->id,
        ]);

        // Days remaining should be approximately 25 (28 - 3)
        $this->assertTrue($event->days_remaining >= 24 && $event->days_remaining <= 26);
    }

    public function test_event_has_relationships(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'project_number' => 'PRJ-2026-001',
            'name' => 'Test Project',
            'status' => 'active',
        ]);

        $event = TimeBarEvent::create([
            'project_id' => $project->id,
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_date' => now(),
            'discovery_date' => now(),
            'event_type' => 'delay',
            'identified_by' => $user->id,
        ]);

        $this->assertInstanceOf(Project::class, $event->project);
        $this->assertInstanceOf(User::class, $event->identifiedBy);
        $this->assertEquals($project->id, $event->project->id);
        $this->assertEquals($user->id, $event->identifiedBy->id);
    }
}
