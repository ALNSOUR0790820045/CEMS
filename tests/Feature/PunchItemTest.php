<?php

namespace Tests\Feature;

use App\Models\PunchList;
use App\Models\PunchItem;
use App\Models\PunchItemComment;
use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PunchItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected PunchList $punchList;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->punchList = PunchList::factory()->create(['project_id' => $this->project->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_punch_item(): void
    {
        $response = $this->postJson('/api/punch-items', [
            'punch_list_id' => $this->punchList->id,
            'location' => 'Room 101',
            'description' => 'Wall paint chipped',
            'category' => 'defect',
            'severity' => 'minor',
            'discipline' => 'architectural',
            'priority' => 'medium',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'item_number',
                         'description',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('punch_items', [
            'description' => 'Wall paint chipped',
            'status' => 'open',
        ]);
    }

    public function test_can_assign_punch_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
        ]);

        $assignee = User::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/punch-items/{$item->id}/assign", [
            'assigned_to_id' => $assignee->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_items', [
            'id' => $item->id,
            'assigned_to_id' => $assignee->id,
        ]);
    }

    public function test_can_complete_punch_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'in_progress',
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/complete", [
            'completion_remarks' => 'Fixed the wall paint',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_items', [
            'id' => $item->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_verify_punch_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'completed',
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/verify");

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_items', [
            'id' => $item->id,
            'status' => 'verified',
        ]);
    }

    public function test_can_reject_punch_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'completed',
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/reject", [
            'rejection_reason' => 'Work quality not acceptable',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_items', [
            'id' => $item->id,
            'status' => 'rejected',
            'rejection_reason' => 'Work quality not acceptable',
        ]);
    }

    public function test_can_reopen_punch_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'rejected',
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/reopen");

        $response->assertStatus(200);

        $this->assertDatabaseHas('punch_items', [
            'id' => $item->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_can_add_comment_to_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/comments", [
            'comment' => 'This needs immediate attention',
            'comment_type' => 'note',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('punch_item_comments', [
            'punch_item_id' => $item->id,
            'comment' => 'This needs immediate attention',
        ]);
    }

    public function test_can_get_item_comments(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
        ]);

        PunchItemComment::factory()->count(3)->create([
            'punch_item_id' => $item->id,
        ]);

        $response = $this->getJson("/api/punch-items/{$item->id}/comments");

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_get_item_history(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
        ]);

        // Create history entry
        $item->addHistory('created', null, 'open', $this->user->id, 'Item created');

        $response = $this->getJson("/api/punch-items/{$item->id}/history");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'action', 'performed_by_id']
                 ]);
    }

    public function test_can_bulk_assign_items(): void
    {
        $items = PunchItem::factory()->count(3)->create([
            'punch_list_id' => $this->punchList->id,
        ]);

        $assignee = User::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson('/api/punch-items/bulk-assign', [
            'item_ids' => $items->pluck('id')->toArray(),
            'assigned_to_id' => $assignee->id,
        ]);

        $response->assertStatus(200);

        foreach ($items as $item) {
            $this->assertDatabaseHas('punch_items', [
                'id' => $item->id,
                'assigned_to_id' => $assignee->id,
            ]);
        }
    }

    public function test_item_workflow_from_open_to_verified(): void
    {
        // Create item
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'open',
        ]);

        // Complete item
        $this->postJson("/api/punch-items/{$item->id}/complete");
        $item->refresh();
        $this->assertEquals('completed', $item->status);

        // Verify item
        $this->postJson("/api/punch-items/{$item->id}/verify");
        $item->refresh();
        $this->assertEquals('verified', $item->status);
    }

    public function test_cannot_verify_non_completed_item(): void
    {
        $item = PunchItem::factory()->create([
            'punch_list_id' => $this->punchList->id,
            'status' => 'open',
        ]);

        $response = $this->postJson("/api/punch-items/{$item->id}/verify");

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => 'Only completed items can be verified'
                 ]);
    }
}
