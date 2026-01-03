@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل العميل: {{ $client->name }}</h1>
    
    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('clients.update', $client) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <!-- Tabs Navigation -->
        <div style="display: flex; gap: 10px; border-bottom: 2px solid #f0f0f0; margin-bottom: 30px;">
            <button type="button" class="tab-btn active" data-tab="basic" style="padding: 12px 24px; background: none; border: none; border-bottom: 2px solid #0071e3; color: #0071e3; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer; margin-bottom: -2px;">
                المعلومات الأساسية
            </button>
            <button type="button" class="tab-btn" data-tab="legal" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
                المعلومات القانونية
            </button>
            <button type="button" class="tab-btn" data-tab="contact" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
                معلومات الاتصال
            </button>
            <button type="button" class="tab-btn" data-tab="financial" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
                الإعدادات المالية
            </button>
        </div>

        <!-- Tab: Basic Information -->
        <div class="tab-content active" data-tab="basic">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود العميل</label>
                    <input type="text" value="{{ $client->client_code }}" readonly 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f5f5f7; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم العميل (عربي) *</label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم العميل (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $client->name_en) }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع العميل *</label>
                    <select name="client_type" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر النوع</option>
                        <option value="government" {{ old('client_type', $client->client_type) == 'government' ? 'selected' : '' }}>حكومي</option>
                        <option value="semi_government" {{ old('client_type', $client->client_type) == 'semi_government' ? 'selected' : '' }}>شبه حكومي</option>
                        <option value="private_sector" {{ old('client_type', $client->client_type) == 'private_sector' ? 'selected' : '' }}>قطاع خاص</option>
                        <option value="individual" {{ old('client_type', $client->client_type) == 'individual' ? 'selected' : '' }}>فرد</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">فئة العميل *</label>
                    <select name="client_category" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر الفئة</option>
                        <option value="strategic" {{ old('client_category', $client->client_category) == 'strategic' ? 'selected' : '' }}>استراتيجي</option>
                        <option value="preferred" {{ old('client_category', $client->client_category) == 'preferred' ? 'selected' : '' }}>مفضل</option>
                        <option value="regular" {{ old('client_category', $client->client_category) == 'regular' ? 'selected' : '' }}>عادي</option>
                        <option value="one_time" {{ old('client_category', $client->client_category) == 'one_time' ? 'selected' : '' }}>لمرة واحدة</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التقييم</label>
                    <select name="rating" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">بدون تقييم</option>
                        <option value="excellent" {{ old('rating') == 'excellent' ? 'selected' : '' }}>ممتاز (⭐⭐⭐⭐⭐)</option>
                        <option value="good" {{ old('rating') == 'good' ? 'selected' : '' }}>جيد (⭐⭐⭐⭐)</option>
                        <option value="average" {{ old('rating') == 'average' ? 'selected' : '' }}>متوسط (⭐⭐⭐)</option>
                        <option value="poor" {{ old('rating') == 'poor' ? 'selected' : '' }}>ضعيف (⭐⭐)</option>
                    </select>
                </div>
                
                <div style="grid-column: 1 / -1;">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        العميل نشط
                    </label>
                </div>
            </div>
        </div>

        <!-- Tab: Legal Information -->
        <div class="tab-content" data-tab="legal" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">السجل التجاري</label>
                    <input type="text" name="commercial_registration" value="{{ old('commercial_registration') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرقم الضريبي</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم الترخيص</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <!-- Tab: Contact Information -->
        <div class="tab-content" data-tab="contact" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة</label>
                    <input type="text" name="country" value="{{ old('country', 'JO') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدينة</label>
                    <input type="text" name="city" value="{{ old('city') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان</label>
                    <textarea name="address" rows="3" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('address') }}</textarea>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">صندوق البريد</label>
                    <input type="text" name="po_box" value="{{ old('po_box') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرمز البريدي</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجوال</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الفاكس</label>
                    <input type="text" name="fax" value="{{ old('fax') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الموقع الإلكتروني</label>
                    <input type="url" name="website" value="{{ old('website') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="grid-column: 1 / -1; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                    <h3 style="margin-bottom: 15px;">شخص الاتصال الرئيسي</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم الكامل</label>
                            <input type="text" name="primary_contact_person" value="{{ old('primary_contact_person') }}" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المسمى الوظيفي</label>
                            <input type="text" name="primary_contact_title" value="{{ old('primary_contact_title') }}" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
                            <input type="text" name="primary_contact_phone" value="{{ old('primary_contact_phone') }}" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
                            <input type="email" name="primary_contact_email" value="{{ old('primary_contact_email') }}" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Financial Settings -->
        <div class="tab-content" data-tab="financial" style="display: none;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">شروط الدفع *</label>
                    <select name="payment_terms" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="immediate" {{ old('payment_terms') == 'immediate' ? 'selected' : '' }}>فوري</option>
                        <option value="7_days" {{ old('payment_terms') == '7_days' ? 'selected' : '' }}>7 أيام</option>
                        <option value="15_days" {{ old('payment_terms') == '15_days' ? 'selected' : '' }}>15 يوم</option>
                        <option value="30_days" {{ old('payment_terms', '30_days') == '30_days' ? 'selected' : '' }}>30 يوم</option>
                        <option value="45_days" {{ old('payment_terms') == '45_days' ? 'selected' : '' }}>45 يوم</option>
                        <option value="60_days" {{ old('payment_terms') == '60_days' ? 'selected' : '' }}>60 يوم</option>
                        <option value="90_days" {{ old('payment_terms') == '90_days' ? 'selected' : '' }}>90 يوم</option>
                        <option value="custom" {{ old('payment_terms') == 'custom' ? 'selected' : '' }}>مخصص</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحد الائتماني</label>
                    <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة</label>
                    <input type="text" name="currency" value="{{ old('currency', 'JOD') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">حساب الذمم المدينة</label>
                    <input type="text" name="gl_account" value="{{ old('gl_account') }}" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
                    <textarea name="notes" rows="4" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ
            </button>
            <a href="{{ route('clients.index') }}" style="padding: 12px 30px; text-decoration: none; color: #666; background: #f5f5f7; border-radius: 8px; font-family: 'Cairo', sans-serif; font-weight: 600;">
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Update buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.borderBottom = 'none';
            b.style.color = '#666';
        });
        this.style.borderBottom = '2px solid #0071e3';
        this.style.color = '#0071e3';
        
        // Update content
        document.querySelectorAll('.tab-content').forEach(c => {
            c.style.display = 'none';
        });
        document.querySelector(`.tab-content[data-tab="${tab}"]`).style.display = 'block';
    });
});
</script>
@endsection
