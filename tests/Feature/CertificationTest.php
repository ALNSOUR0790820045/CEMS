<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test certification code generation.
     */
    public function test_certification_code_is_auto_generated(): void
    {
        $company = Company::factory()->create();

        $certification = Certification::create([
            'certification_name' => 'Test Certification',
            'certification_type' => 'company',
            'entity_type' => 'Company',
            'entity_id' => $company->id,
            'issuing_authority' => 'Test Authority',
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'company_id' => $company->id,
        ]);

        $this->assertNotEmpty($certification->certification_code);
        $this->assertStringStartsWith('CERT-'.date('Y'), $certification->certification_code);
    }

    /**
     * Test certification expiring scope.
     */
    public function test_certification_expiring_scope(): void
    {
        $company = Company::factory()->create();

        // Create an expiring certification
        $expiringCert = Certification::create([
            'certification_name' => 'Expiring Certification',
            'certification_type' => 'company',
            'entity_type' => 'Company',
            'entity_id' => $company->id,
            'issuing_authority' => 'Test Authority',
            'issue_date' => now(),
            'expiry_date' => now()->addDays(20),
            'status' => 'active',
            'company_id' => $company->id,
        ]);

        // Create a non-expiring certification
        Certification::create([
            'certification_name' => 'Valid Certification',
            'certification_type' => 'company',
            'entity_type' => 'Company',
            'entity_id' => $company->id,
            'issuing_authority' => 'Test Authority',
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'active',
            'company_id' => $company->id,
        ]);

        $expiringCertifications = Certification::expiring(30)->get();

        $this->assertCount(1, $expiringCertifications);
        $this->assertEquals($expiringCert->id, $expiringCertifications->first()->id);
    }

    /**
     * Test certification relationships.
     */
    public function test_certification_belongs_to_company(): void
    {
        $company = Company::factory()->create();

        $certification = Certification::create([
            'certification_name' => 'Test Certification',
            'certification_type' => 'company',
            'entity_type' => 'Company',
            'entity_id' => $company->id,
            'issuing_authority' => 'Test Authority',
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'company_id' => $company->id,
        ]);

        $this->assertInstanceOf(Company::class, $certification->company);
        $this->assertEquals($company->id, $certification->company->id);
    }
}
