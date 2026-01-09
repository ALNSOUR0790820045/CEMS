@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة نشاط جديد</h1>
    
    <form method="POST" action="{{ route('activities.store') }}" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        
        <!-- Basic Information -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">معلومات أساسية</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                    <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_code }} - {{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">WBS *</label>
                    <select name="wbs_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر WBS</option>
                        @foreach($wbsItems as $wbs)
                        <option value="{{ $wbs->id }}">{{ $wbs->wbs_code }} - {{ $wbs->name }}</option>
                        @endforeach
                    </select>
                    @error('wbs_id')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود النشاط *</label>
                    <input type="text" name="activity_code" value="{{ old('activity_code') }}" required placeholder="ACT-001" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('activity_code')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم النشاط *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('name')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Schedule -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">الجدول الزمني المخطط</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ البداية *</label>
                    <input type="date" name="planned_start_date" value="{{ old('planned_start_date') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('planned_start_date')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ النهاية *</label>
                    <input type="date" name="planned_end_date" value="{{ old('planned_end_date') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('planned_end_date')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ساعات العمل المخططة</label>
                    <input type="number" name="planned_effort_hours" value="{{ old('planned_effort_hours', 0) }}" min="0" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <!-- Progress & Type -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">التقدم والنوع</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">نسبة الإنجاز</label>
                    <input type="number" name="progress_percent" value="{{ old('progress_percent', 0) }}" min="0" max="100" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">طريقة حساب التقدم *</label>
                    <select name="progress_method" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="manual" {{ old('progress_method') == 'manual' ? 'selected' : '' }}>يدوي</option>
                        <option value="duration" {{ old('progress_method') == 'duration' ? 'selected' : '' }}>بناء على المدة</option>
                        <option value="effort" {{ old('progress_method') == 'effort' ? 'selected' : '' }}>بناء على الجهد</option>
                        <option value="units" {{ old('progress_method') == 'units' ? 'selected' : '' }}>بناء على الوحدات</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع النشاط *</label>
                    <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="task" {{ old('type') == 'task' ? 'selected' : '' }}>مهمة</option>
                        <option value="milestone" {{ old('type') == 'milestone' ? 'selected' : '' }}>معلم</option>
                        <option value="summary" {{ old('type') == 'summary' ? 'selected' : '' }}>ملخص</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Responsibility & Status -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">المسؤولية والحالة</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المسؤول</label>
                    <select name="responsible_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المسؤول</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('responsible_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة *</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="not_started" {{ old('status') == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                    <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cost -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">التكلفة</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التكلفة المخططة</label>
                    <input type="number" name="budgeted_cost" value="{{ old('budgeted_cost', 0) }}" min="0" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
            <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ النشاط</button>
            <a href="{{ route('activities.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
