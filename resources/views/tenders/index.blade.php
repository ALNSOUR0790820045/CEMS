@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; }
    .btn-primary { background: #0071e3; color: white; }
    .btn-primary:hover { background: #0077ed; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3); }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .btn-secondary:hover { background: #e8e8ed; }
    .filters { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; }
    .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .filter-group label { font-size: 0.85rem; font-weight: 600; color: #666; }
    .filter-group select, .filter-group input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif; }
    .table-container { background: white; border-radius: 12px; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f5f5f7; }
    th { padding: 15px; text-align: right; font-weight: 600; font-size: 0.9rem; color: #666; border-bottom: 1px solid #ddd; }
    td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
    .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-identified { background: #e3f2fd; color: #1976d2; }
    .status-studying { background: #fff3e0; color: #f57c00; }
    .status-go { background: #e8f5e9; color: #388e3c; }
    .status-no_go { background: #ffebee; color: #d32f2f; }
    .status-pricing { background: #f3e5f5; color: #7b1fa2; }
    .status-submitted { background: #e1f5fe; color: #0288d1; }
    .status-won { background: #c8e6c9; color: #2e7d32; }
    .status-lost { background: #ffcdd2; color: #c62828; }
    .priority-critical { color: #d32f2f; font-weight: 700; }
    .priority-high { color: #f57c00; font-weight: 600; }
    .priority-medium { color: #1976d2; }
    .priority-low { color: #757575; }
    .actions { display: flex; gap: 10px; }
    .action-btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s; }
    .action-view { background: #e3f2fd; color: #1976d2; }
    .action-view:hover { background: #bbdefb; }
    .action-edit { background: #fff3e0; color: #f57c00; }
    .action-edit:hover { background: #ffe0b2; }
    .action-delete { background: #ffebee; color: #d32f2f; }
    .action-delete:hover { background: #ffcdd2; }
    .view-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
    .view-tab { padding: 10px 20px; border-radius: 8px; text-decoration: none; color: #666; font-weight: 600; transition: all 0.2s; }
    .view-tab.active { background: #0071e3; color: white; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
</style>

<div class="container">
    <div class="header">
        <h1>إدارة المناقصات</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tenders.pipeline') }}" class="btn btn-secondary">
                <i data-lucide="kanban-square" style="width: 18px; height: 18px;"></i>
                عرض Pipeline
            </a>
            <a href="{{ route('tenders.statistics') }}" class="btn btn-secondary">
                <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                الإحصائيات
            </a>
            <a href="{{ route('tenders.calendar') }}" class="btn btn-secondary">
                <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                التقويم
            </a>
            <a href="{{ route('tenders.create') }}" class="btn btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إضافة مناقصة
            </a>
        </div>
    </div>

    <form method="GET" class="filters">
        <div class="filter-group">
            <label>البحث</label>
            <input type="text" name="search" placeholder="اسم المناقصة أو الرقم" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <label>الحالة</label>
            <select name="status">
                <option value="">جميع الحالات</option>
                <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>تم اكتشافها</option>
                <option value="studying" {{ request('status') == 'studying' ? 'selected' : '' }}>قيد الدراسة</option>
                <option value="go" {{ request('status') == 'go' ? 'selected' : '' }}>قرار المشاركة</option>
                <option value="no_go" {{ request('status') == 'no_go' ? 'selected' : '' }}>قرار عدم المشاركة</option>
                <option value="pricing" {{ request('status') == 'pricing' ? 'selected' : '' }}>قيد التسعير</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>تم التقديم</option>
                <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>فوز</option>
                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>خسارة</option>
            </select>
        </div>
        <div class="filter-group">
            <label>الأولوية</label>
            <select name="priority">
                <option value="">جميع الأولويات</option>
                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
            </select>
        </div>
        <div class="filter-group" style="justify-content: flex-end;">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">بحث</button>
        </div>
    </form>

    <div class="table-container">
        @if($tenders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>رقم المناقصة</th>
                    <th>اسم المناقصة</th>
                    <th>الجهة</th>
                    <th>الحالة</th>
                    <th>الأولوية</th>
                    <th>آخر موعد للتقديم</th>
                    <th>القيمة المقدرة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenders as $tender)
                <tr>
                    <td><strong>{{ $tender->tender_number }}</strong></td>
                    <td>{{ $tender->name }}</td>
                    <td>{{ $tender->client?->name ?? $tender->client_name ?? '-' }}</td>
                    <td>
                        @php
                            $statusLabels = [
                                'identified' => 'تم اكتشافها',
                                'studying' => 'قيد الدراسة',
                                'go' => 'قرار المشاركة',
                                'no_go' => 'قرار عدم المشاركة',
                                'documents_purchased' => 'تم شراء الكراسة',
                                'pricing' => 'قيد التسعير',
                                'submitted' => 'تم التقديم',
                                'opened' => 'تم فتح المظاريف',
                                'negotiating' => 'قيد التفاوض',
                                'won' => 'فوز',
                                'lost' => 'خسارة',
                                'cancelled' => 'ملغاة',
                                'converted' => 'تم التحويل',
                            ];
                        @endphp
                        <span class="status-badge status-{{ $tender->status }}">
                            {{ $statusLabels[$tender->status] ?? $tender->status }}
                        </span>
                    </td>
                    <td>
                        @php
                            $priorityLabels = [
                                'critical' => 'حرجة',
                                'high' => 'عالية',
                                'medium' => 'متوسطة',
                                'low' => 'منخفضة',
                            ];
                        @endphp
                        <span class="priority-{{ $tender->priority }}">
                            {{ $priorityLabels[$tender->priority] ?? $tender->priority }}
                        </span>
                    </td>
                    <td>{{ $tender->submission_deadline ? $tender->submission_deadline->format('Y-m-d') : '-' }}</td>
                    <td>{{ $tender->estimated_value ? number_format($tender->estimated_value, 0) . ' ' . $tender->currency : '-' }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('tenders.show', $tender) }}" class="action-btn action-view">عرض</a>
                            <a href="{{ route('tenders.edit', $tender) }}" class="action-btn action-edit">تعديل</a>
                            <form method="POST" action="{{ route('tenders.destroy', $tender) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المناقصة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-delete" style="border: none; cursor: pointer; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 20px;">
            {{ $tenders->links() }}
        </div>
        @else
        <div class="empty-state">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
            <p>لا توجد مناقصات</p>
            <a href="{{ route('tenders.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إضافة مناقصة جديدة
            </a>
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
