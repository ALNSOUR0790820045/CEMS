@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 2rem; color: #1d1d1f;">تفاصيل العامل</h1>
            <a href="{{ route('labor.index') }}" style="background: #e2e3e5; color: #383d41; padding: 12px 24px; border-radius: 8px; text-decoration: none;">
                رجوع
            </a>
        </div>

        <!-- Basic Info Card -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h2 style="color: #1d1d1f; margin-bottom: 20px;">المعلومات الأساسية</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">رقم العامل</div>
                    <div style="font-weight: 600; font-family: monospace;">{{ $laborer->labor_number }}</div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الاسم</div>
                    <div style="font-weight: 600;">{{ $laborer->name }}</div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الفئة</div>
                    <div style="font-weight: 600;">{{ $laborer->category->name ?? '-' }}</div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الجنسية</div>
                    <div style="font-weight: 600;">{{ $laborer->nationality ?? '-' }}</div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">نوع التوظيف</div>
                    <div style="font-weight: 600;">
                        @if($laborer->employment_type == 'permanent') دائم
                        @elseif($laborer->employment_type == 'temporary') مؤقت
                        @else مقاول باطن
                        @endif
                    </div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الأجر اليومي</div>
                    <div style="font-weight: 600;">{{ number_format($laborer->daily_wage, 2) }} ر.س</div>
                </div>
            </div>
        </div>

        <!-- Project & Status -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h2 style="color: #1d1d1f; margin-bottom: 20px;">الحالة والمشروع</h2>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الحالة</div>
                    <div>
                        @if($laborer->status === 'available')
                        <span style="background: #d4edda; color: #155724; padding: 6px 16px; border-radius: 12px; font-weight: 500;">متاح</span>
                        @elseif($laborer->status === 'assigned')
                        <span style="background: #fff3cd; color: #856404; padding: 6px 16px; border-radius: 12px; font-weight: 500;">مخصص</span>
                        @else
                        <span style="background: #e2e3e5; color: #383d41; padding: 6px 16px; border-radius: 12px; font-weight: 500;">{{ $laborer->status }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المشروع الحالي</div>
                    <div style="font-weight: 600;">{{ $laborer->currentProject->name ?? 'غير مخصص' }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        @if($laborer->attendance->count() > 0)
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #1d1d1f; margin-bottom: 20px;">آخر سجلات الحضور</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">التاريخ</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">المشروع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">ساعات العمل</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laborer->attendance as $record)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 12px;">{{ $record->attendance_date->format('Y-m-d') }}</td>
                        <td style="padding: 12px;">{{ $record->project->name ?? '-' }}</td>
                        <td style="padding: 12px;">{{ $record->total_hours }}</td>
                        <td style="padding: 12px;">{{ $record->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
