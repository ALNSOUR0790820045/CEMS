@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
        color: #1d1d1f;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1d1d1f;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th {
        background: #f5f5f7;
        padding: 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        border-bottom: 2px solid #e5e5e7;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid #e5e5e7;
        font-size: 0.9rem;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-critical {
        background: #fee;
        color: #c00;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-safe {
        background: #d4edda;
        color: #155724;
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-announced {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-preparing {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-submitted {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .status-awarded {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-lost {
        background: #ffebee;
        color: #d32f2f;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding: 20px 0;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        color: #1d1d1f;
        background: #f5f5f7;
        font-size: 0.85rem;
    }

    .pagination a:hover {
        background: #0071e3;
        color: white;
    }

    .pagination .active {
        background: #0071e3;
        color: white;
    }
</style>

<div class="page-header">
    <h1 class="page-title">العطاءات</h1>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('tenders.dashboard') }}" class="btn" style="background: #f5f5f7; color: #1d1d1f;">
            <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
            لوحة التحكم
        </a>
        <a href="{{ route('tenders.create') }}" class="btn btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة عطاء جديد
        </a>
    </div>
</div>

<div class="card">
    <!-- Filters -->
    <form method="GET" action="{{ route('tenders.index') }}">
        <div class="filters">
            <div class="form-group">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    <option value="">الكل</option>
                    <option value="announced" {{ request('status') == 'announced' ? 'selected' : '' }}>معلن</option>
                    <option value="evaluating" {{ request('status') == 'evaluating' ? 'selected' : '' }}>قيد التقييم</option>
                    <option value="decision_pending" {{ request('status') == 'decision_pending' ? 'selected' : '' }}>قيد اتخاذ القرار</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>قيد التحضير</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>تم التقديم</option>
                    <option value="awarded" {{ request('status') == 'awarded' ? 'selected' : '' }}>تمت الترسية</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>خسرنا</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ألغي</option>
                    <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>لم نتقدم</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">نوع العطاء</label>
                <select name="tender_type" class="form-control">
                    <option value="">الكل</option>
                    <option value="construction" {{ request('tender_type') == 'construction' ? 'selected' : '' }}>إنشاءات</option>
                    <option value="infrastructure" {{ request('tender_type') == 'infrastructure' ? 'selected' : '' }}>بنية تحتية</option>
                    <option value="buildings" {{ request('tender_type') == 'buildings' ? 'selected' : '' }}>مباني</option>
                    <option value="roads" {{ request('tender_type') == 'roads' ? 'selected' : '' }}>طرق</option>
                    <option value="bridges" {{ request('tender_type') == 'bridges' ? 'selected' : '' }}>جسور</option>
                    <option value="water" {{ request('tender_type') == 'water' ? 'selected' : '' }}>مياه وصرف صحي</option>
                    <option value="electrical" {{ request('tender_type') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                    <option value="mechanical" {{ request('tender_type') == 'mechanical' ? 'selected' : '' }}>ميكانيكا</option>
                    <option value="maintenance" {{ request('tender_type') == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                    <option value="consultancy" {{ request('tender_type') == 'consultancy' ? 'selected' : '' }}>استشارات</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 15px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="filter" style="width: 18px; height: 18px;"></i>
                تطبيق الفلاتر
            </button>
            <a href="{{ route('tenders.index') }}" class="btn" style="background: #f5f5f7; color: #1d1d1f;">
                إلغاء الفلاتر
            </a>
        </div>
    </form>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th>الرقم</th>
                <th>الاسم</th>
                <th>الجهة المالكة</th>
                <th>النوع</th>
                <th>القيمة التقديرية</th>
                <th>موعد التقديم</th>
                <th>الحالة</th>
                <th>المسؤول</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenders as $tender)
            <tr>
                <td><strong>{{ $tender->tender_number }}</strong></td>
                <td>{{ $tender->tender_name }}</td>
                <td>{{ $tender->owner_name }}</td>
                <td>
                    @switch($tender->tender_type)
                        @case('construction') إنشاءات @break
                        @case('infrastructure') بنية تحتية @break
                        @case('buildings') مباني @break
                        @case('roads') طرق @break
                        @case('bridges') جسور @break
                        @case('water') مياه وصرف @break
                        @case('electrical') كهرباء @break
                        @case('mechanical') ميكانيكا @break
                        @case('maintenance') صيانة @break
                        @case('consultancy') استشارات @break
                        @default {{ $tender->tender_type }}
                    @endswitch
                </td>
                <td>
                    {{ number_format($tender->estimated_value ?? 0, 0) }}
                    {{ $tender->currency->code ?? '' }}
                </td>
                <td>
                    {{ $tender->submission_deadline->format('Y-m-d') }}
                    @php
                        $days = $tender->getDaysUntilSubmission();
                        $urgency = $tender->getDeadlineUrgency();
                    @endphp
                    @if($days >= 0)
                        <br><span class="badge badge-{{ $urgency }}">
                            ⏰ {{ $days }} {{ $days == 1 ? 'يوم' : 'أيام' }}
                        </span>
                    @else
                        <br><span class="badge badge-critical">منتهي</span>
                    @endif
                </td>
                <td>
                    <span class="badge-status status-{{ $tender->status }}">
                        @switch($tender->status)
                            @case('announced') معلن @break
                            @case('evaluating') قيد التقييم @break
                            @case('decision_pending') قيد اتخاذ القرار @break
                            @case('preparing') قيد التحضير @break
                            @case('submitted') تم التقديم @break
                            @case('awarded') تمت الترسية @break
                            @case('lost') خسرنا @break
                            @case('cancelled') ألغي @break
                            @case('passed') لم نتقدم @break
                            @default {{ $tender->status }}
                        @endswitch
                    </span>
                </td>
                <td>{{ $tender->assignedUser->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('tenders.show', $tender) }}" style="color: #0071e3; text-decoration: none; margin-left: 10px;">
                        عرض
                    </a>
                    <a href="{{ route('tenders.edit', $tender) }}" style="color: #f57c00; text-decoration: none;">
                        تعديل
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 60px 20px; color: #86868b;">
                    <i data-lucide="inbox" style="width: 64px; height: 64px; margin-bottom: 20px; color: #d2d2d7;"></i>
                    <p>لا توجد عطاءات</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        {{ $tenders->links() }}
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
