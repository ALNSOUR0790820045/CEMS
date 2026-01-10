<?php

namespace Tests\Feature;

use App\Models\PunchList;
use App\Models\PunchItem;
use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PunchListTest extends TestCase
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

    public function test_can_create_punch_list(): void
    {
        $contractor = Vendor::factory()->create();

        $response = $this->postJson('/api/punch-lists', [
            'project_id' => $this->project->id,
            'name' => 'Building A Pre-Handover Inspection',
            'description' => 'Pre-handover inspection for Building A',
            'list_type' => 'pre_handover',
            'area_zone' => 'Zone 1',
            'building' => 'Building A',
            'floor' => 'Ground Floor',
            'discipline' => 'architectural',
            'contractor_id' => $contractor->id,
            'inspection_date' => now()->format('Y-m-d'),
            'inspector_id' => $this->user->id,
            'target_completion_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'list_number',
                         'project_id',
                         'name',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('punch_lists', [
            'name' => 'Building A Pre-Handover Inspection',
            'status' => 'draft',
        ]);
    }

    public function test_can_list_punch_lists(): void
    {
        PunchList::factory()->count(3)->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->getJson('/api/punch-lists');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'list_number', 'name', 'status']
                     ]
                 ]);
    }

    public function test_can_show_punch_list(): void
    {
        $list = PunchList::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->getJson("/api/punch-lists/{$list->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $list->id,
                     'list_number' => $list->list_number,
                 ]);
    }

    public function test_can_update_punch_list(): void
    {
        $list = PunchList::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'Original Name',
        ]);

        $response = $this->putJson("/api/punch-lists/{$list->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_lists', [
            'id' => $list->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_punch_list(): void
    {
        $list = PunchList::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->deleteJson("/api/punch-lists/{$list->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('punch_lists', [
            'id' => $list->id,
        ]);
    }

    public function test_can_issue_punch_list(): void
    {
        $list = PunchList::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/punch-lists/{$list->id}/issue");

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_lists', [
            'id' => $list->id,
            'status' => 'issued',
        ]);
    }

    public function test_can_get_punch_lists_by_project(): void
    {
        $otherProject = Project::factory()->create(['company_id' => $this->company->id]);

        PunchList::factory()->count(2)->create(['project_id' => $this->project->id]);
        PunchList::factory()->count(1)->create(['project_id' => $otherProject->id]);

        $response = $this->getJson("/api/punch-lists/project/{$this->project->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_list_statistics_update_when_items_added(): void
    {
        $list = PunchList::factory()->create([
            'project_id' => $this->project->id,
        ]);

        PunchItem::factory()->count(5)->create([
            'punch_list_id' => $list->id,
            'status' => 'open',
        ]);

        PunchItem::factory()->count(3)->create([
            'punch_list_id' => $list->id,
            'status' => 'completed',
        ]);

        $list->updateStatistics();

        $this->assertEquals(8, $list->total_items);
        $this->assertEquals(3, $list->completed_items);
        $this->assertEquals(5, $list->pending_items);
        $this->assertEquals(37.5, $list->completion_percentage);
    }
}
