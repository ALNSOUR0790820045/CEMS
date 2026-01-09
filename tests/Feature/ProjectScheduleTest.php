<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ScheduleActivity;
use App\Models\ScheduleCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_project_schedule(): void
    {
        $scheduleData = [
            'project_id' => $this->project->id,
            'name' => 'Project Schedule 2024',
            'description' => 'Main project schedule',
            'schedule_type' => 'baseline',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'working_days_per_week' => 5,
            'hours_per_day' => 8,
        ];

        $response = $this->postJson('/api/project-schedules', $scheduleData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'schedule_number', 'name', 'project_id']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Project Schedule 2024',
                     ]
                 ]);

        $this->assertDatabaseHas('project_schedules', [
            'project_id' => $this->project->id,
            'name' => 'Project Schedule 2024',
        ]);
    }

    public function test_schedule_number_is_auto_generated(): void
    {
        $scheduleData = [
            'project_id' => $this->project->id,
            'name' => 'Test Schedule',
            'schedule_type' => 'current',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ];

        $response = $this->postJson('/api/project-schedules', $scheduleData);

        $response->assertStatus(201);
        
        $scheduleNumber = $response->json('data.schedule_number');
        $this->assertMatchesRegularExpression('/^SCH-\d{4}-\d{4}$/', $scheduleNumber);
    }

    public function test_can_list_project_schedules(): void
    {
        ProjectSchedule::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/project-schedules');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'schedule_number', 'name', 'status']
                         ]
                     ]
                 ]);
    }

    public function test_can_get_schedules_by_project(): void
    {
        ProjectSchedule::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/project-schedules/project/' . $this->project->id);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_approve_schedule(): void
    {
        $schedule = ProjectSchedule::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson('/api/project-schedules/' . $schedule->id . '/approve');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'approved',
                     ]
                 ]);

        $this->assertDatabaseHas('project_schedules', [
            'id' => $schedule->id,
            'status' => 'approved',
            'approved_by_id' => $this->user->id,
        ]);
    }

    public function test_can_update_schedule(): void
    {
        $schedule = ProjectSchedule::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $updateData = [
            'name' => 'Updated Schedule Name',
            'description' => 'Updated description',
        ];

        $response = $this->putJson('/api/project-schedules/' . $schedule->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Schedule Name',
                     ]
                 ]);
    }

    public function test_can_delete_schedule(): void
    {
        $schedule = ProjectSchedule::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $response = $this->deleteJson('/api/project-schedules/' . $schedule->id);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertSoftDeleted('project_schedules', ['id' => $schedule->id]);
    }

    public function test_schedule_creation_requires_validation(): void
    {
        $response = $this->postJson('/api/project-schedules', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['project_id', 'name', 'schedule_type', 'start_date', 'end_date']);
    }
}
