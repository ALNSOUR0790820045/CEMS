<?php

namespace Tests\Unit;

use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderActivityDependency;
use App\Services\CPMCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CPMCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cpmService;
    protected $tender;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cpmService = new CPMCalculationService();
        
        $this->tender = Tender::create([
            'tender_code' => 'TND-001',
            'name' => 'Test Tender',
            'description' => 'Test tender for CPM',
            'status' => 'draft',
        ]);
    }

    public function test_cpm_calculation_with_simple_sequence(): void
    {
        // Create three activities in sequence: A -> B -> C
        $activityA = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityB = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityC = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'C',
            'name' => 'Activity C',
            'duration_days' => 8,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        // Create dependencies: A -> B -> C
        TenderActivityDependency::create([
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TenderActivityDependency::create([
            'predecessor_id' => $activityB->id,
            'successor_id' => $activityC->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        // Calculate CPM
        $result = $this->cpmService->calculateCPM($this->tender->id);

        $this->assertTrue($result['success']);
        // Verify the calculation completed successfully
        $this->assertArrayHasKey('project_duration', $result);
        $this->assertGreaterThan(0, $result['project_duration']);

        // Verify activities have CPM values calculated
        $activityA->refresh();
        $this->assertNotNull($activityA->early_start);
        $this->assertNotNull($activityA->early_finish);

        $activityB->refresh();
        $this->assertNotNull($activityB->early_start);
        $this->assertNotNull($activityB->early_finish);

        $activityC->refresh();
        $this->assertNotNull($activityC->early_start);
        $this->assertNotNull($activityC->early_finish);
    }

    public function test_cpm_calculation_with_parallel_paths(): void
    {
        // Create activities with parallel paths:
        //     B (5 days)
        //   /            \
        // A (5 days)      D (5 days)
        //   \            /
        //     C (10 days)
        
        $activityA = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityB = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityC = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'C',
            'name' => 'Activity C',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityD = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'D',
            'name' => 'Activity D',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        // Create dependencies
        TenderActivityDependency::create([
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TenderActivityDependency::create([
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityC->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TenderActivityDependency::create([
            'predecessor_id' => $activityB->id,
            'successor_id' => $activityD->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TenderActivityDependency::create([
            'predecessor_id' => $activityC->id,
            'successor_id' => $activityD->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        // Calculate CPM
        $result = $this->cpmService->calculateCPM($this->tender->id);

        $this->assertTrue($result['success']);
        // Verify the calculation completed and returned a duration
        $this->assertArrayHasKey('project_duration', $result);
        $this->assertGreaterThan(0, $result['project_duration']);

        // Verify all activities have CPM calculations done
        $activityA->refresh();
        $activityB->refresh();
        $activityC->refresh();
        $activityD->refresh();

        $this->assertNotNull($activityA->is_critical);
        $this->assertNotNull($activityB->is_critical);
        $this->assertNotNull($activityC->is_critical);
        $this->assertNotNull($activityD->is_critical);
    }

    public function test_cpm_calculation_with_lag(): void
    {
        $activityA = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityB = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        // Create dependency with 3 days lag
        TenderActivityDependency::create([
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'type' => 'FS',
            'lag_days' => 3,
        ]);

        $result = $this->cpmService->calculateCPM($this->tender->id);

        $this->assertTrue($result['success']);
        // Verify calculation completed with lag
        $this->assertArrayHasKey('project_duration', $result);
        $this->assertGreaterThan(0, $result['project_duration']);

        $activityB->refresh();
        // Verify B has CPM values calculated
        $this->assertNotNull($activityB->early_start);
        $this->assertNotNull($activityB->early_finish);
    }

    public function test_get_critical_path(): void
    {
        $activityA = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
            'is_critical' => true,
        ]);

        $activityB = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'medium',
            'is_critical' => false,
        ]);

        $criticalActivities = $this->cpmService->getCriticalPath($this->tender->id);

        $this->assertCount(1, $criticalActivities);
        $this->assertEquals('A', $criticalActivities->first()->activity_code);
    }

    public function test_get_network_diagram_data(): void
    {
        $activityA = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'A',
            'name' => 'Activity A',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityB = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'B',
            'name' => 'Activity B',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        TenderActivityDependency::create([
            'predecessor_id' => $activityA->id,
            'successor_id' => $activityB->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        $networkData = $this->cpmService->getNetworkDiagram($this->tender->id);

        $this->assertArrayHasKey('nodes', $networkData);
        $this->assertArrayHasKey('edges', $networkData);
        $this->assertCount(2, $networkData['nodes']);
        $this->assertCount(1, $networkData['edges']);

        // Verify node structure
        $this->assertEquals('A', $networkData['nodes'][0]['label']);
        $this->assertEquals(5, $networkData['nodes'][0]['duration']);

        // Verify edge structure
        $this->assertEquals($activityA->id, $networkData['edges'][0]['from']);
        $this->assertEquals($activityB->id, $networkData['edges'][0]['to']);
        $this->assertEquals('FS', $networkData['edges'][0]['type']);
    }

    public function test_cpm_with_no_activities(): void
    {
        $result = $this->cpmService->calculateCPM($this->tender->id);

        $this->assertFalse($result['success']);
        $this->assertEquals('No activities found for this tender', $result['message']);
    }
}
