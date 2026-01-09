<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ContractTemplate;
use App\Models\ContractTemplateClause;
use App\Models\ContractTemplateVariable;

class ContractTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Seed contract templates
        $this->artisan('db:seed', ['--class' => 'ContractTemplateSeeder']);
    }

    public function test_user_can_view_contract_templates_index(): void
    {
        $response = $this->actingAs($this->user)->get('/contract-templates');

        $response->assertStatus(200);
        $response->assertSee('قوالب العقود');
    }

    public function test_user_can_view_specific_template(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->get("/contract-templates/{$template->id}");

        $response->assertStatus(200);
        $response->assertSee($template->name);
    }

    public function test_user_can_view_template_clauses(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->get("/contract-templates/{$template->id}/clauses");

        $response->assertStatus(200);
        $response->assertSee('بنود');
    }

    public function test_user_can_view_generate_contract_form(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->get("/contract-templates/{$template->id}/generate");

        $response->assertStatus(200);
        $response->assertSee('إنشاء عقد');
    }

    public function test_user_can_view_jea01_template(): void
    {
        $response = $this->actingAs($this->user)->get('/contract-templates/jea-01');

        $response->assertStatus(200);
        $response->assertSee('JEA-01');
    }

    public function test_user_can_view_jea02_template(): void
    {
        $response = $this->actingAs($this->user)->get('/contract-templates/jea-02');

        $response->assertStatus(200);
        $response->assertSee('JEA-02');
    }

    public function test_api_returns_contract_templates(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/contract-templates');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'name',
                    'type',
                    'is_active'
                ]
            ]
        ]);
    }

    public function test_api_returns_specific_template(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->getJson("/api/contract-templates/{$template->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'code',
                'name',
                'clauses',
                'variables',
                'special_conditions'
            ]
        ]);
    }

    public function test_api_returns_template_clauses(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->getJson("/api/contract-templates/{$template->id}/clauses");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'clause_number',
                    'title',
                    'content',
                    'category'
                ]
            ]
        ]);
    }

    public function test_api_returns_template_variables(): void
    {
        $template = ContractTemplate::first();

        $response = $this->actingAs($this->user)->getJson("/api/contract-templates/{$template->id}/variables");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'variable_key',
                    'variable_label',
                    'data_type',
                    'is_required'
                ]
            ]
        ]);
    }

    public function test_user_can_generate_contract_from_template(): void
    {
        $template = ContractTemplate::first();

        $contractData = [
            'template_id' => $template->id,
            'contract_title' => 'Test Contract',
            'parties' => [
                'employer_name' => 'Test Employer',
                'contractor_name' => 'Test Contractor'
            ],
            'filled_data' => [
                '{{employer_name}}' => 'Test Employer',
                '{{contractor_name}}' => 'Test Contractor',
                '{{contract_value}}' => '100000'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post('/contract-templates/generate-contract', $contractData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('contract_generated', [
            'contract_title' => 'Test Contract',
            'generated_by' => $this->user->id
        ]);
    }

    public function test_api_can_generate_contract_from_template(): void
    {
        $template = ContractTemplate::first();

        $contractData = [
            'contract_title' => 'API Test Contract',
            'parties' => [
                'employer_name' => 'API Employer',
                'contractor_name' => 'API Contractor'
            ],
            'filled_data' => [
                '{{employer_name}}' => 'API Employer',
                '{{contractor_name}}' => 'API Contractor'
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/contract-templates/{$template->id}/generate", $contractData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'contract_title',
                'status'
            ]
        ]);
    }

    public function test_guest_cannot_access_contract_templates(): void
    {
        $response = $this->get('/contract-templates');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_api_endpoints(): void
    {
        $response = $this->getJson('/api/contract-templates');
        $response->assertStatus(401);
    }
}
