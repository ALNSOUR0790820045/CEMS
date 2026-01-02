@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
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

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: #f5f5f7;
        padding: 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        border-bottom: 2px solid #e5e5e7;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid #e5e5e7;
        font-size: 0.9rem;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
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

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #86868b;
    }
</style>

<div class="page-header">
    <h1 class="page-title">تحليل المنافسين</h1>
    <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        رجوع
    </a>
</div>

<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">{{ $tender->tender_name }}</h2>
    <p style="color: #86868b; margin-bottom: 30px;">{{ $tender->tender_number }}</p>

    @if($tender->competitors->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>الشركة</th>
                    <th>التصنيف</th>
                    <th>السعر المتوقع</th>
                    <th>نقاط القوة</th>
                    <th>نقاط الضعف</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tender->competitors as $competitor)
                <tr>
                    <td><strong>{{ $competitor->company_name }}</strong></td>
                    <td>
                        <span class="badge" style="background: 
                            {{ $competitor->classification == 'strong' ? '#ffebee' : ($competitor->classification == 'medium' ? '#fff3e0' : '#e8f5e9') }};
                            color: {{ $competitor->classification == 'strong' ? '#d32f2f' : ($competitor->classification == 'medium' ? '#f57c00' : '#388e3c') }};">
                            {{ $competitor->classification == 'strong' ? 'قوي' : ($competitor->classification == 'medium' ? 'متوسط' : 'ضعيف') }}
                        </span>
                    </td>
                    <td>{{ number_format($competitor->estimated_price ?? 0, 2) }}</td>
                    <td>{{ Str::limit($competitor->strengths ?? '-', 50) }}</td>
                    <td>{{ Str::limit($competitor->weaknesses ?? '-', 50) }}</td>
                    <td>
                        <a href="#" style="color: #0071e3; text-decoration: none;">تعديل</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i data-lucide="users" style="width: 64px; height: 64px; margin-bottom: 20px; color: #d2d2d7;"></i>
            <p>لم يتم إضافة منافسين بعد</p>
        </div>
    @endif
</div>

<!-- Add Competitor Form -->
<div class="card">
    <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">
        <i data-lucide="user-plus" style="width: 24px; height: 24px; vertical-align: middle;"></i>
        إضافة منافس جديد
    </h3>

    <form method="POST" action="{{ route('tenders.competitors.store', $tender) }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">اسم الشركة</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">التصنيف</label>
                <select name="classification" class="form-control" required>
                    <option value="">اختر التصنيف</option>
                    <option value="strong">قوي</option>
                    <option value="medium">متوسط</option>
                    <option value="weak">ضعيف</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">السعر المتوقع</label>
                <input type="number" name="estimated_price" class="form-control" step="0.01">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">نقاط القوة</label>
            <textarea name="strengths" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">نقاط الضعف</label>
            <textarea name="weaknesses" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e5e7;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                إضافة المنافس
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
