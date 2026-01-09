@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">التقارير اليومية</h1>
        <a href="{{ route('daily-reports.create') }}" 
           style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            تقرير جديد
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">الحالة</label>
                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مرسل</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم التقرير أو الوصف" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" 
                        style="background: #0071e3; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    بحث
                </button>
                <a href="{{ route('daily-reports.index') }}" 
                   style="background: #f5f5f7; color: #666; padding: 8px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                    إعادة تعيين
                </a>
            </div>
        </div>
    </form>

    <!-- Reports Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">رقم التقرير</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">التاريخ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الطقس</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">العمال</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الصور</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">التوقيعات</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 1px solid #ddd;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            <a href="{{ route('daily-reports.show', $report) }}" 
                               style="color: #0071e3; text-decoration: none; font-weight: 500;">
                                {{ $report->report_number }}
                            </a>
                        </td>
                        <td style="padding: 15px;">{{ $report->report_date->format('Y-m-d') }}</td>
                        <td style="padding: 15px;">{{ $report->project->name }}</td>
                        <td style="padding: 15px;">
                            @if($report->weather_condition)
                                {{ $report->weather_condition }}
                                @if($report->temperature)
                                    ({{ $report->temperature }}°C)
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 15px;">{{ $report->workers_count }}</td>
                        <td style="padding: 15px;">
                            <span style="background: #e8f4fd; color: #0071e3; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">
                                {{ $report->photos->count() }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            @php
                                $statusColors = [
                                    'draft' => ['bg' => '#f5f5f7', 'color' => '#666'],
                                    'submitted' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                    'approved' => ['bg' => '#d4edda', 'color' => '#155724'],
                                    'rejected' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                                ];
                                $statusLabels = [
                                    'draft' => 'مسودة',
                                    'submitted' => 'مرسل',
                                    'approved' => 'معتمد',
                                    'rejected' => 'مرفوض',
                                ];
                            @endphp
                            <span style="background: {{ $statusColors[$report->status]['bg'] }}; color: {{ $statusColors[$report->status]['color'] }}; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $statusLabels[$report->status] }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 5px;">
                                @if($report->prepared_at)
                                    <span title="تم الإعداد" style="color: #28a745;">✓</span>
                                @endif
                                @if($report->reviewed_at)
                                    <span title="تمت المراجعة" style="color: #28a745;">✓</span>
                                @endif
                                @if($report->consultant_approved_at)
                                    <span title="اعتماد الاستشاري" style="color: #28a745;">✓</span>
                                @endif
                                @if($report->client_approved_at)
                                    <span title="اعتماد العميل" style="color: #28a745;">✓</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('daily-reports.show', $report) }}" 
                                   style="color: #0071e3; text-decoration: none;" title="عرض">
                                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                                </a>
                                @if($report->status === 'draft')
                                    <a href="{{ route('daily-reports.edit', $report) }}" 
                                       style="color: #ffc107; text-decoration: none;" title="تعديل">
                                        <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    <form method="POST" action="{{ route('daily-reports.destroy', $report) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;" 
                                                title="حذف">
                                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                            لا توجد تقارير يومية
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $reports->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
