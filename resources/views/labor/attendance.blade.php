@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 30px;">تسجيل الحضور</h1>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Attendance Form -->
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="color: #1d1d1f; margin-bottom: 20px;">تسجيل حضور جديد</h2>
        <form method="POST" action="{{ route('labor.attendance.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                    <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">العامل *</label>
                    <input type="number" name="laborer_id" required placeholder="رقم العامل" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التاريخ *</label>
                    <input type="date" name="attendance_date" value="{{ $date }}" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الساعات العادية *</label>
                    <input type="number" name="regular_hours" required step="0.5" value="8" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الساعات الإضافية</label>
                    <input type="number" name="overtime_hours" step="0.5" value="0" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة *</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="present">حاضر</option>
                        <option value="absent">غائب</option>
                        <option value="half_day">نصف يوم</option>
                        <option value="leave">إجازة</option>
                        <option value="sick">مريض</option>
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 10px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                        تسجيل
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance List -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
            <h2 style="color: #1d1d1f;">سجلات الحضور لتاريخ {{ $date }}</h2>
        </div>
        @if($attendance->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">العامل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الساعات العادية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الساعات الإضافية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المجموع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendance as $record)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $record->laborer->name }}</td>
                    <td style="padding: 15px;">{{ $record->project->name }}</td>
                    <td style="padding: 15px;">{{ $record->regular_hours }}</td>
                    <td style="padding: 15px;">{{ $record->overtime_hours }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $record->total_hours }}</td>
                    <td style="padding: 15px;">
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">{{ $record->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 60px; text-align: center; color: #86868b;">
            لا توجد سجلات حضور لهذا التاريخ
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
