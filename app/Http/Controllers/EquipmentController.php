<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentAssignment;
use App\Models\EquipmentUsage;
use App\Models\EquipmentMaintenance;
use App\Models\EquipmentFuelLog;
use App\Models\EquipmentTransfer;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'currentProject', 'assignedOperator']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('equipment_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $equipment = $query->latest()->paginate(20);
        $categories = EquipmentCategory::where('is_active', true)->get();

        return view('equipment.index', compact('equipment', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = EquipmentCategory::where('is_active', true)->get();
        $projects = Project::all();
        $employees = Employee::all();
        
        return view('equipment.create', compact('categories', 'projects', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_number' => 'required|string|unique:equipment,equipment_number',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:equipment_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'ownership' => 'required|in:owned,rented,leased',
            'rental_company' => 'nullable|string|max:255',
            'rental_rate' => 'nullable|numeric',
            'rental_rate_type' => 'nullable|in:hourly,daily,weekly,monthly',
            'purchase_price' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'current_value' => 'nullable|numeric',
            'hourly_rate' => 'nullable|numeric',
            'daily_rate' => 'nullable|numeric',
            'operating_cost_per_hour' => 'nullable|numeric',
            'capacity' => 'nullable|string|max:255',
            'power' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'fuel_consumption' => 'nullable|numeric',
            'status' => 'required|in:available,in_use,maintenance,breakdown,disposed,rented_out',
            'current_project_id' => 'nullable|exists:projects,id',
            'current_location' => 'nullable|string|max:255',
            'assigned_operator_id' => 'nullable|exists:employees,id',
            'maintenance_interval_hours' => 'nullable|integer',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'insurance_expiry_date' => 'nullable|date',
            'registration_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'تم إضافة المعدة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $equipment->load([
            'category',
            'currentProject',
            'assignedOperator',
            'assignments.project',
            'usageLogs' => function($q) {
                $q->latest()->take(10);
            },
            'maintenanceRecords' => function($q) {
                $q->latest()->take(10);
            },
            'fuelLogs' => function($q) {
                $q->latest()->take(10);
            }
        ]);

        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $categories = EquipmentCategory::where('is_active', true)->get();
        $projects = Project::all();
        $employees = Employee::all();
        
        return view('equipment.edit', compact('equipment', 'categories', 'projects', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'equipment_number' => 'required|string|unique:equipment,equipment_number,' . $equipment->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:equipment_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:4',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'ownership' => 'required|in:owned,rented,leased',
            'rental_company' => 'nullable|string|max:255',
            'rental_rate' => 'nullable|numeric',
            'rental_rate_type' => 'nullable|in:hourly,daily,weekly,monthly',
            'purchase_price' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'current_value' => 'nullable|numeric',
            'hourly_rate' => 'nullable|numeric',
            'daily_rate' => 'nullable|numeric',
            'operating_cost_per_hour' => 'nullable|numeric',
            'capacity' => 'nullable|string|max:255',
            'power' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'fuel_consumption' => 'nullable|numeric',
            'status' => 'required|in:available,in_use,maintenance,breakdown,disposed,rented_out',
            'current_project_id' => 'nullable|exists:projects,id',
            'current_location' => 'nullable|string|max:255',
            'assigned_operator_id' => 'nullable|exists:employees,id',
            'maintenance_interval_hours' => 'nullable|integer',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'insurance_expiry_date' => 'nullable|date',
            'registration_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $equipment->update($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'تم تحديث المعدة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        
        return redirect()->route('equipment.index')
            ->with('success', 'تم حذف المعدة بنجاح');
    }

    /**
     * Assign equipment to a project
     */
    public function assign(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'operator_id' => 'nullable|exists:employees,id',
            'assignment_date' => 'required|date',
            'expected_return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
        ]);

        $validated['equipment_id'] = $equipment->id;
        $validated['assigned_by'] = Auth::id();
        $validated['status'] = 'active';

        EquipmentAssignment::create($validated);

        // Update equipment status
        $equipment->update([
            'status' => 'in_use',
            'current_project_id' => $validated['project_id'],
            'assigned_operator_id' => $validated['operator_id'] ?? null,
        ]);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'تم تخصيص المعدة للمشروع بنجاح');
    }

    /**
     * Return equipment from project
     */
    public function returnEquipment(Request $request, Equipment $equipment)
    {
        $assignment = EquipmentAssignment::where('equipment_id', $equipment->id)
            ->where('status', 'active')
            ->first();

        if ($assignment) {
            $assignment->update([
                'status' => 'completed',
                'actual_return_date' => now(),
            ]);
        }

        $equipment->update([
            'status' => 'available',
            'current_project_id' => null,
            'assigned_operator_id' => null,
        ]);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'تم إرجاع المعدة بنجاح');
    }

    /**
     * Transfer equipment between projects
     */
    public function transfer(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'from_project_id' => 'nullable|exists:projects,id',
            'to_project_id' => 'required|exists:projects,id',
            'transfer_date' => 'required|date',
            'reason' => 'nullable|string',
            'transport_method' => 'nullable|string',
            'transport_cost' => 'nullable|numeric',
        ]);

        $validated['equipment_id'] = $equipment->id;
        $validated['approved_by'] = Auth::id();

        EquipmentTransfer::create($validated);

        $equipment->update([
            'current_project_id' => $validated['to_project_id'],
        ]);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'تم نقل المعدة بنجاح');
    }

    /**
     * Get equipment usage logs
     */
    public function usage(Equipment $equipment)
    {
        $usageLogs = $equipment->usageLogs()
            ->with(['project', 'operator', 'recordedBy'])
            ->latest('usage_date')
            ->paginate(15);

        return view('equipment.usage', compact('equipment', 'usageLogs'));
    }

    /**
     * Store equipment usage log
     */
    public function storeUsage(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'usage_date' => 'required|date',
            'hours_worked' => 'required|numeric|min:0',
            'start_meter' => 'nullable|numeric',
            'end_meter' => 'nullable|numeric',
            'fuel_consumed' => 'nullable|numeric',
            'operator_id' => 'nullable|exists:employees,id',
            'work_description' => 'nullable|string',
            'condition' => 'required|in:good,fair,needs_attention',
            'issues' => 'nullable|string',
        ]);

        $validated['equipment_id'] = $equipment->id;
        $validated['recorded_by'] = Auth::id();

        EquipmentUsage::create($validated);

        // Update equipment hours
        $equipment->increment('current_hours', $validated['hours_worked']);
        $equipment->increment('hours_since_last_maintenance', $validated['hours_worked']);

        return redirect()->route('equipment.usage', $equipment)
            ->with('success', 'تم تسجيل الاستخدام بنجاح');
    }

    /**
     * Get maintenance records
     */
    public function maintenance(Equipment $equipment)
    {
        $maintenanceRecords = $equipment->maintenanceRecords()
            ->with('performedBy')
            ->latest('scheduled_date')
            ->paginate(15);

        return view('equipment.maintenance', compact('equipment', 'maintenanceRecords'));
    }

    /**
     * Schedule maintenance
     */
    public function scheduleMaintenance(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'maintenance_number' => 'required|string',
            'type' => 'required|in:preventive,corrective,breakdown,inspection',
            'scheduled_date' => 'required|date',
            'description' => 'required|string',
            'estimated_cost' => 'nullable|numeric',
        ]);

        $validated['equipment_id'] = $equipment->id;
        $validated['status'] = 'scheduled';

        EquipmentMaintenance::create($validated);

        return redirect()->route('equipment.maintenance', $equipment)
            ->with('success', 'تم جدولة الصيانة بنجاح');
    }

    /**
     * Log fuel consumption
     */
    public function storeFuel(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'fill_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'meter_reading' => 'nullable|numeric',
            'supplier' => 'nullable|string',
            'receipt_number' => 'nullable|string',
        ]);

        $validated['equipment_id'] = $equipment->id;
        $validated['total_cost'] = $validated['quantity'] * $validated['unit_price'];
        $validated['recorded_by'] = Auth::id();

        EquipmentFuelLog::create($validated);

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'تم تسجيل تزويد الوقود بنجاح');
    }

    /**
     * Get available equipment
     */
    public function available()
    {
        $equipment = Equipment::available()
            ->with('category')
            ->get();

        return response()->json($equipment);
    }

    /**
     * Get equipment needing maintenance
     */
    public function maintenanceDue()
    {
        $equipment = Equipment::needsMaintenance()
            ->with('category')
            ->get();

        return response()->json($equipment);
    }

    /**
     * Get equipment statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'breakdown' => Equipment::where('status', 'breakdown')->count(),
        ];

        return response()->json($stats);
    }
}
