@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0 0 10px 0; font-size: 2rem; font-weight: 700;">تعديل المستودع</h1>
        <a href="{{ route('warehouses.index') }}" style="color: #0071e3; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
            العودة للقائمة
        </a>
    </div>

    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('warehouses.update', $warehouse) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود المستودع *</label>
                <input type="text" name="code" value="{{ old('code', $warehouse->code) }}" required placeholder="WH001" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الشركة *</label>
                <select name="company_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الشركة</option>
                    @if($warehouse->company_id)
                    <option value="{{ $warehouse->company_id }}" selected>{{ $warehouse->company->name }}</option>
                    @endif
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المستودع (عربي) *</label>
                <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المستودع (إنجليزي)</label>
                <input type="text" name="name_en" value="{{ old('name_en', $warehouse->name_en) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الفرع</label>
                <select name="branch_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الفرع</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $warehouse->branch_id) == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدير</label>
                <select name="manager_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المدير</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('manager_id', $warehouse->manager_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $warehouse->phone) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدينة</label>
                <select name="city_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المدينة</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $warehouse->city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان</label>
            <textarea name="address" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('address', $warehouse->address) }}</textarea>
        </div>

        <div style="display: flex; gap: 30px; margin-bottom: 30px; padding: 15px; background: #f5f5f7; border-radius: 8px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_main" value="1" {{ old('is_main', $warehouse->is_main) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 600;">مستودع رئيسي</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 600;">نشط</span>
            </label>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; transition: all 0.2s;">
                تحديث
            </button>
            <a href="{{ route('warehouses.index') }}" style="background: #f5f5f7; color: #333; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">
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
