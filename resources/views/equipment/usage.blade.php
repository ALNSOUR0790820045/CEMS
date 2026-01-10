@extends('layouts.app')

@section('content')
<style>
    .usage-container {
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

    .usage-table {
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }
</style>

<div class="usage-container">
    <div class="page-header">
        <h1 class="page-title">سجل استخدام: {{ $equipment->name }}</h1>
        <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">
            <i data-lucide="arrow-right"></i>
            رجوع
        </a>
    </div>

    @if($usageLogs->count() > 0)
        <div class="usage-table">
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>ساعات العمل</th>
                        <th>المشروع</th>
                        <th>المشغل</th>
                        <th>الحالة</th>
                        <th>الوقود المستهلك</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usageLogs as $log)
                        <tr>
                            <td>{{ $log->usage_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($log->hours_worked, 1) }} ساعة</td>
                            <td>{{ $log->project ? $log->project->name : '-' }}</td>
                            <td>{{ $log->operator ? $log->operator->name : '-' }}</td>
                            <td>
                                @if($log->condition == 'good') جيد
                                @elseif($log->condition == 'fair') مقبول
                                @else يحتاج انتباه
                                @endif
                            </td>
                            <td>{{ $log->fuel_consumed ? number_format($log->fuel_consumed, 1) . ' لتر' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $usageLogs->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>لا توجد سجلات استخدام لهذه المعدة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
