<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderWBS;
use App\Models\TenderActivityDependency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenderActivityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $tender;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Create a test tender
        $this->tender = Tender::create([
            'tender_code' => 'TND-001',
            'name' => 'Test Tender',
            'description' => 'Test tender description',
            'status' => 'draft',
        ]);
    }

    public function test_can_view_activities_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('tender-activities.index', $this->tender));

        $response->assertStatus(200);
        $response->assertViewIs('tender-activities.index');
        $response->assertViewHas('tender');
        $response->assertViewHas('activities');
    }

    public function test_can_create_activity(): void
    {
        $activityData = [
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'name_en' => 'Test Activity EN',
            'description' => 'Test description',
            'duration_days' => 10,
            'effort_hours' => 80,
            'type' => 'task',
            'priority' => 'high',
            'estimated_cost' => 5000,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tender-activities.store', $this->tender), $activityData);

        $response->assertRedirect(route('tender-activities.index', $this->tender));
        $this->assertDatabaseHas('tender_activities', [
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'tender_id' => $this->tender->id,
        ]);
    }

    public function test_can_update_activity(): void
    {
        $activity = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'Original Name',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $updateData = [
            'activity_code' => 'TACT-001',
            'name' => 'Updated Name',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('tender-activities.update', [$this->tender, $activity]), $updateData);

        $response->assertRedirect(route('tender-activities.index', $this->tender));
        $this->assertDatabaseHas('tender_activities', [
            'id' => $activity->id,
            'name' => 'Updated Name',
            'duration_days' => 10,
            'priority' => 'high',
        ]);
    }

    public function test_can_delete_activity(): void
    {
        $activity = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('tender-activities.destroy', [$this->tender, $activity]));

        $response->assertRedirect(route('tender-activities.index', $this->tender));
        $this->assertDatabaseMissing('tender_activities', [
            'id' => $activity->id,
        ]);
    }

    public function test_can_create_activity_with_dependencies(): void
    {
        $activity1 = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'First Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $activityData = [
            'activity_code' => 'TACT-002',
            'name' => 'Second Activity',
            'duration_days' => 10,
            'type' => 'task',
            'priority' => 'high',
            'predecessors' => [
                [
                    'id' => $activity1->id,
                    'type' => 'FS',
                    'lag_days' => 0,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tender-activities.store', $this->tender), $activityData);

        $response->assertRedirect(route('tender-activities.index', $this->tender));
        
        $activity2 = TenderActivity::where('activity_code', 'TACT-002')->first();
        $this->assertNotNull($activity2);
        
        $this->assertDatabaseHas('tender_activity_dependencies', [
            'predecessor_id' => $activity1->id,
            'successor_id' => $activity2->id,
            'type' => 'FS',
        ]);
    }

    public function test_can_view_gantt_chart(): void
    {
        TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tender-activities.gantt', $this->tender));

        $response->assertStatus(200);
        $response->assertViewIs('tender-activities.gantt');
        $response->assertViewHas('tender');
        $response->assertViewHas('activities');
        $response->assertViewHas('dependencies');
    }

    public function test_can_view_cpm_analysis(): void
    {
        TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tender-activities.cpm-analysis', $this->tender));

        $response->assertStatus(200);
        $response->assertViewIs('tender-activities.cpm-analysis');
        $response->assertViewHas('tender');
        $response->assertViewHas('activities');
        $response->assertViewHas('criticalActivities');
        $response->assertViewHas('cpmResult');
    }

    public function test_activity_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('tender-activities.store', $this->tender), []);

        $response->assertSessionHasErrors(['activity_code', 'name', 'duration_days', 'type', 'priority']);
    }

    public function test_activity_code_must_be_unique(): void
    {
        TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'First Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('tender-activities.store', $this->tender), [
                'activity_code' => 'TACT-001',
                'name' => 'Second Activity',
                'duration_days' => 10,
                'type' => 'task',
                'priority' => 'high',
            ]);

        $response->assertSessionHasErrors(['activity_code']);
    }

    public function test_can_view_edit_form(): void
    {
        $activity = TenderActivity::create([
            'tender_id' => $this->tender->id,
            'activity_code' => 'TACT-001',
            'name' => 'Test Activity',
            'duration_days' => 5,
            'type' => 'task',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tender-activities.edit', [$this->tender, $activity]));

        $response->assertStatus(200);
        $response->assertViewIs('tender-activities.edit');
        $response->assertViewHas('activity');
        $response->assertViewHas('wbsItems');
        $response->assertViewHas('activities');
    }
}
