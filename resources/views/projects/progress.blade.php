@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>تقارير التقدم - {{ $project->name }}</h1>
        <a href="{{ route('projects.show', $project) }}" 
           style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            العودة للمشروع
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Add New Report Form -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin: 0 0 20px 0;">إضافة تقرير تقدم جديد</h3>
        <form method="POST" action="{{ route('projects.progress.store', $project) }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التقرير *</label>
                    <input type="date" name="report_date" required value="{{ date('Y-m-d') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع الفترة *</label>
                    <select name="period_type" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="daily">يومي</option>
                        <option value="weekly" selected>أسبوعي</option>
                        <option value="monthly">شهري</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التقدم الفعلي % *</label>
                    <input type="number" name="physical_progress" required min="0" max="100" step="0.01" value="{{ $project->physical_progress }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التقدم المخطط % *</label>
                    <input type="number" name="planned_progress" required min="0" max="100" step="0.01"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">عدد العمال *</label>
                    <input type="number" name="manpower_count" required min="0" value="0"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">عدد المعدات *</label>
                    <input type="number" name="equipment_count" required min="0" value="0"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الطقس *</label>
                    <select name="weather" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="sunny" selected>مشمس</option>
                        <option value="cloudy">غائم</option>
                        <option value="rainy">ماطر</option>
                        <option value="sandstorm">عاصفة رملية</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأعمال المنجزة</label>
                    <textarea name="work_done" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأعمال المخططة</label>
                    <textarea name="planned_work" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
                </div>
            </div>

            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ التقرير
            </button>
        </form>
    </div>

    <!-- Reports List -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">التقارير السابقة</h3>
        
        @forelse($reports as $report)
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0;">تقرير #{{ $report->report_number }}</h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">{{ $report->report_date->format('Y-m-d') }} - {{ $report->period_type }}</p>
                    </div>
                    <div style="text-align: left;">
                        <p style="margin: 0 0 5px 0; font-size: 24px; font-weight: 700; color: #0071e3;">{{ number_format($report->physical_progress, 1) }}%</p>
                        <p style="margin: 0; font-size: 12px; color: {{ $report->variance >= 0 ? '#155724' : '#721c24' }};">
                            الفرق: {{ $report->variance > 0 ? '+' : '' }}{{ number_format($report->variance, 1) }}%
                        </p>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 15px;">
                    <div>
                        <p style="margin: 0; color: #666; font-size: 12px;">التقدم المخطط</p>
                        <p style="margin: 0; font-weight: 600;">{{ number_format($report->planned_progress, 1) }}%</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 12px;">العمال</p>
                        <p style="margin: 0; font-weight: 600;">{{ $report->manpower_count }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 12px;">المعدات</p>
                        <p style="margin: 0; font-weight: 600;">{{ $report->equipment_count }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 12px;">الطقس</p>
                        <p style="margin: 0; font-weight: 600;">{{ $report->weather }}</p>
                    </div>
                </div>

                @if($report->work_done)
                    <div style="margin-bottom: 10px;">
                        <p style="margin: 0 0 5px 0; color: #666; font-size: 12px;">الأعمال المنجزة</p>
                        <p style="margin: 0;">{{ $report->work_done }}</p>
                    </div>
                @endif

                <div style="border-top: 1px solid #dee2e6; padding-top: 10px; margin-top: 10px;">
                    <p style="margin: 0; color: #666; font-size: 12px;">أعده: {{ $report->preparedBy->name }}</p>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #666; padding: 40px;">لا توجد تقارير حتى الآن</p>
        @endforelse

        @if($reports->hasPages())
            <div style="margin-top: 20px;">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
