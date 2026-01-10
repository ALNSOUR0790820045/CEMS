<?php

namespace Tests\Unit\Services;

use App\Models\Company;
use App\Models\TimeBarProtectionSetting;
use App\Models\User;
use App\Services\TimeBarProtectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeBarProtectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TimeBarProtectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimeBarProtectionService();
    }

    public function test_get_setting_returns_null_when_no_setting_exists(): void
    {
        $setting = $this->service->getSetting('invoice', null);
        
        $this->assertNull($setting);
    }

    public function test_get_setting_returns_global_setting(): void
    {
        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $setting = $this->service->getSetting('invoice', null);
        
        $this->assertNotNull($setting);
        $this->assertEquals('invoice', $setting->entity_type);
    }

    public function test_get_setting_prioritizes_company_specific_setting(): void
    {
        $company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);

        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        TimeBarProtectionSetting::create([
            'company_id' => $company->id,
            'entity_type' => 'invoice',
            'protection_days' => 60,
            'protection_type' => 'full_lock',
            'is_active' => true,
        ]);

        $setting = $this->service->getSetting('invoice', $company->id);
        
        $this->assertNotNull($setting);
        $this->assertEquals($company->id, $setting->company_id);
        $this->assertEquals(60, $setting->protection_days);
    }

    public function test_is_protected_returns_false_when_no_setting(): void
    {
        $createdAt = Carbon::now()->subDays(60);
        
        $isProtected = $this->service->isProtected('invoice', $createdAt, null);
        
        $this->assertFalse($isProtected);
    }

    public function test_is_protected_returns_false_for_recent_records(): void
    {
        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'invoice',
            'protection_days' => 90,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $createdAt = Carbon::now()->subDays(30);
        
        $isProtected = $this->service->isProtected('invoice', $createdAt, null);
        
        $this->assertFalse($isProtected);
    }

    public function test_is_protected_returns_true_for_old_records(): void
    {
        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $createdAt = Carbon::now()->subDays(60);
        
        $isProtected = $this->service->isProtected('invoice', $createdAt, null);
        
        $this->assertTrue($isProtected);
    }

    public function test_get_protection_info_returns_correct_data(): void
    {
        TimeBarProtectionSetting::create([
            'company_id' => null,
            'entity_type' => 'invoice',
            'protection_days' => 30,
            'protection_type' => 'view_only',
            'is_active' => true,
        ]);

        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $table = 'test_models';
            public $timestamps = true;
            public $company_id = null;
            
            public function __construct(array $attributes = [])
            {
                parent::__construct($attributes);
                $this->created_at = Carbon::now()->subDays(60);
            }
        };

        $info = $this->service->getProtectionInfo($model, 'invoice');
        
        $this->assertArrayHasKey('is_protected', $info);
        $this->assertArrayHasKey('can_bypass', $info);
        $this->assertArrayHasKey('can_edit', $info);
        $this->assertArrayHasKey('can_delete', $info);
        $this->assertArrayHasKey('protection_type', $info);
        $this->assertArrayHasKey('protection_days', $info);
        $this->assertArrayHasKey('days_since_creation', $info);
        
        $this->assertTrue($info['is_protected']);
        $this->assertEquals('view_only', $info['protection_type']);
        $this->assertEquals(30, $info['protection_days']);
    }
}
