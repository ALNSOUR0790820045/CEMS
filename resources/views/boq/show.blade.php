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
        align-items: start;
    }
    
    .header-info h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0 0 5px 0;
    }
    
    .boq-number {
        font-size: 0.95rem;
        color: #86868b;
        font-family: 'SF Mono', monospace;
    }
    
    .header-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
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
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .info-label {
        font-size: 0.75rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .info-subtitle {
        font-size: 0.85rem;
        color: #86868b;
        margin-top: 5px;
    }
    
    .sections-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .section-block {
        margin-bottom: 30px;
        border: 1px solid #f3f4f6;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .section-header {
        background: #f9fafb;
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .section-total {
        font-size: 1rem;
        font-weight: 700;
        color: #0071e3;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .items-table thead {
        background: #fafafa;
    }
    
    .items-table th {
        padding: 12px 15px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .items-table td {
        padding: 12px 15px;
        font-size: 0.9rem;
        color: #1d1d1f;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .items-table tbody tr:hover {
        background: #f9fafb;
    }
    
    .status-badge {
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
</style>

<div class="page-header">
    <div class="header-info">
        <h1>{{ $boq->name }}</h1>
        <span class="boq-number">{{ $boq->boq_number }}</span>
    </div>
    <div class="header-actions">
        <a href="{{ route('boq.edit', $boq) }}" class="btn btn-primary">
            <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
            تعديل
        </a>
        @if($boq->status !== 'approved')
        <form method="POST" action="{{ route('boq.approve', $boq) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                اعتماد
            </button>
        </form>
        @endif
    </div>
</div>

<div class="info-grid">
    <div class="info-card">
        <div class="info-label">الحالة</div>
        <div class="info-value">
            <span class="status-badge status-{{ $boq->status }}">
                @if($boq->status === 'draft') مسودة
                @elseif($boq->status === 'submitted') مقدم
                @elseif($boq->status === 'approved') معتمد
                @else تم المراجعة
                @endif
            </span>
        </div>
    </div>
    
    <div class="info-card">
        <div class="info-label">الإجمالي الكلي</div>
        <div class="info-value">{{ number_format($boq->final_amount, 2) }}</div>
        <div class="info-subtitle">{{ $boq->currency }}</div>
    </div>
    
    <div class="info-card">
        <div class="info-label">عدد البنود</div>
        <div class="info-value">{{ $boq->items->count() }}</div>
    </div>
    
    <div class="info-card">
        <div class="info-label">الإصدار</div>
        <div class="info-value">v{{ $boq->version }}</div>
    </div>
</div>

<div class="sections-container">
    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">بنود جدول الكميات</h2>
    
    @forelse($boq->sections as $section)
    <div class="section-block">
        <div class="section-header">
            <div class="section-title">{{ $section->code }} - {{ $section->name }}</div>
            <div class="section-total">{{ number_format($section->total_amount, 2) }} {{ $boq->currency }}</div>
        </div>
        
        @if($section->items->count() > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th>رقم البند</th>
                    <th>الوصف</th>
                    <th>الوحدة</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($section->items as $item)
                <tr>
                    <td>{{ $item->item_number }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_rate, 2) }}</td>
                    <td style="font-weight: 600;">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 30px; text-align: center; color: #86868b;">
            لا توجد بنود في هذا القسم
        </div>
        @endif
    </div>
    @empty
    <div style="text-align: center; padding: 40px; color: #86868b;">
        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
        <p>لا توجد أقسام أو بنود بعد</p>
    </div>
    @endforelse
</div>

<script>
    lucide.createIcons();
</script>
@endsection
