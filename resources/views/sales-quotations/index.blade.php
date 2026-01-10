@extends('layouts.app')

@section('content')
<style>
    .content-wrapper {
        max-width: 1400px;
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

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 12px 24px;
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

    .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .table-card {
        background: white;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 16px;
        text-align: right;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }

    td {
        padding: 16px;
        text-align: right;
        color: var(--text);
        border-bottom: 1px solid #f0f0f0;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tbody tr {
        transition: background 0.2s;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-start;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid;
        background: white;
        cursor: pointer;
    }

    .btn-view {
        color: #0071e3;
        border-color: #0071e3;
    }

    .btn-view:hover {
        background: #0071e3;
        color: white;
    }

    .btn-edit {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-edit:hover {
        background: #28a745;
        color: white;
    }

    .btn-delete {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-delete:hover {
        background: #dc3545;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        width: 64px;
        height: 64px;
        color: #dee2e6;
        margin-bottom: 16px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
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
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="page-header">
        <h1>عروض الأسعار</h1>
        <a href="{{ route('sales-quotations.create') }}" class="btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة عرض سعر جديد
        </a>
    </div>

    <div class="table-card">
        @if($quotations->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>رقم العرض</th>
                        <th>العميل</th>
                        <th>تاريخ العرض</th>
                        <th>صالح حتى</th>
                        <th>الحالة</th>
                        <th>الإجمالي</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $quotation)
                        <tr>
                            <td><strong>{{ $quotation->quotation_number }}</strong></td>
                            <td>{{ $quotation->customer->name }}</td>
                            <td>{{ $quotation->quotation_date->format('Y-m-d') }}</td>
                            <td>{{ $quotation->valid_until->format('Y-m-d') }}</td>
                            <td>
                                <span class="status-badge status-{{ $quotation->status }}">
                                    @switch($quotation->status)
                                        @case('draft') مسودة @break
                                        @case('sent') مرسل @break
                                        @case('accepted') مقبول @break
                                        @case('rejected') مرفوض @break
                                        @case('expired') منتهي @break
                                    @endswitch
                                </span>
                            </td>
                            <td><strong>{{ number_format($quotation->total, 2) }} ر.س</strong></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('sales-quotations.show', $quotation) }}" class="btn-action btn-view">عرض</a>
                                    <a href="{{ route('sales-quotations.edit', $quotation) }}" class="btn-action btn-edit">تعديل</a>
                                    <form method="POST" action="{{ route('sales-quotations.destroy', $quotation) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف عرض السعر؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <i data-lucide="file-text"></i>
                <h3>لا توجد عروض أسعار</h3>
                <p>ابدأ بإنشاء عرض سعر جديد</p>
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
