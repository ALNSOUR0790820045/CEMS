@extends('layouts.app')

@section('content')
<style>
    .maintenance-container {
        padding: 40px;
        max-width: 1200px;
        margin: 0 auto;
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
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .maintenance-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #f8f9fa;
        padding: 15px;
        text-align: right;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }

    tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-scheduled {
        background: #fff3cd;
        color: #856404;
    }

    .status-in_progress {
        background: #cce5ff;
        color: #004085;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }
</style>

<div class="maintenance-container">
    <div class="page-header">
        <h1 class="page-title">سجل الصيانة: {{ $equipment->name }}</h1>
        <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">
            <i data-lucide="arrow-right"></i>
            رجوع
        </a>
    </div>

    @if($maintenanceRecords->count() > 0)
        <div class="maintenance-table">
            <table>
                <thead>
                    <tr>
                        <th>رقم الصيانة</th>
                        <th>النوع</th>
                        <th>التاريخ المجدول</th>
                        <th>التاريخ الفعلي</th>
                        <th>التكلفة الكلية</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenanceRecords as $record)
                        <tr>
                            <td>{{ $record->maintenance_number }}</td>
                            <td>
                                @if($record->type == 'preventive') وقائية
                                @elseif($record->type == 'corrective') تصحيحية
                                @elseif($record->type == 'breakdown') عطل
                                @else فحص
                                @endif
                            </td>
                            <td>{{ $record->scheduled_date->format('Y-m-d') }}</td>
                            <td>{{ $record->actual_date ? $record->actual_date->format('Y-m-d') : '-' }}</td>
                            <td>{{ number_format($record->total_cost, 2) }} ريال</td>
                            <td>
                                <span class="status-badge status-{{ $record->status }}">
                                    @if($record->status == 'scheduled') مجدولة
                                    @elseif($record->status == 'in_progress') قيد التنفيذ
                                    @elseif($record->status == 'completed') مكتملة
                                    @else ملغاة
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $maintenanceRecords->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>لا توجد سجلات صيانة لهذه المعدة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
