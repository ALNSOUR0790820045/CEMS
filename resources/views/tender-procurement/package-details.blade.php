@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .detail-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .detail-card h3 {
        font-size: 0.85rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 15px 0;
        font-weight: 600;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.9rem;
        color: #86868b;
    }

    .detail-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-planned { background: #e3f2fd; color: #1976d2; }
    .badge-approved { background: #e8f5e9; color: #388e3c; }

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

    .actions-bar {
        display: flex;
        gap: 10px;
    }

    .content-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 15px 0;
    }

    .suppliers-list {
        margin-top: 20px;
    }

    .supplier-card {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .supplier-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .supplier-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
    }

    .supplier-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $package->package_name }}</h1>
        <p style="color: #86868b; font-size: 0.9rem; margin: 5px 0 0 0;">{{ $package->package_code }}</p>
    </div>
    <div class="actions-bar">
        <a href="{{ route('tender-procurement.index', $tender->id) }}" class="btn btn-secondary">رجوع</a>
        <a href="{{ route('tender-procurement.suppliers', [$tender->id, $package->id]) }}" class="btn btn-primary">إدارة الموردين</a>
    </div>
</div>

<div class="details-grid">
    <div class="detail-card">
        <h3>معلومات الحزمة</h3>
        <div class="detail-row">
            <span class="detail-label">نوع الشراء</span>
            <span class="detail-value">{{ $package->getProcurementTypeLabel() }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">التصنيف</span>
            <span class="detail-value">{{ $package->getCategoryLabel() }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">الاستراتيجية</span>
            <span class="detail-value">{{ $package->getStrategyLabel() }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">الحالة</span>
            <span class="detail-value">
                <span class="badge badge-{{ str_replace('_', '-', $package->status) }}">
                    {{ $package->status_label }}
                </span>
            </span>
        </div>
    </div>

    <div class="detail-card">
        <h3>التكلفة والجدول الزمني</h3>
        <div class="detail-row">
            <span class="detail-label">القيمة المقدرة</span>
            <span class="detail-value">{{ number_format($package->estimated_value, 2) }} ريال</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">تاريخ الحاجة</span>
            <span class="detail-value">{{ $package->required_by_date?->format('Y-m-d') ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">مدة التوريد</span>
            <span class="detail-value">{{ $package->lead_time_days ?? '-' }} يوم</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">تاريخ بدء الشراء</span>
            <span class="detail-value">{{ $package->procurement_start?->format('Y-m-d') ?? '-' }}</span>
        </div>
    </div>

    <div class="detail-card">
        <h3>المتطلبات</h3>
        <div class="detail-row">
            <span class="detail-label">مواصفات فنية</span>
            <span class="detail-value">{{ $package->requires_technical_specs ? 'مطلوبة' : 'غير مطلوبة' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">عينات</span>
            <span class="detail-value">{{ $package->requires_samples ? 'مطلوبة' : 'غير مطلوبة' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ضمان</span>
            <span class="detail-value">{{ $package->requires_warranty ? 'مطلوب' : 'غير مطلوب' }}</span>
        </div>
        @if($package->warranty_months)
        <div class="detail-row">
            <span class="detail-label">مدة الضمان</span>
            <span class="detail-value">{{ $package->warranty_months }} شهر</span>
        </div>
        @endif
    </div>
</div>

@if($package->description)
<div class="content-section">
    <h3 class="section-title">الوصف</h3>
    <p style="color: #86868b; line-height: 1.6;">{{ $package->description }}</p>
</div>
@endif

@if($package->scope_of_work)
<div class="content-section">
    <h3 class="section-title">نطاق العمل</h3>
    <p style="color: #86868b; line-height: 1.6; white-space: pre-wrap;">{{ $package->scope_of_work }}</p>
</div>
@endif

@if($package->responsible)
<div class="content-section">
    <h3 class="section-title">المسؤول</h3>
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #0071e3, #00c4cc); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
            {{ $package->responsible->initials }}
        </div>
        <div>
            <div style="font-weight: 600; font-size: 1rem;">{{ $package->responsible->name }}</div>
            <div style="color: #86868b; font-size: 0.85rem;">{{ $package->responsible->email }}</div>
        </div>
    </div>
</div>
@endif

@if($package->procurementSuppliers->isNotEmpty())
<div class="content-section">
    <h3 class="section-title">الموردون المحتملون</h3>
    <div class="suppliers-list">
        @foreach($package->procurementSuppliers as $procSupplier)
        <div class="supplier-card">
            <div class="supplier-header">
                <span class="supplier-name">{{ $procSupplier->supplier->name }}</span>
                @if($procSupplier->is_recommended)
                    <span class="badge" style="background: #ffd700; color: #856404;">موصى به</span>
                @endif
            </div>
            <div class="supplier-info">
                @if($procSupplier->quoted_price)
                <div>
                    <div style="font-size: 0.8rem; color: #86868b;">السعر المقدم</div>
                    <div style="font-weight: 600;">{{ number_format($procSupplier->quoted_price, 2) }} ريال</div>
                </div>
                @endif
                @if($procSupplier->delivery_days)
                <div>
                    <div style="font-size: 0.8rem; color: #86868b;">مدة التوريد</div>
                    <div style="font-weight: 600;">{{ $procSupplier->delivery_days }} يوم</div>
                </div>
                @endif
                @if($procSupplier->score)
                <div>
                    <div style="font-size: 0.8rem; color: #86868b;">النقاط</div>
                    <div style="font-weight: 600;">{{ $procSupplier->score }}/100</div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<script>
    lucide.createIcons();
</script>
@endsection
