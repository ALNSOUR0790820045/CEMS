<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['branch', 'manager']);

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $departments = $query->latest()->get();
        $branches = Branch::active()->get();

        return view('departments.index', compact('departments', 'branches'));
    }

    public function create()
    {
        $branches = Branch::active()->get();
        $users = User::active()->get();
        return view('departments.create', compact('branches', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    public function edit(Department $department)
    {
        $branches = Branch::active()->get();
        $users = User::active()->get();
        return view('departments.edit', compact('department', 'branches', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')
            ->with('success', 'تم حذف القسم بنجاح');
    }
}
