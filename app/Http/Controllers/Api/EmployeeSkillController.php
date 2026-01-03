<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSkillController extends Controller
{
    public function index(Employee $employee)
    {
        return response()->json($employee->skills);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
        ]);

        $validated['company_id'] = Auth::user()->company_id;

        $skill = $employee->skills()->create($validated);

        return response()->json($skill, 201);
    }

    public function update(Request $request, Employee $employee, EmployeeSkill $skill)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
        ]);

        $skill->update($validated);

        return response()->json($skill);
    }

    public function destroy(Employee $employee, EmployeeSkill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }
}
