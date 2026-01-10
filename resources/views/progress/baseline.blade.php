@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إدارة الخط الأساسي - {{ $project->name }}</h1>
    
    <!-- Create New Baseline -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;">إنشاء خط أساسي جديد</h3>
        <form method="POST" action="{{ route('progress.baseline.store', $project) }}">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 3fr 1fr; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الخط الأساسي *</label>
                    <input type="text" name="baseline_name" required placeholder="مثال: Initial Baseline" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">السبب</label>
                    <input type="text" name="reason" placeholder="سبب إنشاء خط أساسي جديد" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إنشاء</button>
            </div>
        </form>
    </div>

    <!-- Baselines List -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">الخطوط الأساسية</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: right;">الاسم</th>
                    <th style="padding: 12px; text-align: right;">التاريخ</th>
                    <th style="padding: 12px; text-align: right;">الميزانية</th>
                    <th style="padding: 12px; text-align: right;">السبب</th>
                    <th style="padding: 12px; text-align: center;">الحالة</th>
                    <th style="padding: 12px; text-align: center;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($baselines as $baseline)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px; font-weight: 600;">{{ $baseline->baseline_name }}</td>
                    <td style="padding: 12px;">{{ $baseline->baseline_date->format('Y-m-d') }}</td>
                    <td style="padding: 12px;">{{ number_format($baseline->cost_snapshot['total_budget'] ?? 0, 0) }}</td>
                    <td style="padding: 12px;">{{ $baseline->reason ?? '-' }}</td>
                    <td style="padding: 12px; text-align: center;">
                        @if($baseline->is_current)
                            <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">حالي</span>
                        @else
                            <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">غير فعال</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if(!$baseline->is_current)
                            <form method="POST" action="{{ route('progress.baseline.set-current', [$project, $baseline]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #0071e3; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">تعيين كحالي</button>
                            </form>
                        @endif
                        <a href="{{ route('progress.baseline.compare', [$project, $baseline]) }}" style="background: #17a2b8; color: white; padding: 6px 12px; border: none; border-radius: 5px; text-decoration: none; font-size: 12px; display: inline-block;">مقارنة</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #666;">لا توجد خطوط أساسية</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
