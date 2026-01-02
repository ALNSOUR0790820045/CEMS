@extends('layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }

    .order-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f5f5f7;
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.85rem;
        color: #86868b;
        font-weight: 600;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text);
        font-weight: 500;
    }

    .badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-draft {
        background: #f0f0f0;
        color: #666;
    }

    .badge-sent {
        background: #e3f2fd;
        color: #1976d2;
    }

    .badge-confirmed {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-received {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-cancelled {
        background: #ffebee;
        color: #d32f2f;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    thead {
        background: #f5f5f7;
    }

    th {
        padding: 12px 15px;
        text-align: right;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1d1d1f;
        border-bottom: 2px solid #d2d2d7;
    }

    td {
        padding: 12px 15px;
        border-bottom: 1px solid #f5f5f7;
        font-size: 0.9rem;
    }

    .totals-section {
        background: #f5f5f7;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .totals-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 0.95rem;
    }

    .totals-row.total {
        border-top: 2px solid #d2d2d7;
        margin-top: 10px;
        padding-top: 15px;
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--accent);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #0077ED;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
    }

    .btn-secondary:hover {
        background: #e8e8ed;
    }

    .btn-success {
        background: #34c759;
        color: white;
    }

    .btn-success:hover {
        background: #30b350;
    }

    .btn-warning {
        background: #ff9500;
        color: white;
    }

    .btn-warning:hover {
        background: #e68600;
    }

    .btn-danger {
        background: #ff3b30;
        color: white;
    }

    .btn-danger:hover {
        background: #ff2d20;
    }

    .notes-section {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .notes-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #86868b;
        font-size: 0.85rem;
    }

    .notes-text {
        color: var(--text);
        line-height: 1.6;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-success {
        background: #e8f5e9;
        color: #388e3c;
        border: 1px solid #c8e6c9;
    }

    .alert-error {
        background: #ffebee;
        color: #d32f2f;
        border: 1px solid #ffcdd2;
    }

    @media print {
        .page-header,
        .action-buttons,
        .btn {
            display: none !important;
        }
        
        .order-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">تفاصيل أمر الشراء</h1>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
        <i data-lucide="arrow-right"></i>
        العودة للقائمة
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="order-card">
    <div class="order-header">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 10px;">{{ $purchaseOrder->order_number }}</h2>
            <p style="color: #86868b; font-size: 0.9rem;">تاريخ الإنشاء: {{ $purchaseOrder->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div>
            @php
                $statusMap = [
                    'draft' => ['class' => 'badge-draft', 'text' => 'مسودة'],
                    'sent' => ['class' => 'badge-sent', 'text' => 'مرسل'],
                    'confirmed' => ['class' => 'badge-confirmed', 'text' => 'مؤكد'],
                    'received' => ['class' => 'badge-received', 'text' => 'مستلم'],
                    'cancelled' => ['class' => 'badge-cancelled', 'text' => 'ملغي'],
                ];
            @endphp
            <span class="badge {{ $statusMap[$purchaseOrder->status]['class'] }}">
                {{ $statusMap[$purchaseOrder->status]['text'] }}
            </span>
        </div>
    </div>

    <div class="order-info-grid">
        <div class="info-item">
            <span class="info-label">المورد</span>
            <span class="info-value">{{ $purchaseOrder->supplier->name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">المستودع</span>
            <span class="info-value">{{ $purchaseOrder->warehouse->name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">تاريخ الأمر</span>
            <span class="info-value">{{ $purchaseOrder->order_date->format('Y-m-d') }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">التاريخ المتوقع</span>
            <span class="info-value">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">شروط الدفع</span>
            <span class="info-value">{{ $purchaseOrder->paymentTerm->name }} ({{ $purchaseOrder->paymentTerm->days }} يوم)</span>
        </div>

        <div class="info-item">
            <span class="info-label">أنشئ بواسطة</span>
            <span class="info-value">{{ $purchaseOrder->creator->name }}</span>
        </div>
    </div>

    <h3 style="font-size: 1.2rem; font-weight: 600; margin: 30px 0 20px;">المنتجات</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الضريبة</th>
                <th>الخصم</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong><br>
                        <small style="color: #86868b;">{{ $item->product->sku }}</small>
                    </td>
                    <td>{{ number_format($item->quantity, 3) }} {{ $item->product->unit }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} ريال</td>
                    <td>{{ number_format($item->tax_amount, 2) }} ريال ({{ $item->tax_rate }}%)</td>
                    <td>{{ number_format($item->discount, 2) }} ريال</td>
                    <td><strong>{{ number_format($item->total, 2) }} ريال</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals-row">
            <span>المجموع الفرعي:</span>
            <strong>{{ number_format($purchaseOrder->subtotal, 2) }} ريال</strong>
        </div>
        <div class="totals-row">
            <span>الضريبة:</span>
            <strong>{{ number_format($purchaseOrder->tax_amount, 2) }} ريال</strong>
        </div>
        <div class="totals-row">
            <span>الخصم:</span>
            <strong>{{ number_format($purchaseOrder->discount, 2) }} ريال</strong>
        </div>
        <div class="totals-row total">
            <span>الإجمالي النهائي:</span>
            <strong>{{ number_format($purchaseOrder->total, 2) }} ريال</strong>
        </div>
    </div>

    @if($purchaseOrder->notes)
        <div class="notes-section">
            <div class="notes-label">ملاحظات:</div>
            <div class="notes-text">{{ $purchaseOrder->notes }}</div>
        </div>
    @endif
</div>

<div class="action-buttons">
    <button onclick="window.print()" class="btn btn-primary">
        <i data-lucide="printer"></i>
        طباعة
    </button>

    @if(!in_array($purchaseOrder->status, ['confirmed', 'received', 'cancelled']))
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">
            <i data-lucide="edit"></i>
            تعديل
        </a>
    @endif

    @if($purchaseOrder->status === 'draft')
        <form method="POST" action="{{ route('purchase-orders.status', $purchaseOrder) }}" style="display: inline;">
            @csrf
            <input type="hidden" name="status" value="sent">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="send"></i>
                إرسال للمورد
            </button>
        </form>
    @endif

    @if($purchaseOrder->status === 'sent')
        <form method="POST" action="{{ route('purchase-orders.status', $purchaseOrder) }}" style="display: inline;">
            @csrf
            <input type="hidden" name="status" value="confirmed">
            <button type="submit" class="btn btn-success">
                <i data-lucide="check-circle"></i>
                تأكيد الأمر
            </button>
        </form>
    @endif

    @if($purchaseOrder->status === 'confirmed')
        <form method="POST" action="{{ route('purchase-orders.status', $purchaseOrder) }}" style="display: inline;">
            @csrf
            <input type="hidden" name="status" value="received">
            <button type="submit" class="btn btn-success">
                <i data-lucide="package-check"></i>
                تأكيد الاستلام
            </button>
        </form>
    @endif

    @if(!in_array($purchaseOrder->status, ['received', 'cancelled']))
        <form method="POST" action="{{ route('purchase-orders.status', $purchaseOrder) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الأمر؟')">
            @csrf
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="btn btn-danger">
                <i data-lucide="x-circle"></i>
                إلغاء الأمر
            </button>
        </form>
    @endif

    @if($purchaseOrder->status !== 'received')
        <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الأمر؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i data-lucide="trash-2"></i>
                حذف
            </button>
        </form>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
