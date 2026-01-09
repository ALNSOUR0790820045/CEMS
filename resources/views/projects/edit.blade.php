@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل المشروع:  {{ $project->name }}</h1>
    
    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.update', $project) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <h3 style="margin-bottom:  20px; color: #0071e3; border-bottom:  2px solid #0071e3; padding-bottom: 10px;">معلومات أساسية</h3>
        
        <div style="margin-bottom: 20px; padding:  15px; background: #f8f9fa; border-radius: 8px;">
            <p style="margin: 0; font-size: 14px; color: #666;">رقم المشروع</p>
            <p style="margin: 0; font-weight: 600; font-size:  16px;">{{ $project->project_number ??  $project->project_code }}</p>
        </div>

        <div style="display:  grid; grid-template-columns:  1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom:  5px; font-weight:  600;">اسم المشروع *</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required 
                       style="width: 100%; padding: 10px; border:  1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display:  block; margin-bottom: 5px; font-weight: 600;">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" value="{{ old('name_en', $project->name_en) }}" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:  block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
            <textarea name="description" rows="3" 
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description', $project->description) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العميل *</label>
                <select name="client_id" required 
                        style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العميل</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' :  '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع المشروع *</label>
                <select name="type" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="building" {{ old('type', $project->type ??  $project->project_type) == 'building' ? 'selected' : '' }}>مباني</option>
                    <option value="infrastructure" {{ old('type', $project->type ?? $project->project_type) == 'infrastructure' ? 'selected' : '' }}>بنية تحتية</option>
                    <option value="industrial" {{ old('type', $project->type ?? $project->project_type) == 'industrial' ? 'selected' : '' }}>صناعي</option>
                    <option value="maintenance" {{ old('type', $project->type ?? $project->project_type) == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                    <option value="fit_out" {{ old('type', $project->type ?? $project->project_type) == 'fit_out' ? 'selected' : '' }}>تشطيبات</option>
                    <option value="other" {{ old('type', $project->type ?? $project->project_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap:  20px; margin-bottom:  30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التصنيف *</label>
                <select name="category" required 
                        style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="new_construction" {{ old('category', $project->category) == 'new_construction' ? 'selected' : '' }}>إنشاء جديد</option>
                    <option value="renovation" {{ old('category', $project->category) == 'renovation' ? 'selected' : '' }}>تجديد</option>
                    <option value="expansion" {{ old('category', $project->category) == 'expansion' ? 'selected' : '' }}>توسعة</option>
                    <option value="maintenance" {{ old('category', $project->category) == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نسبة الإنجاز الفعلي</label>
                <input type="number" name="physical_progress" value="{{ old('physical_progress', $project->physical_progress) }}" 
                       min="0" max="100" step="0.01"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <h3 style="margin-bottom: 20px; color: #0071e3; border-bottom: 2px solid #0071e3; padding-bottom: 10px;">الموقع</h3>
        
        <div style="display:  grid; grid-template-columns:  1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدينة *</label>
                <input type="text" name="city" value="{{ old('city', $project->city) }}" required 
                       style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المنطقة</label>
                <input type="text" name="region" value="{{ old('region', $project->region) }}" 
                       style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة *</label>
                <input type="text" name="country" value="{{ old('country', $project->country) }}" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان *</label>
            <input type="text" name="location" value="{{ old('location', $project->location) }}" required 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <h3 style="margin-bottom: 20px; color: #0071e3; border-bottom: 2px solid #0071e3; padding-bottom: 10px;">التواريخ والمدة</h3>
        
        <div style="display:  grid; grid-template-columns:  1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ البدء *</label>
                <input type="date" name="commencement_date" value="{{ old('commencement_date', $project->commencement_date? ->format('Y-m-d') ?? $project->contract_start_date? ->format('Y-m-d')) }}" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الانتهاء المخطط *</label>
                <input type="date" name="original_completion_date" value="{{ old('original_completion_date', $project->original_completion_date? ->format('Y-m-d') ?? $project->contract_end_date?->format('Y-m-d')) }}" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الانتهاء المعدل</label>
                <input type="date" name="revised_completion_date" value="{{ old('revised_completion_date', $project->revised_completion_date?->format('Y-m-d')) }}" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدة (أيام) *</label>
            <input type="number" name="original_duration_days" value="{{ old('original_duration_days', $project->original_duration_days ??  $project->contract_duration_days) }}" required min="1" 
                   style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <h3 style="margin-bottom: 20px; color: #0071e3; border-bottom: 2px solid #0071e3; padding-bottom: 10px;">القيم المالية</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">قيمة ��لعقد الأصلية *</label>
                <input type="number" name="original_contract_value" value="{{ old('original_contract_value', $project->original_contract_value ?? $project->contract_value) }}" required min="0" step="0.01" 
                       style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">قيمة العقد المعدلة *</label>
                <input type="number" name="revised_contract_value" value="{{ old('revised_contract_value', $project->revised_contract_value ?? $project->contract_value) }}" required min="0" step="0.01" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة *</label>
                <select name="currency" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR" {{ old('currency', $project->currency) == 'SAR' ? 'selected' :  '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ old('currency', $project->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="AED" {{ old('currency', $project->currency) == 'AED' ? 'selected' :  '' }}>درهم إماراتي (AED)</option>
                </select>
            </div>
        </div>

        <h3 style="margin-bottom:  20px; color: #0071e3; border-bottom:  2px solid #0071e3; padding-bottom: 10px;">الفريق</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">مدير المشروع</label>
                <select name="project_manager_id" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر مدير المشروع</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('project_manager_id', $project->project_manager_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display:  block; margin-bottom: 5px; font-weight: 600;">مهندس الموقع</label>
                <select name="site_engineer_id" 
                        style="width: 100%; padding: 10px; border:  1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر مهندس الموقع</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('site_engineer_id', $project->site_engineer_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h3 style="margin-bottom:  20px; color: #0071e3; border-bottom:  2px solid #0071e3; padding-bottom: 10px;">الحالة</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">حالة المشروع *</label>
                <select name="status" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="not_started" {{ old('status', $project->status ??  $project->project_status) == 'not_started' ? 'selected' : '' }}>لم يبدأ</option>
                    <option value="mobilization" {{ old('status', $project->status ?? $project->project_status) == 'mobilization' ? 'selected' : '' }}>تجهيز الموقع</option>
                    <option value="in_progress" {{ old('status', $project->status ?? $project->project_status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="on_hold" {{ old('status', $project->status ?? $project->project_status) == 'on_hold' ? 'selected' : '' }}>متوقف</option>
                    <option value="suspended" {{ old('status', $project->status ?? $project->project_status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                    <option value="completed" {{ old('status', $project->status ??  $project->project_status) == 'completed' ? 'selected' : '' }}>منتهي</option>
                    <option value="handed_over" {{ old('status', $project->status ?? $project->project_status) == 'handed_over' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="closed" {{ old('status', $project->status ?? $project->project_status) == 'closed' ? 'selected' : '' }}>مغلق</option>
                </select>
            </div>
            
            <div>
                <label style="display:  block; margin-bottom: 5px; font-weight: 600;">الصحة *</label>
                <select name="health" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:  5px; font-family:  'Cairo', sans-serif;">
                    <option value="on_track" {{ old('health', $project->health) == 'on_track' ?  'selected' : '' }}>في المسار</option>
                    <option value="at_risk" {{ old('health', $project->health) == 'at_risk' ? 'selected' : '' }}>في خطر</option>
                    <option value="delayed" {{ old('health', $project->health) == 'delayed' ? 'selected' : '' }}>متأخر</option>
                    <option value="critical" {{ old('health', $project->health) == 'critical' ? 'selected' : '' }}>حرج</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                <select name="priority" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="critical" {{ old('priority', $project->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background:  #0071e3; color:  white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ التعديلات
            </button>
            <a href="{{ route('projects.show', $project) }}" style="padding: 12px 30px; text-decoration: none; color: #666; background: #f8f9fa; border-radius: 8px; display: inline-block;">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection