<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use App\Models\ShiftSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test company
        $company = Company::firstOrCreate(
            ['email' => 'demo@company.com'],
            [
                'name' => 'Demo Company',
                'name_en' => 'Demo Company',
                'slug' => 'demo-company',
                'email' => 'demo@company.com',
                'phone' => '+1234567890',
                'address' => '123 Business Street',
                'city' => 'Business City',
                'country' => 'Country',
                'commercial_registration' => '1234567890',
                'tax_number' => '0987654321',
                'is_active' => true,
            ]
        );

        // Create shift schedules
        $morningShift = ShiftSchedule::firstOrCreate(
            [
                'shift_name' => 'Morning Shift',
                'company_id' => $company->id,
            ],
            [
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'grace_period_minutes' => 15,
                'working_hours' => 8.0,
                'is_active' => true,
                'company_id' => $company->id,
            ]
        );

        $afternoonShift = ShiftSchedule::firstOrCreate(
            [
                'shift_name' => 'Afternoon Shift',
                'company_id' => $company->id,
            ],
            [
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'grace_period_minutes' => 15,
                'working_hours' => 8.0,
                'is_active' => true,
                'company_id' => $company->id,
            ]
        );

        $nightShift = ShiftSchedule::firstOrCreate(
            [
                'shift_name' => 'Night Shift',
                'company_id' => $company->id,
            ],
            [
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'grace_period_minutes' => 15,
                'working_hours' => 8.0,
                'is_active' => true,
                'company_id' => $company->id,
            ]
        );

        // Create demo users and employees
        $departments = ['IT', 'HR', 'Sales', 'Marketing', 'Finance'];
        $positions = [
            'IT' => ['Developer', 'System Administrator', 'IT Manager'],
            'HR' => ['HR Manager', 'Recruiter', 'HR Assistant'],
            'Sales' => ['Sales Manager', 'Sales Representative', 'Account Manager'],
            'Marketing' => ['Marketing Manager', 'Content Creator', 'SEO Specialist'],
            'Finance' => ['Finance Manager', 'Accountant', 'Financial Analyst'],
        ];

        foreach ($departments as $index => $department) {
            $departmentPositions = $positions[$department];
            
            foreach ($departmentPositions as $posIndex => $position) {
                $empNumber = str_pad(($index * 10) + $posIndex + 1, 4, '0', STR_PAD_LEFT);
                
                $user = User::firstOrCreate(
                    ['email' => "emp{$empNumber}@company.com"],
                    [
                        'name' => fake()->name(),
                        'email' => "emp{$empNumber}@company.com",
                        'password' => Hash::make('password'),
                        'phone' => fake()->phoneNumber(),
                        'job_title' => $position,
                        'employee_id' => "EMP{$empNumber}",
                        'is_active' => true,
                        'company_id' => $company->id,
                    ]
                );

                // Assign shift (rotate between shifts)
                $shiftId = match ($posIndex % 3) {
                    0 => $morningShift->id,
                    1 => $afternoonShift->id,
                    2 => $nightShift->id,
                };

                Employee::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'employee_number' => "EMP{$empNumber}",
                        'hire_date' => now()->subYears(rand(1, 5))->subDays(rand(0, 365)),
                        'department' => $department,
                        'position' => $position,
                        'employment_type' => rand(0, 1) ? 'full_time' : 'part_time',
                        'shift_schedule_id' => $shiftId,
                        'salary' => rand(3000, 8000),
                        'status' => 'active',
                        'company_id' => $company->id,
                    ]
                );
            }
        }

        $this->command->info('Attendance module seeded successfully!');
        $this->command->info("Company: {$company->name} (ID: {$company->id})");
        $this->command->info("Shifts created: 3 (Morning, Afternoon, Night)");
        $this->command->info("Employees created: " . Employee::count());
    }
}
