@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; color: #1d1d1f;">الجداول الزمنية</h1>
        <button onclick="document.getElementById('newTimesheetForm').style.display='block'" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
            إنشاء جدول جديد
        </button>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <!-- New Timesheet Form (Hidden by default) -->
    <div id="newTimesheetForm" style="display: none; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="color: #1d1d1f; margin-bottom: 20px;">جدول زمني جديد</h2>
        <form method="POST" action="{{ route('labor.timesheets.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                    <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        <!-- Projects will be loaded here -->
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ البداية *</label>
                    <input type="date" name="week_start_date" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ النهاية *</label>
                    <input type="date" name="week_end_date" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    إنشاء
                </button>
                <button type="button" onclick="document.getElementById('newTimesheetForm').style.display='none'" style="background: #e2e3e5; color: #383d41; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    إلغاء
                </button>
            </div>
        </form>
    </div>

    <!-- Timesheets List -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($timesheets->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">رقم الجدول</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الفترة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الساعات الكلية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المبلغ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timesheets as $timesheet)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px; font-family: monospace;">{{ $timesheet->timesheet_number }}</td>
                    <td style="padding: 15px;">{{ $timesheet->project->name }}</td>
                    <td style="padding: 15px;">{{ $timesheet->week_start_date->format('Y-m-d') }} - {{ $timesheet->week_end_date->format('Y-m-d') }}</td>
                    <td style="padding: 15px;">{{ $timesheet->total_regular_hours + $timesheet->total_overtime_hours }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ number_format($timesheet->total_amount, 2) }}</td>
                    <td style="padding: 15px;">
                        @if($timesheet->status == 'draft')
                        <span style="background: #e2e3e5; color: #383d41; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">مسودة</span>
                        @elseif($timesheet->status == 'approved')
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">معتمد</span>
                        @else
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">{{ $timesheet->status }}</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        @if($timesheet->status == 'draft')
                        <form method="POST" action="{{ route('labor.timesheets.approve', $timesheet) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: #34c759; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">
                                اعتماد
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 60px; text-align: center; color: #86868b;">
            لا توجد جداول زمنية
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
