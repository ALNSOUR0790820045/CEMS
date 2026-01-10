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
use Tests\TestCase;

class CPMCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected ProjectSchedule $schedule;
    protected ScheduleCPMService $cpmService;

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
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $this->cpmService = new ScheduleCPMService();
    }

    public function test_can_calculate_forward_pass(): void
    {
        // Create a simple network: A -> B -> C
        $activityA = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'planned_duration' => 5,
            'planned_start' => '2024-01-01',
        ]);

        $activityB = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'planned_duration' => 3,
        ]);

        $activityC = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'C',
            'name' => 'Activity C',
            'planned_duration' => 4,
        ]);

        // Create dependencies: A -> B, B -> C
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'dependency_type' => 'FS',
            'lag_days' => 0,
        ]);

        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityB->id,
            'successor_id' => $activityC->id,
            'dependency_type' => 'FS',
            'lag_days' => 0,
        ]);

        // Calculate CPM
        $this->cpmService->calculate($this->schedule);

        // Refresh activities
        $activityA->refresh();
        $activityB->refresh();
        $activityC->refresh();

        // Check forward pass calculations
        $this->assertNotNull($activityA->early_start);
        $this->assertNotNull($activityA->early_finish);
        $this->assertNotNull($activityB->early_start);
        $this->assertNotNull($activityC->early_start);
    }

    public function test_can_identify_critical_path(): void
    {
        // Create a network with critical path: A -> B -> D
        // and non-critical path: A -> C -> D
        $activityA = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'A',
            'planned_duration' => 5,
            'planned_start' => '2024-01-01',
        ]);

        $activityB = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'B',
            'planned_duration' => 10,
        ]);

        $activityC = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'C',
            'planned_duration' => 3,
        ]);

        $activityD = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'D',
            'planned_duration' => 5,
        ]);

        // A -> B
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'dependency_type' => 'FS',
        ]);

        // A -> C
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityC->id,
            'dependency_type' => 'FS',
        ]);

        // B -> D
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityB->id,
            'successor_id' => $activityD->id,
            'dependency_type' => 'FS',
        ]);

        // C -> D
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityC->id,
            'successor_id' => $activityD->id,
            'dependency_type' => 'FS',
        ]);

        // Calculate CPM
        $this->cpmService->calculate($this->schedule);

        // Get critical path
        $criticalPath = $this->cpmService->getCriticalPath($this->schedule);

        // Verify critical activities
        $this->assertGreaterThan(0, $criticalPath->count());
        
        // Activities A, B, D should be critical (longer path)
        $activityA->refresh();
        $activityB->refresh();
        $activityC->refresh();
        $activityD->refresh();

        $this->assertTrue($activityA->is_critical);
        $this->assertTrue($activityB->is_critical);
        $this->assertTrue($activityD->is_critical);
        
        // Activity C should have float
        $this->assertGreaterThan(0, $activityC->total_float);
    }

    public function test_can_handle_different_dependency_types(): void
    {
        $activityA = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'A',
            'planned_duration' => 5,
            'planned_start' => '2024-01-01',
        ]);

        $activityB = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'B',
            'planned_duration' => 3,
        ]);

        // Test SS (Start-to-Start) dependency
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'dependency_type' => 'SS',
            'lag_days' => 2,
        ]);

        $this->cpmService->calculate($this->schedule);

        $activityB->refresh();
        $this->assertNotNull($activityB->early_start);
    }

    public function test_prevents_circular_dependencies(): void
    {
        $activityA = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'A',
            'planned_duration' => 5,
        ]);

        $activityB = ScheduleActivity::factory()->create([
            'project_schedule_id' => $this->schedule->id,
            'activity_code' => 'B',
            'planned_duration' => 3,
        ]);

        // Create A -> B
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'dependency_type' => 'FS',
        ]);

        // Try to create B -> A (circular)
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        ActivityDependency::create([
            'project_schedule_id' => $this->schedule->id,
            'predecessor_id' => $activityB->id,
            'successor_id' => $activityA->id,
            'dependency_type' => 'FS',
        ]);
    }
}
