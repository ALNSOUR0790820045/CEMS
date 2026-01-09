<?php

namespace Tests\Feature;

use App\Models\AlertRule;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AlertRuleTest extends TestCase
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

    public function test_can_list_alert_rules(): void
    {
        AlertRule::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/alert-rules');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'event_type', 'is_active']
                         ]
                     ]
                 ]);
    }

    public function test_can_create_alert_rule(): void
    {
        $data = [
            'name' => 'Budget Alert',
            'event_type' => 'budget_exceeded',
            'recipients_type' => 'user',
            'recipients_ids' => [$this->user->id],
            'channels' => ['email', 'in_app'],
            'is_active' => true,
        ];

        $response = $this->postJson('/api/alert-rules', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Budget Alert',
                         'event_type' => 'budget_exceeded',
                     ]
                 ]);

        $this->assertDatabaseHas('alert_rules', [
            'name' => 'Budget Alert',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_show_alert_rule(): void
    {
        $rule = AlertRule::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson("/api/alert-rules/{$rule->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $rule->id,
                         'name' => $rule->name,
                     ]
                 ]);
    }

    public function test_can_update_alert_rule(): void
    {
        $rule = AlertRule::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $data = [
            'name' => 'Updated Alert Rule',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/alert-rules/{$rule->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Alert Rule',
                         'is_active' => false,
                     ]
                 ]);
    }

    public function test_can_delete_alert_rule(): void
    {
        $rule = AlertRule::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/alert-rules/{$rule->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('alert_rules', ['id' => $rule->id]);
    }

    public function test_can_toggle_alert_rule(): void
    {
        $rule = AlertRule::factory()->create([
            'company_id' => $this->company->id,
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/alert-rules/{$rule->id}/toggle");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'is_active' => false,
                 ]);

        $this->assertFalse($rule->fresh()->is_active);
    }

    public function test_can_test_alert_rule(): void
    {
        $rule = AlertRule::factory()->create([
            'company_id' => $this->company->id,
            'recipients_type' => 'user',
            'recipients_ids' => [$this->user->id],
        ]);

        $response = $this->postJson("/api/alert-rules/{$rule->id}/test", [
            'test_data' => [],
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'matches',
                     'recipients_count',
                     'recipients',
                 ]);
    }

    public function test_alert_rule_requires_validation(): void
    {
        $response = $this->postJson('/api/alert-rules', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'event_type', 'recipients_type']);
    }
}
