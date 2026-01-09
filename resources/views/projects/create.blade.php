@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; font-size: 2rem; font-weight: 700;">إضافة مشروع جديد</h1>
    
    <form method="POST" action="{{ route('projects.store') }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        
        <!-- Basic Info Section -->
        <div style="margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px solid #f5f5f7;">
            <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">المعلومات الأساسية</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap:  20px; margin-bottom: 20px;">
                <div>
                    <label style="display:  block; margin-bottom: 8px; font-weight: 600;">كود المشروع</label>
                    <input type="text" value="{{ $projectCode }}" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius:  8px; background: #f5f5f7; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع المشروع *</label>
                    <select name="project_type" required style="width:  100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="lump_sum" {{ old('project_type') == 'lump_sum' ? 'selected' :  '' }}>مقطوعية</option>
                        <option value="unit_price" {{ old('project_type') == 'unit_price' ? 'selected' : '' }}>فئة سعر</option>
                        <option value="cost_plus" {{ old('project_type') == 'cost_plus' ? 'selected' : '' }}>تكلفة زائد</option>
                        <option value="design_build" {{ old('project_type') == 'design_build' ?  'selected' : '' }}>تصميم وتنفيذ</option>
                    </select>
                </div>
            </div>
            
            <div style="display:  grid; grid-template-columns:  1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">اسم المشروع *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" style="width: 100%; padding:  12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('name')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">اسم المشروع (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap:  20px; margin-bottom:  20px;">
                <div>
                    <label style="display: block; margin-bottom:  8px; font-weight:  600;">العميل *</label>
                    <select name="client_id" required style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر العميل</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom:  8px; font-weight:  600;">حالة المشروع *</label>
                    <select name="project_status" required style="width:  100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="tendering" {{ old('project_status') == 'tendering' ? 'selected' : '' }}>عطاء</option>
                        <option value="awarded" {{ old('project_status') == 'awarded' ? 'selected' : '' }}>مرسى</option>
                        <option value="mobilization" {{ old('project_status') == 'mobilization' ?  'selected' : '' }}>حشد</option>
                        <option value="execution" {{ old('project_status') == 'execution' ? 'selected' : '' }}>تنفيذ</option>
                        <option value="on_hold" {{ old('project_status') == 'on_hold' ? 'selected' : '' }}>متوقف</option>
                        <option value="completed" {{ old('project_status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="closed" {{ old('project_status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contract Details Section -->
        <div style="margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px solid #f5f5f7;">
            <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">تفاصيل العقد</h2>
            
            <div style="display:  grid; grid-template-columns:  1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">قيمة العقد *</label>
                    <input type="number" name="contract_value" required min="0" step="0.01" value="{{ old('contract_value') }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('contract_value')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom:  8px; font-weight:  600;">العملة *</label>
                    <select name="contract_currency_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('contract_currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">تاريخ البدء *</label>
                    <input type="date" name="contract_start_date" required value="{{ old('contract_start_date') }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('contract_start_date')<span style="color: #c62828; font-size:  0.85rem;">{{ $message }}</span>@enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">تاريخ الانتهاء *</label>
                    <input type="date" name="contract_end_date" required value="{{ old('contract_end_date') }}" style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    @error('contract_end_date')<span style="color: #c62828; font-size: 0.85rem;">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div style="margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px solid #f5f5f7;">
            <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">الموقع</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">الدولة</label>
                    <select name="country_id" id="country_id" style="width: 100%; padding:  12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر الدولة</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom:  8px; font-weight:  600;">المدينة</label>
                    <select name="city_id" id="city_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" data-country="{{ $city->country_id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">عنوان الموقع</label>
                <textarea name="site_address" rows="2" style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('site_address') }}</textarea>
            </div>
        </div>

        <!-- Team Section -->
        <div style="margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px solid #f5f5f7;">
            <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">الفريق</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">مدير المشروع *</label>
                    <select name="project_manager_id" required style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر مدير المشروع</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('project_manager_id')<span style="color: #c62828; font-size:  0.85rem;">{{ $message }}</span>@enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">مهندس الموقع</label>
                    <select name="site_engineer_id" style="width: 100%; padding:  12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر مهندس الموقع</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('site_engineer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">مدير العقود</label>
                    <select name="contract_manager_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر مدير العقود</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('contract_manager_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div style="margin-bottom: 40px;">
            <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">معلومات إضافية</h2>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">وصف المشروع</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('description') }}</textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">ملاحظات</label>
                <textarea name="notes" rows="3" style="width: 100%; padding: 12px; border:  1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">{{ old('notes') }}</textarea>
            </div>
            
            <div>
                <label style="display:  flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px;">
                    <span style="font-weight: 600;">المشروع نشط</span>
                </label>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1rem;">حفظ المشروع</button>
            <a href="{{ route('projects.index') }}" style="padding: 14px 40px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Filter cities by country
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const citySelect = document.getElementById('city_id');
        const options = citySelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            if (option.dataset.country === countryId || countryId === '') {
                option.style.display = 'block';
            } else {
                option.style. display = 'none';
            }
        });
        
        citySelect.value = '';
    });
</script>
@endpush
@endsection