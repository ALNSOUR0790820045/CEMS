<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\SiteDiary;
use App\Models\DiaryManpower;
use App\Models\DiaryEquipment;
use App\Models\DiaryActivity;
use App\Models\DiaryIncident;
use App\Models\Subcontractor;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteDiaryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->project = Project::create([
            'name' => 'Test Project',
            'project_code' => 'PROJ001',
            'company_id' => $this->company->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(12),
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_create_a_site_diary()
    {
        $data = [
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'weather_morning' => 'sunny',
            'weather_afternoon' => 'cloudy',
            'temperature_min' => 20.5,
            'temperature_max' => 32.0,
            'humidity' => 65.0,
            'wind_speed' => 15.0,
            'site_condition' => 'dry',
            'work_status' => 'normal',
            'notes' => 'Test diary entry',
        ];

        $response = $this->postJson('/api/site-diaries', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'diary_number',
                    'project_id',
                    'diary_date',
                    'status',
                ]
            ]);

        $this->assertDatabaseHas('site_diaries', [
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'status' => 'draft',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_generates_unique_diary_number()
    {
        $diary1 = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $this->assertNotNull($diary1->diary_number);
        $this->assertStringStartsWith('SD-', $diary1->diary_number);
    }

    /** @test */
    public function it_prevents_duplicate_diary_for_same_project_and_date()
    {
        // Create first diary
        SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        // Try to create duplicate
        $data = [
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
        ];

        $response = $this->postJson('/api/site-diaries', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['diary_date']);
    }

    /** @test */
    public function it_can_add_manpower_to_diary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $data = [
            'trade' => 'carpenter',
            'own_count' => 5,
            'subcontractor_count' => 3,
            'hours_worked' => 8.0,
            'overtime_hours' => 2.0,
            'notes' => 'Working on formwork',
        ];

        $response = $this->postJson("/api/site-diaries/{$diary->id}/manpower", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'trade',
                    'own_count',
                    'subcontractor_count',
                ]
            ]);

        $this->assertDatabaseHas('diary_manpower', [
            'site_diary_id' => $diary->id,
            'trade' => 'carpenter',
            'own_count' => 5,
            'subcontractor_count' => 3,
        ]);
    }

    /** @test */
    public function it_can_add_equipment_to_diary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $data = [
            'equipment_type' => 'Excavator',
            'quantity' => 2,
            'hours_worked' => 8.0,
            'hours_idle' => 1.0,
            'fuel_consumed' => 120.5,
            'operator_name' => 'John Doe',
            'status' => 'working',
        ];

        $response = $this->postJson("/api/site-diaries/{$diary->id}/equipment", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'equipment_type',
                    'quantity',
                ]
            ]);

        $this->assertDatabaseHas('diary_equipment', [
            'site_diary_id' => $diary->id,
            'equipment_type' => 'Excavator',
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_can_add_activity_to_diary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $unit = Unit::create([
            'name' => 'متر مربع',
            'name_en' => 'Square Meter',
            'abbreviation' => 'م²',
            'abbreviation_en' => 'm²',
            'is_active' => true,
        ]);

        $data = [
            'location' => 'Block A, Floor 2',
            'description' => 'صب الخرسانة',
            'description_en' => 'Concrete pouring',
            'quantity_today' => 25.5,
            'unit_id' => $unit->id,
            'cumulative_quantity' => 125.5,
            'percentage_complete' => 45.5,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'status' => 'completed',
        ];

        $response = $this->postJson("/api/site-diaries/{$diary->id}/activities", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'description',
                    'quantity_today',
                ]
            ]);

        $this->assertDatabaseHas('diary_activities', [
            'site_diary_id' => $diary->id,
            'location' => 'Block A, Floor 2',
            'description' => 'صب الخرسانة',
        ]);
    }

    /** @test */
    public function it_can_record_incident()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        $data = [
            'incident_type' => 'near_miss',
            'severity' => 'minor',
            'time_occurred' => '14:30',
            'location' => 'Excavation Area',
            'description' => 'Worker almost stepped into excavated area',
            'persons_involved' => 'Worker ID 123',
            'immediate_action' => 'Area cordoned off with safety tape',
            'reported_to' => 'Site Safety Officer',
            'hse_notified' => true,
            'investigation_required' => true,
        ];

        $response = $this->postJson("/api/site-diaries/{$diary->id}/incidents", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'incident_type',
                    'severity',
                    'description',
                ]
            ]);

        $this->assertDatabaseHas('diary_incidents', [
            'site_diary_id' => $diary->id,
            'incident_type' => 'near_miss',
            'severity' => 'minor',
            'hse_notified' => true,
        ]);
    }

    /** @test */
    public function it_can_submit_diary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/site-diaries/{$diary->id}/submit");

        $response->assertStatus(200);

        $this->assertDatabaseHas('site_diaries', [
            'id' => $diary->id,
            'status' => 'submitted',
        ]);

        $diary->refresh();
        $this->assertNotNull($diary->submitted_at);
    }

    /** @test */
    public function it_can_review_diary()
    {
        $reviewer = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($reviewer);
        $response = $this->postJson("/api/site-diaries/{$diary->id}/review");

        $response->assertStatus(200);

        $this->assertDatabaseHas('site_diaries', [
            'id' => $diary->id,
            'status' => 'reviewed',
            'reviewed_by_id' => $reviewer->id,
        ]);
    }

    /** @test */
    public function it_can_approve_diary()
    {
        $approver = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($approver);
        $response = $this->postJson("/api/site-diaries/{$diary->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('site_diaries', [
            'id' => $diary->id,
            'status' => 'approved',
            'approved_by_id' => $approver->id,
        ]);
    }

    /** @test */
    public function it_cannot_edit_approved_diary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
            'status' => 'approved',
        ]);

        $data = [
            'trade' => 'carpenter',
            'own_count' => 5,
            'subcontractor_count' => 0,
            'hours_worked' => 8.0,
        ];

        $response = $this->postJson("/api/site-diaries/{$diary->id}/manpower", $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_generate_daily_summary()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        // Add manpower
        $diary->manpower()->create([
            'trade' => 'carpenter',
            'own_count' => 5,
            'subcontractor_count' => 3,
            'hours_worked' => 8.0,
        ]);

        // Add equipment
        $diary->equipment()->create([
            'equipment_type' => 'Excavator',
            'quantity' => 1,
            'hours_worked' => 8.0,
            'hours_idle' => 0.0,
        ]);

        $response = $this->getJson("/api/reports/daily-summary/{$this->project->id}?date=" . now()->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'diary',
                'totals' => [
                    'manpower' => [
                        'own',
                        'subcontractor',
                        'total',
                        'total_hours',
                    ],
                    'equipment' => [
                        'count',
                        'total_hours_worked',
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_can_generate_weekly_summary()
    {
        // Create diaries for a week
        for ($i = 0; $i < 7; $i++) {
            $date = now()->startOfWeek()->addDays($i);
            
            $diary = SiteDiary::create([
                'project_id' => $this->project->id,
                'diary_date' => $date->format('Y-m-d'),
                'work_status' => 'normal',
                'company_id' => $this->company->id,
                'prepared_by_id' => $this->user->id,
            ]);

            $diary->manpower()->create([
                'trade' => 'carpenter',
                'own_count' => 5,
                'subcontractor_count' => 3,
                'hours_worked' => 8.0,
            ]);
        }

        $response = $this->getJson("/api/reports/weekly-summary/{$this->project->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period' => [
                    'start_date',
                    'end_date',
                    'total_days',
                ],
                'manpower' => [
                    'daily_average_own',
                    'daily_average_subcontractor',
                    'total_man_hours',
                ],
                'weather',
                'work_status',
            ]);
    }

    /** @test */
    public function it_can_duplicate_diary_from_previous()
    {
        $diary = SiteDiary::create([
            'project_id' => $this->project->id,
            'diary_date' => now()->format('Y-m-d'),
            'weather_morning' => 'sunny',
            'weather_afternoon' => 'cloudy',
            'site_condition' => 'dry',
            'work_status' => 'normal',
            'company_id' => $this->company->id,
            'prepared_by_id' => $this->user->id,
        ]);

        // Add manpower
        $diary->manpower()->create([
            'trade' => 'carpenter',
            'own_count' => 5,
            'subcontractor_count' => 3,
            'hours_worked' => 8.0,
        ]);

        // Add equipment
        $diary->equipment()->create([
            'equipment_type' => 'Excavator',
            'quantity' => 1,
            'operator_name' => 'John Doe',
        ]);

        $response = $this->postJson("/api/site-diaries/{$diary->id}/duplicate");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'diary_number',
                    'manpower',
                    'equipment',
                ]
            ]);

        // Check new diary was created for next day
        $nextDay = now()->addDay()->format('Y-m-d');
        $this->assertDatabaseHas('site_diaries', [
            'project_id' => $this->project->id,
            'diary_date' => $nextDay,
            'status' => 'draft',
        ]);

        // Check manpower was duplicated
        $newDiary = SiteDiary::where('diary_date', $nextDay)->first();
        $this->assertEquals(1, $newDiary->manpower()->count());
        $this->assertEquals(1, $newDiary->equipment()->count());
    }
}
