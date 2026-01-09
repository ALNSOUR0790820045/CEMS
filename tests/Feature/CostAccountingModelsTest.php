<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CostCenter;
use App\Models\CostCategory;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\CostAllocation;

class CostAccountingModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cost_category_model_exists()
    {
        $this->assertTrue(class_exists(CostCategory::class));
    }

    public function test_cost_allocation_model_exists()
    {
        $this->assertTrue(class_exists(CostAllocation::class));
    }

    public function test_budget_model_exists()
    {
        $this->assertTrue(class_exists(Budget::class));
    }

    public function test_budget_item_model_exists()
    {
        $this->assertTrue(class_exists(BudgetItem::class));
    }

    public function test_cost_allocation_can_generate_number()
    {
        $allocationNumber = CostAllocation::generateAllocationNumber();
        $this->assertMatchesRegularExpression('/CA-\d{4}-\d{4}/', $allocationNumber);
    }

    public function test_budget_can_generate_number()
    {
        $budgetNumber = Budget::generateBudgetNumber(2026);
        $this->assertMatchesRegularExpression('/BDG-\d{4}-\d{4}/', $budgetNumber);
    }

    public function test_budget_item_variance_percentage_calculation()
    {
        $item = new BudgetItem([
            'budgeted_amount' => 100,
            'actual_amount' => 80,
            'variance' => 20,
        ]);

        $this->assertEquals(20, $item->variance_percentage);
    }
}
