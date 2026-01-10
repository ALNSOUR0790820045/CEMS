@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">التنبؤ بالإنجاز - {{ $project->name }}</h1>
    
    @if($latestSnapshot)
    <!-- Current Status -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;">الوضع الحالي</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">نسبة الإنجاز</div>
                <div style="font-size: 28px; font-weight: bold;">{{ number_format($latestSnapshot->overall_progress_percent, 1) }}%</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">SPI الحالي</div>
                <div style="font-size: 28px; font-weight: bold;">{{ number_format($latestSnapshot->schedule_performance_index_spi, 2) }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">CPI الحالي</div>
                <div style="font-size: 28px; font-weight: bold;">{{ number_format($latestSnapshot->cost_performance_index_cpi, 2) }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">التاريخ المخطط</div>
                <div style="font-size: 18px; font-weight: bold;">{{ $latestSnapshot->planned_completion_date->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <!-- Scenarios -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;">سيناريوهات التنبؤ</h3>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <!-- Optimistic -->
            <div style="padding: 25px; border: 2px solid #28a745; border-radius: 10px; background: #f0fff4;">
                <h4 style="color: #28a745; margin-bottom: 15px; text-align: center;">متفائل</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">{{ $scenarios['optimistic']['description'] }}</div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 10px;">SPI: {{ number_format($scenarios['optimistic']['spi'], 2) }}</div>
                    <div style="font-size: 24px; font-weight: bold; color: #28a745;">
                        {{ $scenarios['optimistic']['completion_date']->format('Y-m-d') }}
                    </div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        {{ $scenarios['optimistic']['completion_date']->diffForHumans($project->planned_end_date) }}
                    </div>
                </div>
            </div>

            <!-- Most Likely -->
            <div style="padding: 25px; border: 2px solid #0071e3; border-radius: 10px; background: #e3f2fd;">
                <h4 style="color: #0071e3; margin-bottom: 15px; text-align: center;">الأكثر احتمالاً</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">{{ $scenarios['most_likely']['description'] }}</div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 10px;">SPI: {{ number_format($scenarios['most_likely']['spi'], 2) }}</div>
                    <div style="font-size: 24px; font-weight: bold; color: #0071e3;">
                        {{ $scenarios['most_likely']['completion_date']->format('Y-m-d') }}
                    </div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        {{ $scenarios['most_likely']['completion_date']->diffForHumans($project->planned_end_date) }}
                    </div>
                </div>
            </div>

            <!-- Pessimistic -->
            <div style="padding: 25px; border: 2px solid #dc3545; border-radius: 10px; background: #fee;">
                <h4 style="color: #dc3545; margin-bottom: 15px; text-align: center;">متشائم</h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">{{ $scenarios['pessimistic']['description'] }}</div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 10px;">SPI: {{ number_format($scenarios['pessimistic']['spi'], 2) }}</div>
                    <div style="font-size: 24px; font-weight: bold; color: #dc3545;">
                        {{ $scenarios['pessimistic']['completion_date']->format('Y-m-d') }}
                    </div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        {{ $scenarios['pessimistic']['completion_date']->diffForHumans($project->planned_end_date) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Scenario Calculator -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">حاسبة السيناريو المخصص</h3>
        <p style="color: #666; margin-bottom: 20px;">قم بتعديل تحسين/تدهور الأداء لرؤية التأثير على تاريخ الإنجاز</p>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
            <div>
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">نسبة التحسين/التدهور في الأداء (%)</label>
                <input type="range" id="spiAdjustment" min="-50" max="50" value="0" step="1" 
                       style="width: 100%; margin-bottom: 10px;"
                       oninput="updateCustomScenario()">
                <div style="text-align: center;">
                    <span id="adjustmentValue" style="font-size: 24px; font-weight: bold; color: #0071e3;">0%</span>
                </div>
            </div>

            <div id="customResult" style="padding: 20px; background: #f8f9fa; border-radius: 10px; text-align: center;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">تاريخ الإنجاز المتوقع</div>
                <div style="font-size: 24px; font-weight: bold; color: #0071e3;">-</div>
                <div style="font-size: 12px; color: #666; margin-top: 10px;">التأخير: <span id="delayDays">-</span> يوم</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">SPI: <span id="customSpi">-</span></div>
            </div>
        </div>
    </div>
    @else
    <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <p style="font-size: 18px; color: #666;">لا توجد بيانات كافية للتنبؤ</p>
    </div>
    @endif
</div>

@if($latestSnapshot)
<script>
function updateCustomScenario() {
    const adjustment = document.getElementById('spiAdjustment').value;
    document.getElementById('adjustmentValue').textContent = (adjustment > 0 ? '+' : '') + adjustment + '%';
    
    fetch(`/progress/forecasting/{{ $project->id }}/custom-scenario`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            spi_adjustment: parseFloat(adjustment)
        })
    })
    .then(response => response.json())
    .then(data => {
        document.querySelector('#customResult > div:nth-child(2)').textContent = data.completion_date;
        document.getElementById('delayDays').textContent = Math.abs(data.delay_days);
        document.getElementById('customSpi').textContent = data.spi;
        
        // Color coding
        const resultDiv = document.getElementById('customResult');
        if (data.delay_days <= 0) {
            resultDiv.style.background = '#f0fff4';
            resultDiv.querySelector('div:nth-child(2)').style.color = '#28a745';
        } else if (data.delay_days <= 30) {
            resultDiv.style.background = '#fff3cd';
            resultDiv.querySelector('div:nth-child(2)').style.color = '#ffc107';
        } else {
            resultDiv.style.background = '#fee';
            resultDiv.querySelector('div:nth-child(2)').style.color = '#dc3545';
        }
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateCustomScenario();
});
</script>
@endif
@endsection
