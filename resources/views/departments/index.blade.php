@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إدارة الأقسام</h1>
            <p style="color: #86868b;">عرض وإدارة جميع الأقسام في النظام</p>
        </div>
        <a href="{{ route('departments.create') }}" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة قسم جديد
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('departments.index') }}" style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الفرع</label>
                <select name="branch_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div style="flex: 2;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الكود..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 24px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                <i data-lucide="search" style="width: 16px; height: 16px;"></i>
            </button>
            
            @if(request('branch_id') || request('search'))
            <a href="{{ route('departments.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 24px; border-radius: 5px; text-decoration: none; font-weight: 600;">
                إلغاء
            </a>
            @endif
        </form>
    </div>

    <!-- Departments Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($departments->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">#</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الكود</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الفرع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المدير</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $loop->iteration }}</td>
                    <td style="padding: 15px; font-weight: 600; color: #0071e3;">{{ $department->code }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $department->name }}</td>
                    <td style="padding: 15px; color: #86868b;">{{ $department->branch->name ?? '-' }}</td>
                    <td style="padding: 15px; color: #86868b;">{{ $department->manager->name ?? '-' }}</td>
                    <td style="padding: 15px;">
                        @if($department->is_active)
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">نشط</span>
                        @else
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="{{ route('departments.edit', $department) }}" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            <form method="POST" action="{{ route('departments.destroy', $department) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="building-2" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد أقسام</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة قسم جديد</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
