@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 5px 0;
    }

    .comparison-table {
        background: white;
        border-radius: 12px;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .table thead {
        background: #f5f5f7;
    }

    .table th {
        padding: 15px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 15px;
        border-top: 1px solid var(--border);
        font-size: 0.9rem;
    }

    .table tbody tr:hover {
        background: #f9f9f9;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-recommended {
        background: #ffd700;
        color: #856404;
    }

    .score-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .score-high { background: #e8f5e9; color: #388e3c; }
    .score-medium { background: #fff3e0; color: #f57c00; }
    .score-low { background: #ffebee; color: #d32f2f; }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .add-supplier-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
</style>

<div class="page-header">
    <h1 class="page-title">مقارنة الموردين - {{ $package->package_name }}</h1>
    <p style="color: #86868b; font-size: 0.9rem; margin: 5px 0 0 0;">{{ $package->package_code }}</p>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="add-supplier-form">
    <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 600;">إضافة مورد</h3>
    <form method="POST" action="{{ route('tender-procurement.suppliers.add', [$tender->id, $package->id]) }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>المورد</label>
                <select name="supplier_id" required>
                    <option value="">اختر المورد</option>
                    @foreach($availableSuppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>السعر المقدم (ريال)</label>
                <input type="number" name="quoted_price" step="0.01" min="0" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>مدة التوريد (أيام)</label>
                <input type="number" name="delivery_days" min="0" placeholder="عدد الأيام">
            </div>

            <div class="form-group">
                <label>النقاط (0-100)</label>
                <input type="number" name="score" min="0" max="100" placeholder="0-100">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>شروط الدفع</label>
                <textarea name="payment_terms" placeholder="تفاصيل شروط الدفع"></textarea>
            </div>

            <div class="form-group">
                <label>الالتزام الفني</label>
                <textarea name="technical_compliance" placeholder="ملاحظات الالتزام الفني"></textarea>
            </div>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="is_recommended" id="is_recommended" value="1">
            <label for="is_recommended">موصى به</label>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">إضافة المورد</button>
            <a href="{{ route('tender-procurement.show', [$tender->id, $package->id]) }}" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>

<div class="comparison-table">
    @if($package->procurementSuppliers->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: #86868b;">
            <i data-lucide="users" style="width: 60px; height: 60px; margin-bottom: 15px;"></i>
            <p>لم يتم إضافة موردين بعد</p>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>المورد</th>
                    <th>السعر المقدم</th>
                    <th>مدة التوريد</th>
                    <th>شروط الدفع</th>
                    <th>الالتزام الفني</th>
                    <th>النقاط</th>
                    <th>التوصية</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($package->procurementSuppliers->sortByDesc('score') as $procSupplier)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $procSupplier->supplier->name }}</strong>
                            @if($procSupplier->supplier->rating)
                                <div style="font-size: 0.75rem; color: #86868b;">تصنيف: {{ $procSupplier->supplier->rating }}</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($procSupplier->quoted_price)
                            <strong>{{ number_format($procSupplier->quoted_price, 2) }}</strong> ريال
                        @else
                            <span style="color: #86868b;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($procSupplier->delivery_days)
                            {{ $procSupplier->delivery_days }} يوم
                        @else
                            <span style="color: #86868b;">-</span>
                        @endif
                    </td>
                    <td style="max-width: 200px;">
                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $procSupplier->payment_terms ?? '-' }}
                        </div>
                    </td>
                    <td style="max-width: 200px;">
                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $procSupplier->technical_compliance ?? '-' }}
                        </div>
                    </td>
                    <td>
                        @if($procSupplier->score)
                            <div class="score-badge score-{{ $procSupplier->score >= 75 ? 'high' : ($procSupplier->score >= 50 ? 'medium' : 'low') }}">
                                {{ $procSupplier->score }}
                            </div>
                        @else
                            <span style="color: #86868b;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($procSupplier->is_recommended)
                            <span class="badge badge-recommended">✓ موصى به</span>
                        @else
                            <span style="color: #86868b;">-</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editSupplier({{ $procSupplier->id }})">
                            تعديل
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if($package->procurementSuppliers->isNotEmpty())
<div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 600;">ملخص المقارنة</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div>
            <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">أقل سعر</div>
            <div style="font-size: 1.3rem; font-weight: 700; color: var(--text);">
                {{ number_format($package->procurementSuppliers->whereNotNull('quoted_price')->min('quoted_price') ?? 0, 2) }} ريال
            </div>
        </div>

        <div>
            <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">متوسط السعر</div>
            <div style="font-size: 1.3rem; font-weight: 700; color: var(--text);">
                {{ number_format($package->procurementSuppliers->whereNotNull('quoted_price')->avg('quoted_price') ?? 0, 2) }} ريال
            </div>
        </div>

        <div>
            <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">أقصر مدة توريد</div>
            <div style="font-size: 1.3rem; font-weight: 700; color: var(--text);">
                {{ $package->procurementSuppliers->whereNotNull('delivery_days')->min('delivery_days') ?? '-' }} 
                @if($package->procurementSuppliers->whereNotNull('delivery_days')->min('delivery_days'))
                    يوم
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">أعلى تقييم</div>
            <div style="font-size: 1.3rem; font-weight: 700; color: var(--text);">
                {{ $package->procurementSuppliers->whereNotNull('score')->max('score') ?? '-' }}
                @if($package->procurementSuppliers->whereNotNull('score')->max('score'))
                    / 100
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
    lucide.createIcons();

    function editSupplier(supplierId) {
        // This would open a modal or redirect to edit page
        alert('تعديل المورد رقم: ' + supplierId);
    }
</script>
@endsection
