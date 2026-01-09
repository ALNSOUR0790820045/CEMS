<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
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

    public function test_can_list_dashboards(): void
    {
        Dashboard::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/dashboards');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'type', 'is_default', 'is_public']
                     ]
                 ]);
    }

    public function test_can_create_dashboard(): void
    {
        $dashboardData = [
            'name' => 'Test Dashboard',
            'name_en' => 'Test Dashboard EN',
            'description' => 'Test Description',
            'type' => 'executive',
            'is_default' => false,
            'is_public' => true,
        ];

        $response = $this->postJson('/api/dashboards', $dashboardData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Dashboard created successfully',
                     'dashboard' => [
                         'name' => 'Test Dashboard',
                         'type' => 'executive',
                     ]
                 ]);

        $this->assertDatabaseHas('dashboards', [
            'name' => 'Test Dashboard',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_show_dashboard(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/dashboards/{$dashboard->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $dashboard->id,
                     'name' => $dashboard->name,
                 ]);
    }

    public function test_can_update_dashboard(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);

        $updateData = [
            'name' => 'Updated Dashboard Name',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("/api/dashboards/{$dashboard->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Dashboard updated successfully',
                 ]);

        $this->assertDatabaseHas('dashboards', [
            'id' => $dashboard->id,
            'name' => 'Updated Dashboard Name',
        ]);
    }

    public function test_can_delete_dashboard(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/dashboards/{$dashboard->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Dashboard deleted successfully',
                 ]);

        $this->assertSoftDeleted('dashboards', ['id' => $dashboard->id]);
    }

    public function test_can_add_widget_to_dashboard(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);

        $widgetData = [
            'widget_type' => 'chart',
            'title' => 'Test Widget',
            'data_source' => 'revenue',
            'position_x' => 0,
            'position_y' => 0,
            'width' => 6,
            'height' => 4,
        ];

        $response = $this->postJson("/api/dashboards/{$dashboard->id}/widgets", $widgetData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Widget added successfully',
                     'widget' => [
                         'title' => 'Test Widget',
                         'widget_type' => 'chart',
                     ]
                 ]);

        $this->assertDatabaseHas('dashboard_widgets', [
            'dashboard_id' => $dashboard->id,
            'title' => 'Test Widget',
        ]);
    }

    public function test_can_get_dashboard_widgets(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);
        $dashboard->widgets()->createMany([
            [
                'widget_type' => 'chart',
                'title' => 'Widget 1',
                'position_x' => 0,
                'position_y' => 0,
                'width' => 6,
                'height' => 4,
            ],
            [
                'widget_type' => 'kpi',
                'title' => 'Widget 2',
                'position_x' => 6,
                'position_y' => 0,
                'width' => 6,
                'height' => 4,
            ],
        ]);

        $response = $this->getJson("/api/dashboards/{$dashboard->id}/widgets");

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_can_update_dashboard_layout(): void
    {
        $dashboard = Dashboard::factory()->create(['company_id' => $this->company->id]);

        $layoutData = [
            'layout' => [
                ['x' => 0, 'y' => 0, 'w' => 6, 'h' => 4],
                ['x' => 6, 'y' => 0, 'w' => 6, 'h' => 4],
            ]
        ];

        $response = $this->putJson("/api/dashboards/{$dashboard->id}/layout", $layoutData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Dashboard layout updated successfully',
                 ]);

        $this->assertDatabaseHas('dashboards', [
            'id' => $dashboard->id,
        ]);
    }

    public function test_dashboard_creation_requires_validation(): void
    {
        $response = $this->postJson('/api/dashboards', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'type']);
    }

    public function test_setting_default_dashboard_unsets_other_defaults(): void
    {
        // Create an existing default dashboard
        Dashboard::factory()->create([
            'company_id' => $this->company->id,
            'type' => 'executive',
            'is_default' => true,
        ]);

        // Create a new default dashboard of the same type
        $response = $this->postJson('/api/dashboards', [
            'name' => 'New Default Dashboard',
            'type' => 'executive',
            'is_default' => true,
        ]);

        $response->assertStatus(201);

        // Check that only one default exists for this type
        $this->assertEquals(1, Dashboard::where('company_id', $this->company->id)
            ->where('type', 'executive')
            ->where('is_default', true)
            ->count());
    }
}
