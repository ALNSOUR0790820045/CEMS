@extends('layouts.app')

@section('content')
<style>
    .form-container {
        padding: 40px;
        max-width: 900px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 30px;
    }

    .form-card {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-grid.single {
        grid-template-columns: 1fr;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 0;
    }

    .checkbox-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-label {
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #005bb5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #666;
    }

    .btn-secondary:hover {
        background: #e5e5e7;
    }

    .error-message {
        color: #ff3b30;
        font-size: 0.8rem;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <h1 class="page-title">إضافة فرع جديد</h1>

    <div class="form-card">
        <form method="POST" action="{{ route('branches.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">الشركة</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">اختر الشركة</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">كود الفرع</label>
                    <input type="text" name="code" class="form-input" value="{{ old('code') }}" required placeholder="BR001">
                    @error('code')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">اسم الفرع (عربي)</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="الفرع الرئيسي">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">اسم الفرع (إنجليزي)</label>
                    <input type="text" name="name_en" class="form-input" value="{{ old('name_en') }}" placeholder="Main Branch">
                    @error('name_en')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+966 50 123 4567">
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="branch@company.com">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid single">
                <div class="form-group">
                    <label class="form-label">المدينة</label>
                    <select name="city_id" class="form-select">
                        <option value="">اختر المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid single">
                <div class="form-group">
                    <label class="form-label">العنوان</label>
                    <textarea name="address" class="form-textarea" placeholder="اكتب العنوان الكامل...">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_main" id="is_main" class="checkbox-input" value="1" {{ old('is_main') ? 'checked' : '' }}>
                    <label for="is_main" class="checkbox-label">فرع رئيسي</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="is_active" id="is_active" class="checkbox-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="checkbox-label">نشط</label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
