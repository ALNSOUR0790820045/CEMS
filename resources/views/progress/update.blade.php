@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تحديث التقدم - {{ $project->name }}</h1>
    
    @if($latestSnapshot)
    <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin-bottom: 30px; border-right: 4px solid #0071e3;">
        <h3 style="margin-bottom: 10px;">آخر تحديث: {{ $latestSnapshot->snapshot_date->format('Y-m-d') }}</h3>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
            <div>
                <div style="font-size: 12px; color: #666;">نسبة الإنجاز</div>
                <div style="font-size: 24px; font-weight: bold; color: #0071e3;">{{ number_format($latestSnapshot->overall_progress_percent, 1) }}%</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666;">SPI</div>
                <div style="font-size: 24px; font-weight: bold; color: #0071e3;">{{ number_format($latestSnapshot->schedule_performance_index_spi, 2) }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666;">CPI</div>
                <div style="font-size: 24px; font-weight: bold; color: #0071e3;">{{ number_format($latestSnapshot->cost_performance_index_cpi, 2) }}</div>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('progress.update.store', $project) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        
        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">معلومات التقرير</h3>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التقرير *</label>
            <input type="date" name="snapshot_date" value="{{ old('snapshot_date', now()->format('Y-m-d')) }}" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('snapshot_date')
                <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نسبة الإنجاز الإجمالية % *</label>
                <input type="number" name="overall_progress_percent" id="overall_progress" 
                       value="{{ old('overall_progress_percent', $latestSnapshot->overall_progress_percent ?? 0) }}" 
                       min="0" max="100" step="0.01" required
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                       oninput="previewCalculations()">
                @error('overall_progress_percent')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نسبة الإنجاز الفيزيائي %</label>
                <input type="number" name="physical_progress_percent" 
                       value="{{ old('physical_progress_percent', $latestSnapshot->physical_progress_percent ?? 0) }}" 
                       min="0" max="100" step="0.01"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('physical_progress_percent')
                    <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">التكلفة الفعلية (AC) *</label>
            <input type="number" name="actual_cost_ac" id="actual_cost" 
                   value="{{ old('actual_cost_ac', $latestSnapshot->actual_cost_ac ?? 0) }}" 
                   min="0" step="0.01" required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                   oninput="previewCalculations()">
            <div style="font-size: 12px; color: #666; margin-top: 5px;">الميزانية الكلية: {{ number_format($project->total_budget, 0) }}</div>
            @error('actual_cost_ac')
                <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">التعليقات</label>
            <textarea name="comments" rows="4" 
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('comments') }}</textarea>
            @error('comments')
                <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <h3 style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">معاينة الحسابات التلقائية</h3>
        
        <div id="calculations-preview" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">PV (القيمة المخططة)</div>
                    <div id="pv-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">EV (القيمة المكتسبة)</div>
                    <div id="ev-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">AC (التكلفة الفعلية)</div>
                    <div id="ac-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">SV (انحراف الجدول)</div>
                    <div id="sv-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">CV (انحراف التكلفة)</div>
                    <div id="cv-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">SPI (مؤشر أداء الجدول)</div>
                    <div id="spi-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">CPI (مؤشر أداء التكلفة)</div>
                    <div id="cpi-value" style="font-size: 20px; font-weight: bold;">-</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">EAC (التكلفة عند الإنجاز)</div>
                    <div id="eac-value" style="font-size: 18px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">ETC (التكلفة للإنجاز)</div>
                    <div id="etc-value" style="font-size: 18px; font-weight: bold;">-</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">VAC (انحراف عند الإنجاز)</div>
                    <div id="vac-value" style="font-size: 18px; font-weight: bold;">-</div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ التحديث</button>
            <a href="{{ route('progress.dashboard', ['project_id' => $project->id]) }}" style="padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>

<script>
const projectData = {
    id: {{ $project->id }},
    budget: {{ $project->total_budget }},
    startDate: '{{ $project->start_date }}',
    endDate: '{{ $project->planned_end_date }}'
};

function previewCalculations() {
    const snapshotDate = document.querySelector('[name="snapshot_date"]').value;
    const progress = parseFloat(document.getElementById('overall_progress').value) || 0;
    const actualCost = parseFloat(document.getElementById('actual_cost').value) || 0;
    const physicalProgress = parseFloat(document.querySelector('[name="physical_progress_percent"]').value) || progress;

    if (!snapshotDate || progress === 0) {
        return;
    }

    // Make AJAX request to preview endpoint
    fetch(`/progress/update/${projectData.id}/preview`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
        },
        body: JSON.stringify({
            snapshot_date: snapshotDate,
            overall_progress_percent: progress,
            physical_progress_percent: physicalProgress,
            actual_cost_ac: actualCost
        })
    })
    .then(response => response.json())
    .then(data => {
        // Update preview values
        document.getElementById('pv-value').textContent = formatNumber(data.planned_value_pv);
        document.getElementById('ev-value').textContent = formatNumber(data.earned_value_ev);
        document.getElementById('ac-value').textContent = formatNumber(data.actual_cost_ac);
        
        document.getElementById('sv-value').textContent = formatNumber(data.schedule_variance_sv);
        document.getElementById('sv-value').style.color = data.schedule_variance_sv >= 0 ? '#28a745' : '#dc3545';
        
        document.getElementById('cv-value').textContent = formatNumber(data.cost_variance_cv);
        document.getElementById('cv-value').style.color = data.cost_variance_cv >= 0 ? '#28a745' : '#dc3545';
        
        document.getElementById('spi-value').textContent = data.schedule_performance_index_spi;
        document.getElementById('spi-value').style.color = data.schedule_performance_index_spi >= 0.95 ? '#28a745' : (data.schedule_performance_index_spi >= 0.85 ? '#ffc107' : '#dc3545');
        
        document.getElementById('cpi-value').textContent = data.cost_performance_index_cpi;
        document.getElementById('cpi-value').style.color = data.cost_performance_index_cpi >= 0.95 ? '#28a745' : (data.cost_performance_index_cpi >= 0.85 ? '#ffc107' : '#dc3545');
        
        document.getElementById('eac-value').textContent = formatNumber(data.estimate_at_completion_eac);
        document.getElementById('etc-value').textContent = formatNumber(data.estimate_to_complete_etc);
        document.getElementById('vac-value').textContent = formatNumber(data.variance_at_completion_vac);
        document.getElementById('vac-value').style.color = data.variance_at_completion_vac >= 0 ? '#28a745' : '#dc3545';
    })
    .catch(error => console.error('Error:', error));
}

function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA').format(Math.round(num));
}

// Trigger preview on load if values exist
document.addEventListener('DOMContentLoaded', function() {
    previewCalculations();
});
</script>
@endsection
