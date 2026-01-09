@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">إضافة مورد جديد</h1>
    </div>

    <form method="POST" action="{{ route('vendors.store') }}" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        @csrf

        <!-- Vendor Code -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود المورد *</label>
            <input type="text" name="vendor_code" value="{{ old('vendor_code', $vendorCode) }}" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif; background: #f5f5f5;">
            @error('vendor_code')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Names -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المورد (عربي) *</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('name')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المورد (إنجليزي)</label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('name_en')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Type and Category -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع المورد *</label>
                <select name="vendor_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النوع</option>
                    <option value="materials_supplier" {{ old('vendor_type') == 'materials_supplier' ? 'selected' : '' }}>مورد مواد</option>
                    <option value="equipment_supplier" {{ old('vendor_type') == 'equipment_supplier' ? 'selected' : '' }}>مورد معدات</option>
                    <option value="services_provider" {{ old('vendor_type') == 'services_provider' ? 'selected' : '' }}>مزود خدمات</option>
                    <option value="subcontractor" {{ old('vendor_type') == 'subcontractor' ? 'selected' : '' }}>مقاول باطن</option>
                    <option value="consultant" {{ old('vendor_type') == 'consultant' ? 'selected' : '' }}>استشاري</option>
                </select>
                @error('vendor_type')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تصنيف المورد</label>
                <select name="vendor_category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="regular" {{ old('vendor_category', 'regular') == 'regular' ? 'selected' : '' }}>عادي</option>
                    <option value="strategic" {{ old('vendor_category') == 'strategic' ? 'selected' : '' }}>استراتيجي</option>
                    <option value="preferred" {{ old('vendor_category') == 'preferred' ? 'selected' : '' }}>مفضل</option>
                    <option value="blacklisted" {{ old('vendor_category') == 'blacklisted' ? 'selected' : '' }}>محظور</option>
                </select>
                @error('vendor_category')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Legal Information -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">السجل التجاري</label>
                <input type="text" name="commercial_registration" value="{{ old('commercial_registration') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('commercial_registration')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرقم الضريبي</label>
                <input type="text" name="tax_number" value="{{ old('tax_number') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('tax_number')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم الترخيص</label>
                <input type="text" name="license_number" value="{{ old('license_number') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('license_number')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Contact Information -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('phone')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجوال</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('mobile')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('email')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Address -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان</label>
            <textarea name="address" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">{{ old('address') }}</textarea>
            @error('address')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Payment Terms and Credit Limit -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">شروط الدفع</label>
                <select name="payment_terms" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="30_days" {{ old('payment_terms', '30_days') == '30_days' ? 'selected' : '' }}>30 يوم</option>
                    <option value="cod" {{ old('payment_terms') == 'cod' ? 'selected' : '' }}>نقداً عند التسليم</option>
                    <option value="7_days" {{ old('payment_terms') == '7_days' ? 'selected' : '' }}>7 أيام</option>
                    <option value="15_days" {{ old('payment_terms') == '15_days' ? 'selected' : '' }}>15 يوم</option>
                    <option value="45_days" {{ old('payment_terms') == '45_days' ? 'selected' : '' }}>45 يوم</option>
                    <option value="60_days" {{ old('payment_terms') == '60_days' ? 'selected' : '' }}>60 يوم</option>
                    <option value="90_days" {{ old('payment_terms') == '90_days' ? 'selected' : '' }}>90 يوم</option>
                    <option value="custom" {{ old('payment_terms') == 'custom' ? 'selected' : '' }}>مخصص</option>
                </select>
                @error('payment_terms')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">حد الائتمان</label>
                <input type="number" name="credit_limit" value="{{ old('credit_limit') }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                @error('credit_limit')
                <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Notes -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">{{ old('notes') }}</textarea>
            @error('notes')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status Checkboxes -->
        <div style="display: flex; gap: 30px; margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>نشط</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_approved" value="1" {{ old('is_approved') ? 'checked' : '' }}>
                <span>معتمد</span>
            </label>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ
            </button>
            <a href="{{ route('vendors.index') }}" style="padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; display: inline-block; font-weight: 600;">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
