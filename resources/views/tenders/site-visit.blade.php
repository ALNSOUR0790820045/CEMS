@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">تسجيل زيارة موقع</h1>
    <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        رجوع
    </a>
</div>

<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">{{ $tender->tender_name }}</h2>
    <p style="color: #86868b; margin-bottom: 30px;">{{ $tender->tender_number }}</p>

    <form method="POST" action="#" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">تاريخ الزيارة</label>
                <input type="date" name="visit_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">وقت الزيارة</label>
                <input type="time" name="visit_time" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">الحاضرون من فريقنا</label>
            <textarea name="attendees" class="form-control" rows="3" placeholder="أدخل أسماء الحاضرين (سطر لكل شخص)"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">الملاحظات والمشاهدات</label>
            <textarea name="observations" class="form-control" rows="6" placeholder="سجل ملاحظاتك عن الموقع..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">صور الموقع</label>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
            <small style="color: #86868b; font-size: 0.8rem;">يمكنك اختيار عدة صور</small>
        </div>

        <div class="form-group">
            <label class="form-label">الإحداثيات (GPS)</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="latitude" class="form-control" placeholder="خط العرض">
                <input type="text" name="longitude" class="form-control" placeholder="خط الطول">
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e5e7;">
            <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
                إلغاء
            </a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                حفظ الزيارة
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
