@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل الحساب: {{ $account->name }}</h1>
    
    <form method="POST" action="{{ route('accounts.update', $account->id) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود الحساب *</label>
                <input type="text" name="code" value="{{ old('code', $account->code) }}" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                    placeholder="مثال: 1-1-001">
                @error('code')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحساب الأب</label>
                <select name="parent_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">بدون (حساب رئيسي)</option>
                    @foreach($parentAccounts as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $account->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->code }} - {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الحساب (عربي) *</label>
            <input type="text" name="name" value="{{ old('name', $account->name) }}" required 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                placeholder="مثال: الصندوق">
            @error('name')
                <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الحساب (إنجليزي)</label>
            <input type="text" name="name_en" value="{{ old('name_en', $account->name_en) }}" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                placeholder="Example: Cash">
            @error('name_en')
                <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع الحساب *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النوع</option>
                    <option value="asset" {{ old('type', $account->type) == 'asset' ? 'selected' : '' }}>أصول (Assets)</option>
                    <option value="liability" {{ old('type', $account->type) == 'liability' ? 'selected' : '' }}>خصوم (Liabilities)</option>
                    <option value="equity" {{ old('type', $account->type) == 'equity' ? 'selected' : '' }}>حقوق ملكية (Equity)</option>
                    <option value="revenue" {{ old('type', $account->type) == 'revenue' ? 'selected' : '' }}>إيرادات (Revenue)</option>
                    <option value="expense" {{ old('type', $account->type) == 'expense' ? 'selected' : '' }}>مصروفات (Expenses)</option>
                </select>
                @error('type')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">طبيعة الحساب *</label>
                <select name="nature" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الطبيعة</option>
                    <option value="debit" {{ old('nature', $account->nature) == 'debit' ? 'selected' : '' }}>مدين (Debit)</option>
                    <option value="credit" {{ old('nature', $account->nature) == 'credit' ? 'selected' : '' }}>دائن (Credit)</option>
                </select>
                @error('nature')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرصيد الافتتاحي</label>
                <input type="number" name="opening_balance" value="{{ old('opening_balance', $account->opening_balance) }}" step="0.01"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('opening_balance')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرصيد الحالي</label>
                <input type="number" name="current_balance" value="{{ old('current_balance', $account->current_balance) }}" step="0.01"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('current_balance')
                    <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
            <textarea name="description" rows="3" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;"
                placeholder="وصف اختياري للحساب">{{ old('description', $account->description) }}</textarea>
            @error('description')
                <span style="color: #dc3545; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 20px; display: flex; gap: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_parent" value="1" {{ old('is_parent', $account->is_parent) ? 'checked' : '' }}
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span>حساب أب (يحتوي على حسابات فرعية)</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }}
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span>الحساب نشط</span>
            </label>
        </div>

        <div style="padding-top: 20px; border-top: 1px solid #eee;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                تحديث
            </button>
            <a href="{{ route('accounts.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; display: inline-block;">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
