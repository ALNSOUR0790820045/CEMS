<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Country;
use App\Models\City;
use App\Models\Department;
use App\Models\Position;
use App\Models\Currency;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'position', 'country', 'city'])
            ->where('company_id', Auth::user()->company_id);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('position_id')) {
            $query->byPosition($request->position_id);
        }

        if ($request->filled('employee_type')) {
            $query->byType($request->employee_type);
        }

        if ($request->filled('employment_status')) {
            $query->byStatus($request->employment_status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('nationality_id')) {
            $query->where('nationality_id', $request->nationality_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15);

        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $positions = Position::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();

        return view('employees.index', compact('employees', 'departments', 'positions', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $positions = Position::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $banks = Bank::where('is_active', true)->get();
        $supervisors = Employee::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();

        $employeeCode = Employee::generateEmployeeCode();

        return view('employees.create', compact(
            'countries',
            'cities',
            'departments',
            'positions',
            'currencies',
            'banks',
            'supervisors',
            'employeeCode'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_en' => 'nullable|string|max:255',
            'middle_name_en' => 'nullable|string|max:255',
            'last_name_en' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|unique:employees,national_id',
            'passport_number' => 'nullable|string|unique:employees,passport_number',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality_id' => 'nullable|exists:countries,id',
            'gender' => 'required|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:employees,email',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'job_title' => 'required|string|max:255',
            'employee_type' => 'required|in:permanent,contract,temporary,consultant,daily_worker',
            'employment_status' => 'nullable|in:active,on_leave,suspended,resigned,terminated',
            'hire_date' => 'required|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'probation_end_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_frequency' => 'nullable|in:monthly,daily,hourly',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account_number' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'visa_number' => 'nullable|string|max:255',
            'visa_expiry_date' => 'nullable|date',
            'work_permit_number' => 'nullable|string|max:255',
            'work_permit_expiry_date' => 'nullable|date',
            'health_insurance_number' => 'nullable|string|max:255',
            'health_insurance_expiry_date' => 'nullable|date',
            'supervisor_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee = Employee::create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'department',
            'position',
            'country',
            'city',
            'nationality',
            'currency',
            'bank',
            'supervisor',
            'documents',
            'dependents',
            'qualifications',
            'workHistory',
            'skills',
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $countries = Country::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $positions = Position::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $banks = Bank::where('is_active', true)->get();
        $supervisors = Employee::where('company_id', Auth::user()->company_id)
            ->where('id', '!=', $employee->id)
            ->where('is_active', true)->get();

        return view('employees.edit', compact(
            'employee',
            'countries',
            'cities',
            'departments',
            'positions',
            'currencies',
            'banks',
            'supervisors'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_en' => 'nullable|string|max:255',
            'middle_name_en' => 'nullable|string|max:255',
            'last_name_en' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|unique:employees,national_id,' . $employee->id,
            'passport_number' => 'nullable|string|unique:employees,passport_number,' . $employee->id,
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality_id' => 'nullable|exists:countries,id',
            'gender' => 'required|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'job_title' => 'required|string|max:255',
            'employee_type' => 'required|in:permanent,contract,temporary,consultant,daily_worker',
            'employment_status' => 'nullable|in:active,on_leave,suspended,resigned,terminated',
            'hire_date' => 'required|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'probation_end_date' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'payment_frequency' => 'nullable|in:monthly,daily,hourly',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_account_number' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'visa_number' => 'nullable|string|max:255',
            'visa_expiry_date' => 'nullable|date',
            'work_permit_number' => 'nullable|string|max:255',
            'work_permit_expiry_date' => 'nullable|date',
            'health_insurance_number' => 'nullable|string|max:255',
            'health_insurance_expiry_date' => 'nullable|date',
            'supervisor_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    /**
     * Restore the specified employee.
     */
    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('employees.index')
            ->with('success', 'تم استعادة الموظف بنجاح');
    }

    /**
     * Generate a new employee code.
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Employee::generateEmployeeCode()
        ]);
    }
}
