<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contract;
use App\Models\IPC;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IPCManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $contract;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->contract = Contract::factory()->create([
            'project_id' => $this->project->id,
        ]);
    }

    public function test_user_can_list_ipcs(): void
    {
        IPC::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'ipc_number' => 'IPC-001',
            'ipc_date' => '2026-01-15',
            'amount' => 100000,
            'description' => 'First payment certificate',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/ipcs');

        $response->assertStatus(200);
    }

    public function test_user_can_create_ipc(): void
    {
        $ipcData = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'ipc_number' => 'IPC-001',
            'ipc_date' => '2026-01-15',
            'amount' => 100000,
            'description' => 'First payment certificate',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)
            ->post('/ipcs', $ipcData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('i_p_c_s', [
            'ipc_number' => 'IPC-001',
            'amount' => 100000,
        ]);
    }

    public function test_ipc_requires_contract(): void
    {
        $ipcData = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'ipc_number' => 'IPC-001',
            'amount' => 100000,
            // contract_id is missing
        ];

        $response = $this->actingAs($this->user)
            ->post('/ipcs', $ipcData);

        $response->assertSessionHasErrors(['contract_id']);
    }

    public function test_user_can_view_ipc(): void
    {
        $ipc = IPC::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'ipc_number' => 'IPC-001',
            'ipc_date' => '2026-01-15',
            'amount' => 100000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/ipcs/{$ipc->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_ipc_status(): void
    {
        $ipc = IPC::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'ipc_number' => 'IPC-001',
            'ipc_date' => '2026-01-15',
            'amount' => 100000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->put("/ipcs/{$ipc->id}", [
                'company_id' => $this->company->id,
                'project_id' => $this->project->id,
                'contract_id' => $this->contract->id,
                'ipc_number' => $ipc->ipc_number,
                'ipc_date' => $ipc->ipc_date->format('Y-m-d'),
                'amount' => $ipc->amount,
                'status' => 'submitted',
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('i_p_c_s', [
            'id' => $ipc->id,
            'status' => 'submitted',
        ]);
    }

    public function test_ipc_belongs_to_contract_and_project(): void
    {
        $ipc = IPC::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'contract_id' => $this->contract->id,
            'ipc_number' => 'IPC-001',
            'ipc_date' => '2026-01-15',
            'amount' => 100000,
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(Contract::class, $ipc->contract);
        $this->assertInstanceOf(Project::class, $ipc->project);
        $this->assertEquals($this->contract->id, $ipc->contract->id);
        $this->assertEquals($this->project->id, $ipc->project->id);
    }
}
