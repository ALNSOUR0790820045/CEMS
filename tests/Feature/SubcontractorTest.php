<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Subcontractor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubcontractorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company and user for testing
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_list_subcontractors(): void
    {
        Subcontractor::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/subcontractors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'subcontractor_code',
                        'name',
                        'subcontractor_type',
                        'trade_category',
                    ]
                ]
            ]);
    }

    public function test_can_create_subcontractor(): void
    {
        $data = [
            'name' => 'Test Subcontractor',
            'name_en' => 'Test Subcontractor EN',
            'subcontractor_type' => 'specialized',
            'trade_category' => 'electrical',
            'payment_terms' => '30_days',
            'email' => 'test@subcontractor.com',
            'phone' => '1234567890',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/subcontractors', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Subcontractor']);

        $this->assertDatabaseHas('subcontractors', [
            'name' => 'Test Subcontractor',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_view_subcontractor(): void
    {
        $subcontractor = Subcontractor::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/subcontractors/{$subcontractor->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $subcontractor->name]);
    }

    public function test_can_update_subcontractor(): void
    {
        $subcontractor = Subcontractor::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $updateData = [
            'name' => 'Updated Subcontractor Name',
            'subcontractor_type' => 'general',
            'trade_category' => 'civil',
            'payment_terms' => '45_days',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/subcontractors/{$subcontractor->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Subcontractor Name']);

        $this->assertDatabaseHas('subcontractors', [
            'id' => $subcontractor->id,
            'name' => 'Updated Subcontractor Name',
        ]);
    }

    public function test_can_delete_subcontractor(): void
    {
        $subcontractor = Subcontractor::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/subcontractors/{$subcontractor->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('subcontractors', [
            'id' => $subcontractor->id,
        ]);
    }

    public function test_can_approve_subcontractor(): void
    {
        $subcontractor = Subcontractor::factory()->create([
            'company_id' => $this->company->id,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/subcontractors/{$subcontractor->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('subcontractors', [
            'id' => $subcontractor->id,
            'is_approved' => true,
        ]);
    }

    public function test_subcontractor_code_is_auto_generated(): void
    {
        $subcontractor = Subcontractor::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->assertNotNull($subcontractor->subcontractor_code);
        $this->assertStringStartsWith('SUB-', $subcontractor->subcontractor_code);
    }
}

