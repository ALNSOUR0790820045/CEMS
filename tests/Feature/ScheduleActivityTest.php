<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ScheduleActivity;
use App\Models\ActivityDependency;
use App\Models\User;
use App\Services\ScheduleCPMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleActivityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected ProjectSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->schedule = ProjectSchedule::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_schedule_activity(): void
    {
        $activityData = [
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'ACT-001',
            'name' => 'Foundation Work',
            'activity_type' => 'task',
            'planned_duration' => 10,
            'planned_start' => '2024-01-01',
            'planned_finish' => '2024-01-10',
        ];

        $response = $this->postJson('/api/schedule-activities', $activityData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'activity_code', 'name']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'activity_code' => 'ACT-001',
                         'name' => 'Foundation Work',
                     ]
                 ]);

        $this->assertDatabaseHas('schedule_activities', [
            'activity_code' => 'ACT-001',
            'name' => 'Foundation Work',
        ]);
    }

    public function test_can_update_activity_progress(): void
    {
        $activity = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'budgeted_cost' => 10000,
            'percent_complete' => 0,
        ]);

        $progressData = [
            'percent_complete' => 50,
            'actual_cost' => 5000,
            'actual_start' => '2024-01-01',
        ];

        $response = $this->postJson('/api/schedule-activities/' . $activity->id . '/update-progress', $progressData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'percent_complete' => '50.00',
                         'status' => 'in_progress',
                     ]
                 ]);

        $activity->refresh();
        $this->assertEquals(5000, $activity->earned_value);
    }

    public function test_can_bulk_update_activities(): void
    {
        $activities = ScheduleActivity::factory()->count(3)->create([
            'project_schedule_id' => $this->schedule->id,
        ]);

        $bulkData = [
            'activities' => [
                ['id' => $activities[0]->id, 'percent_complete' => 100, 'status' => 'completed'],
                ['id' => $activities[1]->id, 'percent_complete' => 50, 'status' => 'in_progress'],
                ['id' => $activities[2]->id, 'percent_complete' => 0, 'status' => 'not_started'],
            ]
        ];

        $response = $this->postJson('/api/schedule-activities/bulk-update', $bulkData);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('schedule_activities', [
            'id' => $activities[0]->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_get_activities_by_schedule(): void
    {
        ScheduleActivity::factory()->count(5)->create([
            'project_schedule_id' => $this->schedule->id,
        ]);

        $response = $this->getJson('/api/schedule-activities/schedule/' . $this->schedule->id);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        
        $this->assertCount(5, $response->json('data'));
    }

    public function test_activity_code_must_be_unique(): void
    {
        ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'UNIQUE-001',
        ]);

        $response = $this->postJson('/api/schedule-activities', [
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'UNIQUE-001',
            'name' => 'Test Activity',
            'activity_type' => 'task',
            'planned_duration' => 5,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['activity_code']);
    }
}
