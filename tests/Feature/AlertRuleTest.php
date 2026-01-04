<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\AlertRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertRuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_user_can_list_alert_rules()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        AlertRule::factory()->count(3)->create(['company_id' => $company->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/alert-rules');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_create_alert_rule()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/alert-rules', [
                'rule_name' => 'Budget Alert',
                'rule_type' => 'budget_exceeded',
                'trigger_condition' => ['threshold' => 10000],
                'notification_template' => 'Budget exceeded by {amount}',
                'target_users' => [1, 2],
                'target_roles' => [1],
                'is_active' => true,
                'company_id' => $company->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Alert rule created successfully'
            ]);

        $this->assertEquals(1, AlertRule::count());
    }

    public function test_user_can_update_alert_rule()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $alertRule = AlertRule::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/alert-rules/{$alertRule->id}", [
                'rule_name' => 'Updated Alert Rule',
                'is_active' => false,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Alert rule updated successfully'
            ]);

        $this->assertEquals('Updated Alert Rule', $alertRule->fresh()->rule_name);
        $this->assertFalse($alertRule->fresh()->is_active);
    }

    public function test_user_can_delete_alert_rule()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $alertRule = AlertRule::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/alert-rules/{$alertRule->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Alert rule deleted successfully'
            ]);

        $this->assertEquals(0, AlertRule::count());
    }

    public function test_alert_rules_require_authentication()
    {
        $response = $this->getJson('/api/alert-rules');
        $response->assertStatus(401);
    }
}
