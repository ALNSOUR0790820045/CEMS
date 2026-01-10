@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0071e3, #0077ed);
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .boq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .boq-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.2s;
        border: 1px solid rgba(0,0,0,0.06);
    }
    
    .boq-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .boq-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }
    
    .boq-number {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0071e3;
        font-family: 'SF Mono', monospace;
    }
    
    .boq-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-draft { background: #f3f4f6; color: #6b7280; }
    .status-submitted { background: #dbeafe; color: #1e40af; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-revised { background: #fef3c7; color: #92400e; }
    
    .boq-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
    }
    
    .boq-type {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 15px;
    }
    
    .boq-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }
    
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .meta-label {
        font-size: 0.75rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .meta-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .boq-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }
    
    .btn-sm {
        padding: 6px 14px;
        font-size: 0.8rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-view {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn-view:hover {
        background: #e8e8ed;
    }
    
    .btn-edit {
        background: #0071e3;
        color: white;
    }
    
    .btn-edit:hover {
        background: #0077ed;
    }
    
    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .empty-icon {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    
    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 10px;
    }
    
    .empty-text {
        font-size: 1rem;
        color: #86868b;
        margin-bottom: 25px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">جداول الكميات</h1>
    <a href="{{ route('boq.create') }}" class="btn-primary">
        <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
        إنشاء جدول كميات جديد
    </a>
</div>

@if(session('success'))
<div style="background: #d1fae5; color: #065f46; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0;">
    {{ session('success') }}
</div>
@endif

@if($boqs->isEmpty())
<div class="empty-state">
    <div class="empty-icon">📋</div>
    <h2 class="empty-title">لا توجد جداول كميات بعد</h2>
    <p class="empty-text">ابدأ بإنشاء جدول كميات جديد للمناقصات أو المشاريع</p>
    <a href="{{ route('boq.create') }}" class="btn-primary">
        <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
        إنشاء جدول كميات
    </a>
</div>
@else
<div class="boq-grid">
    @foreach($boqs as $boq)
    <div class="boq-card">
        <div class="boq-header">
            <span class="boq-number">{{ $boq->boq_number }}</span>
            <span class="boq-status status-{{ $boq->status }}">
                @if($boq->status === 'draft') مسودة
                @elseif($boq->status === 'submitted') مقدم
                @elseif($boq->status === 'approved') معتمد
                @else تم المراجعة
                @endif
            </span>
        </div>
        
        <h3 class="boq-name">{{ $boq->name }}</h3>
        <p class="boq-type">
            @if($boq->type === 'tender') مناقصة
            @elseif($boq->type === 'contract') عقد
            @else تعديل
            @endif
        </p>
        
        <div class="boq-meta">
            <div class="meta-item">
                <span class="meta-label">عدد البنود</span>
                <span class="meta-value">{{ $boq->items->count() }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">الإجمالي</span>
                <span class="meta-value">{{ number_format($boq->final_amount, 2) }} {{ $boq->currency }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">تاريخ الإنشاء</span>
                <span class="meta-value">{{ $boq->created_at->format('Y-m-d') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">الإصدار</span>
                <span class="meta-value">v{{ $boq->version }}</span>
            </div>
        </div>
        
        <div class="boq-actions">
            <a href="{{ route('boq.show', $boq) }}" class="btn-sm btn-view">
                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                عرض
            </a>
            <a href="{{ route('boq.edit', $boq) }}" class="btn-sm btn-edit">
                <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                تعديل
            </a>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top: 30px;">
    {{ $boqs->links() }}
</div>
@endif

<script>
    lucide.createIcons();
</script>
@endsection
