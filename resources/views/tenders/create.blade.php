@extends('layouts.app')

@section('content')
<style>
    .tender-form {
        padding: 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 10px 15px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Cairo', sans-serif;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #0071e3;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }
</style>

<div class="tender-form">
    <div class="page-header">
        <h1 class="page-title">إنشاء عطاء جديد</h1>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('tenders.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">الشركة</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">اختر الشركة</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label required">كود العطاء</label>
                    <input type="text" name="code" class="form-input" required placeholder="TND-2026-001">
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">عنوان العطاء</label>
                    <input type="text" name="title" class="form-input" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التقديم</label>
                    <input type="date" name="submission_date" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">القيمة المقدرة (د.أ)</label>
                    <input type="number" step="0.01" name="estimated_value" class="form-input">
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ route('tenders.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">إنشاء العطاء</button>
            </div>
        </form>
    </div>
</div>
@endsection
