@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">الموظفون</h1>
        <a href="{{ route('employees.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة موظف جديد
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('employees.index') }}" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            
            <select name="department_id" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">كل الأقسام</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            
            <select name="position_id" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">كل الوظائف</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                        {{ $pos->name }}
                    </option>
                @endforeach
            </select>
            
            <select name="employment_status" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                <option value="suspended" {{ request('employment_status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
                <option value="resigned" {{ request('employment_status') == 'resigned' ? 'selected' : '' }}>مستقيل</option>
                <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
            </select>
            
            <button type="submit" style="background: #0071e3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                بحث
            </button>
        </div>
    </form>

    <!-- Employees Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">الصورة</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">رقم الموظف</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">الاسم</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">القسم</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">الوظيفة</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">الجوال</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 1px solid #ddd;">الحالة</th>
                    <th style="padding: 15px; text-align: center; border-bottom: 1px solid #ddd;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            @if($employee->photo_path)
                                <img src="{{ asset('storage/' . $employee->photo_path) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #666;">
                                    {{ mb_substr($employee->first_name, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 15px;">{{ $employee->employee_code }}</td>
                        <td style="padding: 15px; font-weight: 600;">{{ $employee->full_name }}</td>
                        <td style="padding: 15px;">{{ $employee->department->name ?? '-' }}</td>
                        <td style="padding: 15px;">{{ $employee->position->name ?? '-' }}</td>
                        <td style="padding: 15px;">{{ $employee->mobile }}</td>
                        <td style="padding: 15px;">
                            @if($employee->employment_status == 'active')
                                <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">نشط</span>
                            @else
                                <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">{{ $employee->employment_status }}</span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('employees.show', $employee) }}" style="color: #0071e3; text-decoration: none; margin-left: 10px;">عرض</a>
                            <a href="{{ route('employees.edit', $employee) }}" style="color: #28a745; text-decoration: none; margin-left: 10px;">تعديل</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الموظف؟')" style="color: #dc3545; background: none; border: none; cursor: pointer; text-decoration: underline;">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                            لا يوجد موظفون
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $employees->links() }}
    </div>
</div>
@endsection
