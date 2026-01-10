@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">أوراق العمل اليومية - {{ $project->name }}</h1>
    
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666;">إجمالي الساعات</div>
            <div style="font-size: 32px; font-weight: bold; color: #0071e3;">{{ number_format($summary['total_hours'], 1) }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666;">ساعات إضافية</div>
            <div style="font-size: 32px; font-weight: bold; color: #ffc107;">{{ number_format($summary['overtime_hours'], 1) }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666;">إجمالي التكلفة</div>
            <div style="font-size: 32px; font-weight: bold; color: #28a745;">{{ number_format($summary['total_cost'], 0) }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666;">عدد الموظفين</div>
            <div style="font-size: 32px; font-weight: bold; color: #6c757d;">{{ $summary['employees_count'] }}</div>
        </div>
    </div>

    <!-- Add Timesheet Form -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;">إضافة ورقة عمل جديدة</h3>
        <form method="POST" action="{{ route('progress.timesheets.store', $project) }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">الموظف *</label>
                    <select name="employee_id" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">النشاط</label>
                    <select name="activity_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">بدون نشاط محدد</option>
                        @foreach($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">التاريخ *</label>
                    <input type="date" name="work_date" value="{{ now()->format('Y-m-d') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">ساعات عادية *</label>
                    <input type="number" name="regular_hours" min="0" max="24" step="0.5" value="8" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">ساعات إضافية</label>
                    <input type="number" name="overtime_hours" min="0" max="12" step="0.5" value="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">نسبة الإنجاز %</label>
                    <input type="number" name="progress_achieved" min="0" max="100" step="0.1" value="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600;">وصف العمل</label>
                    <input type="text" name="work_description" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إضافة</button>
        </form>
    </div>

    <!-- Timesheets List -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">سجل أوراق العمل</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: right;">التاريخ</th>
                    <th style="padding: 12px; text-align: right;">الموظف</th>
                    <th style="padding: 12px; text-align: right;">النشاط</th>
                    <th style="padding: 12px; text-align: center;">ساعات عادية</th>
                    <th style="padding: 12px; text-align: center;">ساعات إضافية</th>
                    <th style="padding: 12px; text-align: center;">الإنجاز %</th>
                    <th style="padding: 12px; text-align: center;">التكلفة</th>
                    <th style="padding: 12px; text-align: center;">الحالة</th>
                    <th style="padding: 12px; text-align: center;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timesheets as $timesheet)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $timesheet->work_date->format('Y-m-d') }}</td>
                    <td style="padding: 12px;">{{ $timesheet->employee->name }}</td>
                    <td style="padding: 12px;">{{ $timesheet->activity->name ?? '-' }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $timesheet->regular_hours }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $timesheet->overtime_hours }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $timesheet->progress_achieved }}%</td>
                    <td style="padding: 12px; text-align: center;">{{ number_format($timesheet->cost, 0) }}</td>
                    <td style="padding: 12px; text-align: center;">
                        @if($timesheet->status == 'draft')
                            <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">مسودة</span>
                        @elseif($timesheet->status == 'submitted')
                            <span style="background: #ffc107; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">معلقة</span>
                        @elseif($timesheet->status == 'approved')
                            <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">موافق عليها</span>
                        @else
                            <span style="background: #dc3545; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">مرفوضة</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($timesheet->status == 'draft')
                            <form method="POST" action="{{ route('progress.timesheets.submit', $timesheet) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #0071e3; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">تقديم</button>
                            </form>
                        @elseif($timesheet->status == 'submitted')
                            <form method="POST" action="{{ route('progress.timesheets.approve', $timesheet) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">موافقة</button>
                            </form>
                            <form method="POST" action="{{ route('progress.timesheets.reject', $timesheet) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">رفض</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #666;">لا توجد أوراق عمل</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
