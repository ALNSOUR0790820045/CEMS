@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">تحديث تقدم النشاط</h1>
        <p style="color: #86868b;">{{ $activity->activity_code }} - {{ $activity->name }}</p>
    </div>

    <!-- Current Progress -->
    <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">التقدم الحالي</h3>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">نسبة الإنجاز</div>
                <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ number_format($activity->progress_percent, 0) }}%</div>
            </div>

            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">الحالة</div>
                <div style="font-weight: 600; color: {{ $activity->status_color }};">
                    @if($activity->status == 'not_started') لم يبدأ
                    @elseif($activity->status == 'in_progress') قيد التنفيذ
                    @elseif($activity->status == 'completed') مكتمل
                    @elseif($activity->status == 'on_hold') معلق
                    @else ملغي
                    @endif
                </div>
            </div>

            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">ساعات العمل</div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #34c759;">{{ number_format($activity->actual_effort_hours, 2) }}</div>
                <div style="font-size: 0.75rem; color: #86868b;">من {{ number_format($activity->planned_effort_hours, 2) }}</div>
            </div>

            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">التكلفة الفعلية</div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #ff9500;">{{ number_format($activity->actual_cost, 2) }}</div>
                <div style="font-size: 0.75rem; color: #86868b;">من {{ number_format($activity->budgeted_cost, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Update Form -->
    <form method="POST" action="{{ route('activities.update-progress', $activity) }}" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')
        
        <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">تحديث البيانات</h3>

        <!-- Progress Percentage -->
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 1.1rem;">نسبة الإنجاز *</label>
            
            <div style="display: flex; align-items: center; gap: 20px;">
                <input type="range" name="progress_percent" id="progressRange" value="{{ old('progress_percent', $activity->progress_percent) }}" min="0" max="100" step="1" 
                    style="flex: 1; height: 8px; border-radius: 4px; -webkit-appearance: none; background: linear-gradient(to right, #34c759 0%, #34c759 {{ old('progress_percent', $activity->progress_percent) }}%, #f0f0f0 {{ old('progress_percent', $activity->progress_percent) }}%, #f0f0f0 100%);">
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" name="progress_percent" id="progressInput" value="{{ old('progress_percent', $activity->progress_percent) }}" min="0" max="100" step="0.01" required
                        style="width: 100px; padding: 10px; border: 2px solid #0071e3; border-radius: 8px; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1.2rem; text-align: center;">
                    <span style="font-size: 1.2rem; font-weight: 600; color: #86868b;">%</span>
                </div>
            </div>
            
            @error('progress_percent')
            <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Dates -->
        <div style="margin-bottom: 30px;">
            <h4 style="margin-bottom: 15px; color: #1d1d1f;">التواريخ الفعلية</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ البداية الفعلي</label>
                    <input type="date" name="actual_start_date" value="{{ old('actual_start_date', $activity->actual_start_date?->format('Y-m-d')) }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('actual_start_date')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ النهاية الفعلي</label>
                    <input type="date" name="actual_end_date" value="{{ old('actual_end_date', $activity->actual_end_date?->format('Y-m-d')) }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('actual_end_date')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Effort and Cost -->
        <div style="margin-bottom: 30px;">
            <h4 style="margin-bottom: 15px; color: #1d1d1f;">الجهد والتكلفة</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ساعات العمل الفعلية</label>
                    <input type="number" name="actual_effort_hours" value="{{ old('actual_effort_hours', $activity->actual_effort_hours) }}" min="0" step="0.01" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-top: 5px;">
                        الساعات المخططة: {{ number_format($activity->planned_effort_hours, 2) }}
                    </div>
                    @error('actual_effort_hours')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التكلفة الفعلية</label>
                    <input type="number" name="actual_cost" value="{{ old('actual_cost', $activity->actual_cost) }}" min="0" step="0.01" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-top: 5px;">
                        التكلفة المخططة: {{ number_format($activity->budgeted_cost, 2) }}
                    </div>
                    @error('actual_cost')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات التقدم</label>
            <textarea name="notes" rows="4" placeholder="أضف أي ملاحظات حول التقدم في هذا النشاط..." 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('notes', $activity->notes) }}</textarea>
            @error('notes')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Progress Method Info -->
        <div style="background: #f5f5f7; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <i data-lucide="info" style="width: 20px; height: 20px; color: #0071e3;"></i>
                <span style="font-weight: 600; color: #1d1d1f;">معلومات مهمة</span>
            </div>
            <div style="color: #86868b; font-size: 0.9rem; line-height: 1.6;">
                • طريقة حساب التقدم الحالية: 
                <strong>
                    @if($activity->progress_method == 'manual') يدوي
                    @elseif($activity->progress_method == 'duration') بناء على المدة
                    @elseif($activity->progress_method == 'effort') بناء على الجهد
                    @else بناء على الوحدات
                    @endif
                </strong>
                <br>
                • سيتم تحديث حالة النشاط تلقائياً بناءً على نسبة الإنجاز
                <br>
                • المدة الفعلية سيتم حسابها تلقائياً من التواريخ
            </div>
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
            <button type="submit" style="background: linear-gradient(135deg, #34c759 0%, #30d158 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                حفظ التحديث
            </button>
            <a href="{{ route('activities.show', $activity) }}" style="background: #f5f5f7; color: #1d1d1f; padding: 14px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1.1rem; display: inline-flex; align-items: center;">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    // Sync range slider with input
    const progressRange = document.getElementById('progressRange');
    const progressInput = document.getElementById('progressInput');

    progressRange.addEventListener('input', function() {
        progressInput.value = this.value;
        updateRangeBackground(this.value);
    });

    progressInput.addEventListener('input', function() {
        let value = Math.min(100, Math.max(0, this.value));
        progressRange.value = value;
        this.value = value;
        updateRangeBackground(value);
    });

    function updateRangeBackground(value) {
        progressRange.style.background = `linear-gradient(to right, #34c759 0%, #34c759 ${value}%, #f0f0f0 ${value}%, #f0f0f0 100%)`;
    }

    // Initialize on page load
    updateRangeBackground(progressInput.value);
</script>
@endpush
@endsection
