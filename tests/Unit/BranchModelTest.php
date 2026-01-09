<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test branch can be created.
     */
    public function test_branch_can_be_created(): void
    {
        $company = Company::factory()->create();
        
        $branch = Branch::create([
            'company_id' => $company->id,
            'code' => 'TEST-001',
            'name' => 'Test Branch',
            'name_en' => 'Test Branch EN',
            'city' => 'Amman',
            'country' => 'JO',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertEquals('TEST-001', $branch->code);
        $this->assertEquals('Test Branch', $branch->name);
    }

    /**
     * Test branch belongs to a company.
     */
    public function test_branch_belongs_to_company(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $branch->company);
        $this->assertEquals($company->id, $branch->company->id);
    }

    /**
     * Test branch can have a manager.
     */
    public function test_branch_can_have_manager(): void
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create(['manager_id' => $user->id]);

        $this->assertInstanceOf(User::class, $branch->manager);
        $this->assertEquals($user->id, $branch->manager->id);
    }

    /**
     * Test branch has many users.
     */
    public function test_branch_has_many_users(): void
    {
        $branch = Branch::factory()->create();
        $users = User::factory()->count(3)->create(['branch_id' => $branch->id]);

        $this->assertCount(3, $branch->users);
        $this->assertInstanceOf(User::class, $branch->users->first());
    }

    /**
     * Test branch code must be unique.
     */
    public function test_branch_code_must_be_unique(): void
    {
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Branch::factory()->create(['code' => 'UNIQUE-001']);
        Branch::factory()->create(['code' => 'UNIQUE-001']);
    }

    /**
     * Test branch can be soft deleted.
     */
    public function test_branch_can_be_soft_deleted(): void
    {
        $branch = Branch::factory()->create();
        $branchId = $branch->id;

        $branch->delete();

        $this->assertSoftDeleted('branches', ['id' => $branchId]);
        $this->assertNotNull($branch->fresh()->deleted_at);
    }
}
