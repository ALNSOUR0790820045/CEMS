@extends('layouts.app')

@section('content')
<style>
    .form-container {
        padding: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 10px;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .form-section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0071e3;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
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
        color: #333;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input, .form-select, .form-textarea {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .form-checkbox input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        padding-top: 20px;
        border-top: 1px solid #eee;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #f5f5f5;
    }

    .error-message {
        color: #ff3b30;
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">تعديل المعدة: {{ $equipment->name }}</h1>
    </div>

    <form method="POST" action="{{ route('equipment.update', $equipment) }}" class="form-card">
        @csrf
        @method('PUT')

        <!-- Copy all form sections from create.blade.php but with old() values falling back to $equipment values -->
        
        <!-- معلومات أساسية -->
        <div class="form-section">
            <h3 class="section-title">معلومات أساسية</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">رقم المعدة</label>
                    <input type="text" name="equipment_number" class="form-input" value="{{ old('equipment_number', $equipment->equipment_number) }}" required>
                    @error('equipment_number')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">اسم المعدة</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $equipment->name) }}" required>
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" class="form-input" value="{{ old('name_en', $equipment->name_en) }}">
                </div>

                <div class="form-group">
                    <label class="form-label required">التصنيف</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $equipment->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-textarea">{{ old('description', $equipment->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Rest of form sections similar to create but using $equipment values -->

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                تحديث المعدة
            </button>
            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">
                <i data-lucide="x"></i>
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
