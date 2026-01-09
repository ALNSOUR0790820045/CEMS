<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\KpiDefinition;
use App\Models\KpiValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KpiApiTest extends TestCase
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

    public function test_can_list_kpi_definitions(): void
    {
        KpiDefinition::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/kpi-definitions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'code', 'name', 'category', 'unit', 'frequency']
                     ]
                 ]);
    }

    public function test_can_create_kpi_definition(): void
    {
        $kpiData = [
            'code' => 'REV-001',
            'name' => 'Revenue Growth',
            'name_en' => 'Revenue Growth EN',
            'description' => 'Monthly revenue growth percentage',
            'category' => 'financial',
            'unit' => 'percentage',
            'target_value' => 10.0,
            'warning_threshold' => 5.0,
            'critical_threshold' => 2.0,
            'frequency' => 'monthly',
        ];

        $response = $this->postJson('/api/kpi-definitions', $kpiData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'KPI definition created successfully',
                     'kpi' => [
                         'code' => 'REV-001',
                         'name' => 'Revenue Growth',
                     ]
                 ]);

        $this->assertDatabaseHas('kpi_definitions', [
            'code' => 'REV-001',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_show_kpi_definition(): void
    {
        $kpi = KpiDefinition::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/kpi-definitions/{$kpi->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $kpi->id,
                     'code' => $kpi->code,
                 ]);
    }

    public function test_can_update_kpi_definition(): void
    {
        $kpi = KpiDefinition::factory()->create(['company_id' => $this->company->id]);

        $updateData = [
            'name' => 'Updated KPI Name',
            'target_value' => 15.0,
        ];

        $response = $this->putJson("/api/kpi-definitions/{$kpi->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'KPI definition updated successfully',
                 ]);

        $this->assertDatabaseHas('kpi_definitions', [
            'id' => $kpi->id,
            'name' => 'Updated KPI Name',
        ]);
    }

    public function test_can_delete_kpi_definition(): void
    {
        $kpi = KpiDefinition::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/kpi-definitions/{$kpi->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'KPI definition deleted successfully',
                 ]);

        $this->assertDatabaseMissing('kpi_definitions', ['id' => $kpi->id]);
    }

    public function test_can_calculate_kpi_value(): void
    {
        $kpi = KpiDefinition::factory()->create([
            'company_id' => $this->company->id,
            'target_value' => 100.0,
            'warning_threshold' => 10.0,
            'critical_threshold' => 20.0,
        ]);

        $valueData = [
            'kpi_definition_id' => $kpi->id,
            'period_date' => '2026-01-01',
            'actual_value' => 95.0,
        ];

        $response = $this->postJson('/api/kpi-values/calculate', $valueData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'KPI value calculated successfully',
                     'kpi_value' => [
                         'actual_value' => '95.00',
                         'target_value' => '100.00',
                     ]
                 ]);

        $this->assertDatabaseHas('kpi_values', [
            'kpi_definition_id' => $kpi->id,
            'period_date' => '2026-01-01',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_calculate_kpi_determines_correct_status(): void
    {
        // Test on_track status
        $kpi = KpiDefinition::factory()->create([
            'company_id' => $this->company->id,
            'target_value' => 100.0,
            'warning_threshold' => 10.0,
            'critical_threshold' => 20.0,
        ]);

        $response = $this->postJson('/api/kpi-values/calculate', [
            'kpi_definition_id' => $kpi->id,
            'period_date' => '2026-01-01',
            'actual_value' => 95.0,
        ]);

        $response->assertStatus(201);
        $kpiValue = KpiValue::first();
        $this->assertEquals('on_track', $kpiValue->status);
    }

    public function test_can_get_kpi_values(): void
    {
        $kpi = KpiDefinition::factory()->create(['company_id' => $this->company->id]);
        KpiValue::factory()->count(3)->create([
            'kpi_definition_id' => $kpi->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/kpi-values?kpi_definition_id=' . $kpi->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'actual_value', 'target_value', 'status']
                     ]
                 ]);
    }

    public function test_kpi_creation_requires_validation(): void
    {
        $response = $this->postJson('/api/kpi-definitions', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code', 'name', 'category', 'unit', 'frequency']);
    }

    public function test_kpi_code_must_be_unique(): void
    {
        KpiDefinition::factory()->create([
            'company_id' => $this->company->id,
            'code' => 'UNIQUE-001'
        ]);

        $response = $this->postJson('/api/kpi-definitions', [
            'code' => 'UNIQUE-001',
            'name' => 'Test KPI',
            'category' => 'financial',
            'unit' => 'percentage',
            'frequency' => 'monthly',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code']);
    }

    public function test_can_filter_kpi_values_by_date_range(): void
    {
        $kpi = KpiDefinition::factory()->create(['company_id' => $this->company->id]);
        
        KpiValue::factory()->create([
            'kpi_definition_id' => $kpi->id,
            'company_id' => $this->company->id,
            'period_date' => '2026-01-01',
        ]);
        
        KpiValue::factory()->create([
            'kpi_definition_id' => $kpi->id,
            'company_id' => $this->company->id,
            'period_date' => '2026-01-15',
        ]);
        
        KpiValue::factory()->create([
            'kpi_definition_id' => $kpi->id,
            'company_id' => $this->company->id,
            'period_date' => '2026-02-01',
        ]);

        $response = $this->getJson('/api/kpi-values?from_date=2026-01-01&to_date=2026-01-31');

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }
}
