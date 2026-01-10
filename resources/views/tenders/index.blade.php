@extends('layouts.app')

@section('content')
<style>
    .tenders-list {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-1px);
    }

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .tenders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tenders-table thead {
        background: #f5f5f7;
    }

    .tenders-table th {
        padding: 15px;
        text-align: right;
        font-weight: 700;
        color: #1d1d1f;
        font-size: 0.9rem;
        border-bottom: 2px solid #d2d2d7;
    }

    .tenders-table td {
        padding: 15px;
        text-align: right;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }

    .tenders-table tbody tr:hover {
        background: #f9f9f9;
    }

    .tender-code {
        font-family: 'SF Mono', 'Courier New', monospace;
        font-weight: 600;
        color: #0071e3;
    }

    .action-link {
        color: #0071e3;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 10px;
    }

    .action-link:hover {
        text-decoration: underline;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-draft { background: #f5f5f7; color: #1d1d1f; }
    .badge-submitted { background: #ff9500; color: white; }
    .badge-won { background: #34c759; color: white; }
    .badge-lost { background: #ff3b30; color: white; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6e6e73;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }
</style>

<div class="tenders-list">
    <div class="page-header">
        <h1 class="page-title">إدارة العطاءات</h1>
        <a href="{{ route('tenders.create') }}" class="btn btn-primary">➕ عطاء جديد</a>
    </div>

    <div class="table-container">
        @if($tenders->count() > 0)
            <table class="tenders-table">
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>العنوان</th>
                        <th>الشركة</th>
                        <th>القيمة المقدرة</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenders as $tender)
                        <tr>
                            <td><span class="tender-code">{{ $tender->code }}</span></td>
                            <td>{{ $tender->title }}</td>
                            <td>{{ $tender->company->name }}</td>
                            <td>{{ $tender->estimated_value ? number_format($tender->estimated_value, 2) . ' د.أ' : '-' }}</td>
                            <td>
                                @if($tender->status == 'draft')
                                    <span class="badge badge-draft">مسودة</span>
                                @elseif($tender->status == 'submitted')
                                    <span class="badge badge-submitted">مقدم</span>
                                @elseif($tender->status == 'won')
                                    <span class="badge badge-won">فائز</span>
                                @else
                                    <span class="badge badge-lost">خاسر</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('tender-risks.dashboard', $tender->id) }}" class="action-link">📊 المخاطر</a>
                                <a href="{{ route('tenders.edit', $tender->id) }}" class="action-link">تعديل</a>
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
                <div class="empty-state-icon">📋</div>
                <div style="font-size: 1.2rem; margin-bottom: 10px;">لا توجد عطاءات</div>
                <p>ابدأ بإضافة عطاء جديد</p>
            </div>
        @endif
    </div>
</div>
@endsection
