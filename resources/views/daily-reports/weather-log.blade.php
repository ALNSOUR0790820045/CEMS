@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">سجل الطقس الشامل</h1>

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
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" 
                        style="background: #0071e3; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    بحث
                </button>
                <a href="{{ route('daily-reports.weather') }}" 
                   style="background: #f5f5f7; color: #666; padding: 8px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                    إعادة تعيين
                </a>
            </div>
        </div>
    </form>

    <!-- Weather Stats -->
    @php
        $clearDays = $reports->where('weather_condition', 'صافي')->count();
        $cloudyDays = $reports->where('weather_condition', 'غائم')->count();
        $rainyDays = $reports->where('weather_condition', 'ممطر')->count();
        $stormyDays = $reports->where('weather_condition', 'عاصف')->count();
        $avgTemp = $reports->whereNotNull('temperature')->avg('temperature');
        $avgHumidity = $reports->whereNotNull('humidity')->avg('humidity');
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #ffd700;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <span style="font-size: 2rem;">☀️</span>
                <div>
                    <div style="color: #666; font-size: 0.85rem;">أيام صافية</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #ffd700;">{{ $clearDays }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #999;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <span style="font-size: 2rem;">☁️</span>
                <div>
                    <div style="color: #666; font-size: 0.85rem;">أيام غائمة</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #999;">{{ $cloudyDays }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #0071e3;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <span style="font-size: 2rem;">🌧️</span>
                <div>
                    <div style="color: #666; font-size: 0.85rem;">أيام ممطرة</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0071e3;">{{ $rainyDays }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #dc3545;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <span style="font-size: 2rem;">⛈️</span>
                <div>
                    <div style="color: #666; font-size: 0.85rem;">أيام عاصفة</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #dc3545;">{{ $stormyDays }}</div>
                </div>
            </div>
        </div>

        @if($avgTemp)
            <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #ff6b6b;">
                <div style="color: #666; font-size: 0.85rem;">متوسط الحرارة</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #ff6b6b;">{{ number_format($avgTemp, 1) }}°C</div>
            </div>
        @endif

        @if($avgHumidity)
            <div style="background: white; padding: 20px; border-radius: 10px; border-right: 4px solid #4ecdc4;">
                <div style="color: #666; font-size: 0.85rem;">متوسط الرطوبة</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #4ecdc4;">{{ number_format($avgHumidity, 1) }}%</div>
            </div>
        @endif
    </div>

    <!-- Weather Affected Days Alert -->
    @if($rainyDays + $stormyDays > 0)
        <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 10px; border-right: 4px solid #ffc107; margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px;">⚠️ أيام متأثرة بالطقس</h4>
            <p style="margin: 0;">
                هناك <strong>{{ $rainyDays + $stormyDays }}</strong> يوم متأثر بالطقس (ممطر/عاصف) 
                والتي قد تستخدم في مطالبات تمديد الوقت (EOT Claims).
            </p>
        </div>
    @endif

    <!-- Weather Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">التاريخ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الحالة الجوية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الحرارة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الرطوبة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">ساعات العمل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">التأثير</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 1px solid #ddd;">التقرير</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">{{ $report->report_date->format('Y-m-d') }}</td>
                        <td style="padding: 15px;">{{ $report->project->name }}</td>
                        <td style="padding: 15px;">
                            @php
                                $weatherIcons = [
                                    'صافي' => '☀️',
                                    'غائم' => '☁️',
                                    'ممطر' => '🌧️',
                                    'عاصف' => '⛈️',
                                ];
                                $weatherColors = [
                                    'صافي' => '#ffd700',
                                    'غائم' => '#999',
                                    'ممطر' => '#0071e3',
                                    'عاصف' => '#dc3545',
                                ];
                            @endphp
                            <span style="display: inline-flex; align-items: center; gap: 5px;">
                                <span>{{ $weatherIcons[$report->weather_condition] ?? '' }}</span>
                                <span style="color: {{ $weatherColors[$report->weather_condition] ?? '#333' }}; font-weight: 600;">
                                    {{ $report->weather_condition }}
                                </span>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            @if($report->temperature)
                                {{ $report->temperature }}°C
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 15px;">
                            @if($report->humidity)
                                {{ $report->humidity }}%
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 15px;">{{ $report->total_work_hours }} ساعة</td>
                        <td style="padding: 15px;">
                            @if(in_array($report->weather_condition, ['ممطر', 'عاصف']))
                                <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    متأثر
                                </span>
                            @elseif($report->delays)
                                <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    تأخير
                                </span>
                            @else
                                <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    عادي
                                </span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('daily-reports.show', $report) }}" 
                               style="color: #0071e3; text-decoration: none;" title="عرض التقرير">
                                <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                            لا توجد سجلات طقس
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

    <!-- EOT Claims Info -->
    <div style="background: white; padding: 25px; border-radius: 10px; margin-top: 20px;">
        <h3 style="margin-bottom: 15px; color: #0071e3;">📋 دعم مطالبات تمديد الوقت (EOT)</h3>
        <div style="color: #666; line-height: 1.8;">
            <p style="margin-bottom: 10px;">
                يوفر سجل الطقس الشامل بيانات دقيقة وموثقة لدعم مطالبات تمديد الوقت (Extension of Time) بسبب الظروف الجوية السيئة.
            </p>
            <ul style="padding-right: 20px;">
                <li>الأيام الممطرة والعاصفة موثقة بالتاريخ والوقت</li>
                <li>درجات الحرارة والرطوبة مسجلة</li>
                <li>تأثير الطقس على ساعات العمل محدد</li>
                <li>التأخيرات والمشاكل المرتبطة بالطقس موثقة</li>
                <li>الصور الفوتوغرافية مع GPS تدعم المطالبات</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
