<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Company;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CertificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->currency = Currency::factory()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_certifications(): void
    {
        Certification::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/certifications');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'certification_number', 'name', 'type', 'category']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_certification(): void
    {
        $certificationData = [
            'name' => 'Test License',
            'name_en' => 'Test License EN',
            'type' => 'license',
            'category' => 'company',
            'issuing_authority' => 'Government Authority',
            'issue_date' => '2024-01-01',
            'expiry_date' => '2025-01-01',
            'status' => 'active',
            'cost' => 1000.00,
            'currency_id' => $this->currency->id,
            'reminder_days' => 30,
            'company_id' => $this->company->id,
        ];

        $response = $this->postJson('/api/certifications', $certificationData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'certification_number', 'name']
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Test License',
                         'type' => 'license',
                     ]
                 ]);

        $this->assertDatabaseHas('certifications', ['name' => 'Test License']);
    }

    public function test_can_show_certification(): void
    {
        $certification = Certification::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/certifications/{$certification->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $certification->id,
                         'name' => $certification->name,
                     ]
                 ]);
    }

    public function test_can_update_certification(): void
    {
        $certification = Certification::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $updateData = [
            'name' => 'Updated License',
            'status' => 'pending_renewal',
        ];

        $response = $this->putJson("/api/certifications/{$certification->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated License',
                         'status' => 'pending_renewal',
                     ]
                 ]);

        $this->assertDatabaseHas('certifications', [
            'id' => $certification->id,
            'name' => 'Updated License',
        ]);
    }

    public function test_can_delete_certification(): void
    {
        $certification = Certification::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->deleteJson("/api/certifications/{$certification->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertSoftDeleted('certifications', ['id' => $certification->id]);
    }

    public function test_can_get_expiring_certifications(): void
    {
        Certification::factory()->expiring()->count(2)->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        Certification::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'expiry_date' => now()->addYear(),
        ]);

        $response = $this->getJson("/api/certifications/expiring?company_id={$this->company->id}");

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data.data')));
    }

    public function test_can_get_expired_certifications(): void
    {
        Certification::factory()->expired()->count(2)->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/certifications/expired?company_id={$this->company->id}");

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data.data')));
    }

    public function test_can_renew_certification(): void
    {
        $certification = Certification::factory()->create([
            'company_id' => $this->company->id,
            'currency_id' => $this->currency->id,
            'expiry_date' => now()->addMonths(2),
        ]);

        $renewalData = [
            'new_expiry_date' => now()->addYear()->format('Y-m-d'),
            'renewal_cost' => 500.00,
            'notes' => 'Renewal test',
        ];

        $response = $this->postJson("/api/certifications/{$certification->id}/renew", $renewalData);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('certification_renewals', [
            'certification_id' => $certification->id,
        ]);

        $certification->refresh();
        $this->assertEquals($renewalData['new_expiry_date'], $certification->expiry_date->format('Y-m-d'));
    }
}
