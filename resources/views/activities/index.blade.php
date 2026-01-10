@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إدارة الأنشطة</h1>
            <p style="color: #86868b;">عرض وإدارة جميع أنشطة المشروع ({{ $activities->total() }} نشاط)</p>
        </div>
        <a href="{{ route('activities.create') }}" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة نشاط جديد
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('activities.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="كود أو اسم النشاط" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">WBS</label>
                <select name="wbs_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع WBS</option>
                    @foreach($wbsItems as $wbs)
                    <option value="{{ $wbs->id }}" {{ request('wbs_id') == $wbs->id ? 'selected' : '' }}>
                        {{ $wbs->wbs_code }} - {{ $wbs->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المسؤول</label>
                <select name="responsible_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('responsible_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">حرج؟</label>
                <select name="is_critical" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="1" {{ request('is_critical') == '1' ? 'selected' : '' }}>حرج فقط</option>
                    <option value="0" {{ request('is_critical') == '0' ? 'selected' : '' }}>غير حرج</option>
                </select>
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">بحث</button>
                <a href="{{ route('activities.index') }}" style="flex: 1; background: #f5f5f7; color: #1d1d1f; padding: 10px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600;">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- Activities Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($activities->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 1200px;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الكود</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">WBS</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">البداية (مخطط)</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النهاية (مخطط)</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المدة</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الإنجاز</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المسؤول</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    <tr style="border-bottom: 1px solid #f0f0f0; {{ $activity->is_critical ? 'background: #fff5f5;' : '' }}">
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if($activity->is_critical)
                                <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: #ff3b30;"></i>
                                @endif
                                <span style="font-family: monospace; font-weight: 600;">{{ $activity->activity_code }}</span>
                            </div>
                        </td>
                        <td style="padding: 15px; font-weight: 600;">{{ $activity->name }}</td>
                        <td style="padding: 15px; color: #86868b; font-size: 0.9rem;">
                            {{ $activity->wbs ? $activity->wbs->wbs_code : '-' }}
                        </td>
                        <td style="padding: 15px; color: #86868b;">{{ $activity->planned_start_date->format('Y-m-d') }}</td>
                        <td style="padding: 15px; color: #86868b;">{{ $activity->planned_end_date->format('Y-m-d') }}</td>
                        <td style="padding: 15px; color: #86868b;">{{ $activity->planned_duration_days }} يوم</td>
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="flex: 1; height: 8px; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                    <div style="height: 100%; background: linear-gradient(90deg, #34c759, #0071e3); width: {{ $activity->progress_percent }}%;"></div>
                                </div>
                                <span style="font-weight: 600; color: #1d1d1f; font-size: 0.9rem;">{{ number_format($activity->progress_percent, 0) }}%</span>
                            </div>
                        </td>
                        <td style="padding: 15px;">
                            @php
                                $statusLabels = [
                                    'not_started' => 'لم يبدأ',
                                    'in_progress' => 'قيد التنفيذ',
                                    'completed' => 'مكتمل',
                                    'on_hold' => 'معلق',
                                    'cancelled' => 'ملغي',
                                ];
                            @endphp
                            <span style="background: {{ $activity->status_color }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $statusLabels[$activity->status] ?? $activity->status }}
                            </span>
                        </td>
                        <td style="padding: 15px; color: #86868b;">
                            {{ $activity->responsible ? $activity->responsible->name : '-' }}
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('activities.show', $activity) }}" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">عرض</a>
                                <a href="{{ route('activities.edit', $activity) }}" style="background: #ff9500; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 20px; border-top: 1px solid #f0f0f0;">
            {{ $activities->links() }}
        </div>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="activity" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد أنشطة</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة نشاط جديد</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <a href="{{ route('dependencies.index') }}" style="background: white; padding: 20px; border-radius: 12px; text-decoration: none; color: #1d1d1f; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 15px; transition: transform 0.2s;">
            <i data-lucide="git-branch" style="width: 32px; height: 32px; color: #0071e3;"></i>
            <div>
                <div style="font-weight: 600; margin-bottom: 5px;">إدارة التبعيات</div>
                <div style="font-size: 0.85rem; color: #86868b;">إدارة العلاقات بين الأنشطة</div>
            </div>
        </a>

        <a href="{{ route('milestones.index') }}" style="background: white; padding: 20px; border-radius: 12px; text-decoration: none; color: #1d1d1f; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 15px; transition: transform 0.2s;">
            <i data-lucide="flag" style="width: 32px; height: 32px; color: #ff9500;"></i>
            <div>
                <div style="font-weight: 600; margin-bottom: 5px;">المعالم الرئيسية</div>
                <div style="font-size: 0.85rem; color: #86868b;">متابعة المعالم المهمة</div>
            </div>
        </a>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
