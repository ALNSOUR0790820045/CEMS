<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use App\Models\ShiftSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $employee;
    protected $shiftSchedule;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a company
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
            'is_active' => true,
        ]);

        // Create a user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'company_id' => $this->company->id,
        ]);

        // Create a shift schedule
        $this->shiftSchedule = ShiftSchedule::create([
            'shift_name' => 'Morning Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'grace_period_minutes' => 15,
            'working_hours' => 8.0,
            'is_active' => true,
            'company_id' => $this->company->id,
        ]);

        // Create an employee
        $this->employee = Employee::create([
            'user_id' => $this->user->id,
            'employee_number' => 'EMP001',
            'hire_date' => now()->subYear(),
            'department' => 'IT',
            'position' => 'Developer',
            'employment_type' => 'full_time',
            'shift_schedule_id' => $this->shiftSchedule->id,
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_employee_model_has_relationships()
    {
        $this->assertInstanceOf(User::class, $this->employee->user);
        $this->assertInstanceOf(Company::class, $this->employee->company);
        $this->assertInstanceOf(ShiftSchedule::class, $this->employee->shiftSchedule);
    }

    public function test_shift_schedule_model_has_relationships()
    {
        $this->assertInstanceOf(Company::class, $this->shiftSchedule->company);
        $this->assertTrue($this->shiftSchedule->employees->contains($this->employee));
    }

    public function test_can_retrieve_employees_list()
    {
        $response = $this->getJson("/api/employees?company_id={$this->company->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'employee_number',
                    'department',
                    'position',
                    'status',
                ]
            ]
        ]);
    }

    public function test_can_retrieve_shift_schedules_list()
    {
        $response = $this->getJson("/api/shift-schedules?company_id={$this->company->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'shift_name',
                    'start_time',
                    'end_time',
                    'working_hours',
                ]
            ]
        ]);
    }

    public function test_attendance_record_model_creates_successfully()
    {
        $attendance = \App\Models\AttendanceRecord::create([
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->format('Y-m-d'),
            'check_in_time' => now()->setTime(9, 0, 0),
            'status' => 'present',
            'company_id' => $this->company->id,
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'employee_id' => $this->employee->id,
            'status' => 'present',
        ]);

        $this->assertInstanceOf(Employee::class, $attendance->employee);
    }

    public function test_leave_request_model_creates_successfully()
    {
        $leaveRequest = \App\Models\LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(10),
            'total_days' => 4,
            'reason' => 'Vacation',
            'status' => 'pending',
            'company_id' => $this->company->id,
        ]);

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(Employee::class, $leaveRequest->employee);
    }

    public function test_leave_request_can_calculate_total_days()
    {
        $leaveRequest = \App\Models\LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'sick',
            'start_date' => '2026-01-10',
            'end_date' => '2026-01-15',
            'total_days' => 0, // Will be calculated
            'reason' => 'Medical leave',
            'status' => 'pending',
            'company_id' => $this->company->id,
        ]);

        $leaveRequest->calculateTotalDays();
        
        $this->assertEquals(6, $leaveRequest->total_days);
    }

    public function test_leave_request_can_be_approved()
    {
        $leaveRequest = \App\Models\LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(10),
            'total_days' => 4,
            'reason' => 'Vacation',
            'status' => 'pending',
            'company_id' => $this->company->id,
        ]);

        $leaveRequest->approve($this->user->id);

        $this->assertEquals('approved', $leaveRequest->status);
        $this->assertEquals($this->user->id, $leaveRequest->approved_by_id);
        $this->assertNotNull($leaveRequest->approved_at);
    }

    public function test_models_have_proper_scopes()
    {
        // Test Employee active scope
        $activeEmployees = Employee::active()->get();
        $this->assertTrue($activeEmployees->contains($this->employee));

        // Test ShiftSchedule active scope
        $activeShifts = ShiftSchedule::active()->get();
        $this->assertTrue($activeShifts->contains($this->shiftSchedule));

        // Create inactive employee
        $inactiveEmployee = Employee::create([
            'user_id' => User::factory()->create(['company_id' => $this->company->id])->id,
            'employee_number' => 'EMP002',
            'status' => 'inactive',
            'company_id' => $this->company->id,
        ]);

        $activeEmployees = Employee::active()->get();
        $this->assertFalse($activeEmployees->contains($inactiveEmployee));
    }
}
