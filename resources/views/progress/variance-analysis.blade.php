@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تحليل الانحرافات - {{ $project->name }}</h1>
    
    @if($latestSnapshot)
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-right: 4px solid {{ $latestSnapshot->schedule_variance_sv >= 0 ? '#28a745' : '#dc3545' }};">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">انحراف الجدول (SV)</div>
            <div style="font-size: 32px; font-weight: bold; color: {{ $latestSnapshot->schedule_variance_sv >= 0 ? '#28a745' : '#dc3545' }};">
                {{ number_format($latestSnapshot->schedule_variance_sv, 0) }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-right: 4px solid {{ $latestSnapshot->cost_variance_cv >= 0 ? '#28a745' : '#dc3545' }};">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">انحراف التكلفة (CV)</div>
            <div style="font-size: 32px; font-weight: bold; color: {{ $latestSnapshot->cost_variance_cv >= 0 ? '#28a745' : '#dc3545' }};">
                {{ number_format($latestSnapshot->cost_variance_cv, 0) }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">أنشطة متأخرة</div>
            <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $delayedActivities->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">أنشطة تجاوزت الميزانية</div>
            <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $overBudgetActivities->count() }}</div>
        </div>
    </div>

    <!-- Top Delayed Activities -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;">أكثر 10 أنشطة تأخيراً</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: right;">النشاط</th>
                    <th style="padding: 12px; text-align: center;">التقدم %</th>
                    <th style="padding: 12px; text-align: center;">انحراف الجدول</th>
                    <th style="padding: 12px; text-align: center;">الحالة</th>
                    <th style="padding: 12px; text-align: center;">حرج</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topDelayed as $activity)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px; font-weight: 600;">{{ $activity['name'] }}</td>
                    <td style="padding: 12px; text-align: center;">{{ number_format($activity['progress'], 1) }}%</td>
                    <td style="padding: 12px; text-align: center; color: #dc3545; font-weight: bold;">{{ number_format($activity['schedule_variance'], 0) }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <span style="background: #ffc107; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">{{ $activity['status'] }}</span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($activity['is_critical'])
                            <span style="color: #dc3545; font-weight: bold;">✓</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #666;">لا توجد أنشطة متأخرة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Over Budget Activities -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">أكثر 10 أنشطة تجاوزاً للميزانية</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: right;">النشاط</th>
                    <th style="padding: 12px; text-align: center;">التقدم %</th>
                    <th style="padding: 12px; text-align: center;">انحراف التكلفة</th>
                    <th style="padding: 12px; text-align: center;">الحالة</th>
                    <th style="padding: 12px; text-align: center;">حرج</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topOverBudget as $activity)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px; font-weight: 600;">{{ $activity['name'] }}</td>
                    <td style="padding: 12px; text-align: center;">{{ number_format($activity['progress'], 1) }}%</td>
                    <td style="padding: 12px; text-align: center; color: #dc3545; font-weight: bold;">{{ number_format($activity['cost_variance'], 0) }}</td>
                    <td style="padding: 12px; text-align: center;">
                        <span style="background: #ffc107; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">{{ $activity['status'] }}</span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($activity['is_critical'])
                            <span style="color: #dc3545; font-weight: bold;">✓</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #666;">لا توجد أنشطة تجاوزت الميزانية</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <p style="font-size: 18px; color: #666;">لا توجد بيانات كافية لتحليل الانحرافات</p>
    </div>
    @endif
</div>
@endsection
