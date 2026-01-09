@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0 0 10px 0;
    }
    
    .breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: 0.85rem;
        color: #86868b;
    }
    
    .breadcrumb a {
        color: #0071e3;
        text-decoration: none;
    }
    
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
    }
    
    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        background: white;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ff3b30;
    }
    
    .invalid-feedback {
        display: block;
        color: #ff3b30;
        font-size: 0.8rem;
        margin-top: 5px;
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        padding-left: 36px;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f3f4f6;
    }
    
    .btn {
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0071e3, #0077ed);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn-secondary:hover {
        background: #e8e8ed;
    }
</style>

<div class="page-header">
    <h1 class="page-title">إنشاء جدول كميات جديد</h1>
    <div class="breadcrumb">
        <a href="{{ route('boq.index') }}">جداول الكميات</a>
        <span>/</span>
        <span>جديد</span>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('boq.store') }}">
        @csrf
        
        <div class="form-group">
            <label class="form-label required">اسم جدول الكميات</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" 
                   placeholder="مثال: جدول كميات مشروع الطريق الدائري"
                   required>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label class="form-label">الوصف</label>
            <textarea name="description" 
                      class="form-control @error('description') is-invalid @enderror" 
                      placeholder="وصف تفصيلي لجدول الكميات">{{ old('description') }}</textarea>
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label required">النوع</label>
                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="">اختر النوع</option>
                    <option value="tender" {{ old('type') === 'tender' ? 'selected' : '' }}>مناقصة</option>
                    <option value="contract" {{ old('type') === 'contract' ? 'selected' : '' }}>عقد</option>
                    <option value="variation" {{ old('type') === 'variation' ? 'selected' : '' }}>تعديل</option>
                </select>
                @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label required">العملة</label>
                <select name="currency" class="form-control @error('currency') is-invalid @enderror" required>
                    <option value="SAR" {{ old('currency', 'SAR') === 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    <option value="AED" {{ old('currency') === 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                </select>
                @error('currency')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                إنشاء جدول الكميات
            </button>
            <a href="{{ route('boq.index') }}" class="btn btn-secondary">
                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
