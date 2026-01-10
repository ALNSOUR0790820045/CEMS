@extends('layouts.app')

@section('content')
<style>
    .content-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #f8f9fa;
    }

    .quotation-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .quotation-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .company-info h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 5px;
    }

    .company-info p {
        color: #6c757d;
        margin: 3px 0;
    }

    .quotation-meta {
        text-align: left;
    }

    .quotation-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 10px;
    }

    .status-draft {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-sent {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-accepted {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-rejected {
        background: #ffebee;
        color: #d32f2f;
    }

    .status-expired {
        background: #f5f5f5;
        color: #616161;
    }

    .info-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    .info-block h4 {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .info-block p {
        color: var(--text);
        margin: 5px 0;
        font-size: 0.95rem;
    }

    .items-table {
        margin: 40px 0;
    }

    .items-table h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 12px;
        text-align: right;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e9ecef;
    }

    td {
        padding: 12px;
        text-align: right;
        color: var(--text);
        border-bottom: 1px solid #f0f0f0;
    }

    .totals-section {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end;
    }

    .totals-table {
        width: 400px;
    }

    .totals-table tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .totals-table tr:last-child {
        border-bottom: 2px solid var(--accent);
    }

    .totals-table td {
        padding: 12px;
    }

    .totals-table td:first-child {
        font-weight: 600;
        color: #6c757d;
    }

    .totals-table td:last-child {
        text-align: left;
        font-weight: 600;
    }

    .total-row td {
        font-size: 1.2rem;
        color: var(--accent);
    }

    .notes-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #f0f0f0;
    }

    .notes-section h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text);
    }

    .notes-section p {
        color: #6c757d;
        line-height: 1.6;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>

<div class="content-wrapper">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-header">
        <h1>عرض السعر</h1>
        <div class="action-buttons">
            <a href="{{ route('sales-quotations.pdf', $salesQuotation) }}" class="btn btn-primary">
                <i data-lucide="download" style="width: 18px; height: 18px;"></i>
                تحميل PDF
            </a>
            <a href="{{ route('sales-quotations.edit', $salesQuotation) }}" class="btn btn-secondary">
                <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                تعديل
            </a>
            <a href="{{ route('sales-quotations.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="quotation-card">
        <div class="quotation-header">
            <div class="company-info">
                <h2>CEMS ERP</h2>
                <p>نظام إدارة المشاريع المتكامل</p>
                <p>المملكة العربية السعودية</p>
            </div>
            <div class="quotation-meta">
                <div class="quotation-number">{{ $salesQuotation->quotation_number }}</div>
                <span class="status-badge status-{{ $salesQuotation->status }}">
                    @switch($salesQuotation->status)
                        @case('draft') مسودة @break
                        @case('sent') مرسل @break
                        @case('accepted') مقبول @break
                        @case('rejected') مرفوض @break
                        @case('expired') منتهي @break
                    @endswitch
                </span>
            </div>
        </div>

        <div class="info-section">
            <div class="info-block">
                <h4>معلومات العميل</h4>
                <p><strong>{{ $salesQuotation->customer->name }}</strong></p>
                @if($salesQuotation->customer->email)
                    <p>{{ $salesQuotation->customer->email }}</p>
                @endif
                @if($salesQuotation->customer->phone)
                    <p>{{ $salesQuotation->customer->phone }}</p>
                @endif
                @if($salesQuotation->customer->address)
                    <p>{{ $salesQuotation->customer->address }}</p>
                @endif
            </div>

            <div class="info-block">
                <h4>تفاصيل العرض</h4>
                <p><strong>تاريخ العرض:</strong> {{ $salesQuotation->quotation_date->format('Y-m-d') }}</p>
                <p><strong>صالح حتى:</strong> {{ $salesQuotation->valid_until->format('Y-m-d') }}</p>
                <p><strong>أنشئ بواسطة:</strong> {{ $salesQuotation->creator->name }}</p>
            </div>
        </div>

        <div class="items-table">
            <h3>المنتجات</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الخصم</th>
                        <th>الضريبة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesQuotation->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->quantity, 3) }}</td>
                            <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                            <td>{{ number_format($item->discount, 2) }} ر.س</td>
                            <td>{{ number_format($item->tax_amount, 2) }} ر.س ({{ $item->tax_rate }}%)</td>
                            <td><strong>{{ number_format($item->total, 2) }} ر.س</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>المجموع الفرعي</td>
                    <td>{{ number_format($salesQuotation->subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>الخصم</td>
                    <td>{{ number_format($salesQuotation->discount, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>الضريبة</td>
                    <td>{{ number_format($salesQuotation->tax_amount, 2) }} ر.س</td>
                </tr>
                <tr class="total-row">
                    <td>الإجمالي</td>
                    <td>{{ number_format($salesQuotation->total, 2) }} ر.س</td>
                </tr>
            </table>
        </div>

        @if($salesQuotation->terms_conditions)
            <div class="notes-section">
                <h4>الشروط والأحكام</h4>
                <p>{{ $salesQuotation->terms_conditions }}</p>
            </div>
        @endif

        @if($salesQuotation->notes)
            <div class="notes-section">
                <h4>ملاحظات</h4>
                <p>{{ $salesQuotation->notes }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
