<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
use App\Models\TimeBarProtectionSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeBarProtectionSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_list_all_settings(): void
    {
        TimeBarProtectionSetting::factory()->count(3)->create();

        $response = $this->getJson('/api/time-bar-protection');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_setting(): void
    {
        $data = [
            'entity_type' => 'invoice',
            'protection_days' => 90,
            'protection_type' => 'view_only',
            'is_active' => true,
            'description' => 'Test protection setting',
        ];

        $response = $this->postJson('/api/time-bar-protection', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('data.entity_type', 'invoice')
                 ->assertJsonPath('data.protection_days', 90);

        $this->assertDatabaseHas('time_bar_protection_settings', [
            'entity_type' => 'invoice',
            'protection_days' => 90,
        ]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $data = [
            'entity_type' => '',
            'protection_days' => -10,
            'protection_type' => 'invalid_type',
        ];

        $response = $this->postJson('/api/time-bar-protection', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['entity_type', 'protection_days', 'protection_type']);
    }

    public function test_can_update_setting(): void
    {
        $setting = TimeBarProtectionSetting::create([
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $data = [
            'entity_type' => 'invoice',
            'protection_days' => 60,
            'protection_type' => 'full_lock',
            'is_active' => true,
        ];

        $response = $this->putJson("/api/time-bar-protection/{$setting->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonPath('data.protection_days', 60)
                 ->assertJsonPath('data.protection_type', 'full_lock');

        $this->assertDatabaseHas('time_bar_protection_settings', [
            'id' => $setting->id,
            'protection_days' => 60,
            'protection_type' => 'full_lock',
        ]);
    }

    public function test_can_delete_setting(): void
    {
        $setting = TimeBarProtectionSetting::create([
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/time-bar-protection/{$setting->id}");

        $response->assertStatus(200);
        
        $this->assertSoftDeleted('time_bar_protection_settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_can_toggle_active_status(): void
    {
        $setting = TimeBarProtectionSetting::create([
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $response = $this->patchJson("/api/time-bar-protection/{$setting->id}/toggle-active");

        $response->assertStatus(200)
                 ->assertJsonPath('is_active', false);

        $this->assertDatabaseHas('time_bar_protection_settings', [
            'id' => $setting->id,
            'is_active' => false,
        ]);
    }

    public function test_can_check_protection_status(): void
    {
        TimeBarProtectionSetting::create([
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $data = [
            'entity_type' => 'invoice',
            'created_at' => now()->subDays(60)->toIso8601String(),
        ];

        $response = $this->postJson('/api/time-bar-protection/check', $data);

        $response->assertStatus(200)
                 ->assertJsonPath('is_protected', true)
                 ->assertJsonPath('protection_type', 'view_only');
    }

    public function test_can_filter_by_company(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);

        TimeBarProtectionSetting::create([
            'company_id' => $company->id,
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'contract',
            'protection_days' => 60,
            'protection_type' => 'full_lock',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/time-bar-protection?company_id={$company->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.company_id', $company->id);
    }
}
