@extends('layouts.app')

@section('content')
<style>
    .risks-list {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .tender-info {
        background: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .tender-code {
        font-weight: 600;
        color: #0071e3;
        font-size: 1.1rem;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .btn-secondary:hover {
        background: #e8e8ed;
    }

    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6e6e73;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .risks-table {
        width: 100%;
        border-collapse: collapse;
    }

    .risks-table thead {
        background: #f5f5f7;
    }

    .risks-table th {
        padding: 15px;
        text-align: right;
        font-weight: 700;
        color: #1d1d1f;
        font-size: 0.9rem;
        border-bottom: 2px solid #d2d2d7;
    }

    .risks-table td {
        padding: 15px;
        text-align: right;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }

    .risks-table tbody tr:hover {
        background: #f9f9f9;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-critical {
        background: #000;
        color: white;
    }

    .badge-high {
        background: #ff3b30;
        color: white;
    }

    .badge-medium {
        background: #ff9500;
        color: white;
    }

    .badge-low {
        background: #34c759;
        color: white;
    }

    .badge-category {
        background: #e8e8ed;
        color: #1d1d1f;
    }

    .risk-code {
        font-family: 'SF Mono', 'Courier New', monospace;
        font-weight: 600;
        color: #0071e3;
    }

    .action-links {
        display: flex;
        gap: 10px;
    }

    .action-link {
        color: #0071e3;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .action-link:hover {
        text-decoration: underline;
    }

    .action-link.delete {
        color: #ff3b30;
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px;
        gap: 10px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .pagination a {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .pagination a:hover {
        background: #e8e8ed;
    }

    .pagination .active {
        background: #0071e3;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6e6e73;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .empty-state-text {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .table-container {
            overflow-x: auto;
        }

        .risks-table {
            min-width: 800px;
        }
    }
</style>

<div class="risks-list">
    <div class="page-header">
        <h1 class="page-title">قائمة المخاطر</h1>
        <div class="action-buttons">
            <a href="{{ route('tender-risks.dashboard', $tender->id) }}" class="btn btn-secondary">📊 لوحة المخاطر</a>
            <a href="{{ route('tender-risks.create', $tender->id) }}" class="btn btn-primary">➕ مخاطرة جديدة</a>
        </div>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('tender-risks.index', $tender->id) }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">المستوى</label>
                    <select name="risk_level" class="filter-select" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="critical" {{ request('risk_level') == 'critical' ? 'selected' : '' }}>⚫ حرج</option>
                        <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>🔴 عالي</option>
                        <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>🟡 متوسط</option>
                        <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>🟢 منخفض</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">الفئة</label>
                    <select name="risk_category" class="filter-select" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="technical" {{ request('risk_category') == 'technical' ? 'selected' : '' }}>فنية</option>
                        <option value="financial" {{ request('risk_category') == 'financial' ? 'selected' : '' }}>مالية</option>
                        <option value="contractual" {{ request('risk_category') == 'contractual' ? 'selected' : '' }}>تعاقدية</option>
                        <option value="schedule" {{ request('risk_category') == 'schedule' ? 'selected' : '' }}>جدولة</option>
                        <option value="resources" {{ request('risk_category') == 'resources' ? 'selected' : '' }}>موارد</option>
                        <option value="external" {{ request('risk_category') == 'external' ? 'selected' : '' }}>خارجية</option>
                        <option value="safety" {{ request('risk_category') == 'safety' ? 'selected' : '' }}>سلامة</option>
                        <option value="quality" {{ request('risk_category') == 'quality' ? 'selected' : '' }}>جودة</option>
                        <option value="political" {{ request('risk_category') == 'political' ? 'selected' : '' }}>سياسية</option>
                        <option value="environmental" {{ request('risk_category') == 'environmental' ? 'selected' : '' }}>بيئية</option>
                        <option value="other" {{ request('risk_category') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">الحالة</label>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>محددة</option>
                        <option value="assessed" {{ request('status') == 'assessed' ? 'selected' : '' }}>مقيّمة</option>
                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>مخطط لها</option>
                        <option value="monitored" {{ request('status') == 'monitored' ? 'selected' : '' }}>مراقبة</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Risks Table -->
    <div class="table-container">
        @if($risks->count() > 0)
            <table class="risks-table">
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>العنوان</th>
                        <th>الفئة</th>
                        <th>الاحتمالية</th>
                        <th>التأثير</th>
                        <th>النتيجة</th>
                        <th>المستوى</th>
                        <th>الاستراتيجية</th>
                        <th>المالك</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($risks as $risk)
                        <tr>
                            <td><span class="risk-code">{{ $risk->risk_code }}</span></td>
                            <td>{{ $risk->risk_title }}</td>
                            <td><span class="badge badge-category">{{ $risk->category_name }}</span></td>
                            <td>{{ $risk->probability_score }}</td>
                            <td>{{ $risk->impact_score }}</td>
                            <td><strong>{{ $risk->risk_score }}</strong></td>
                            <td>
                                @if($risk->risk_level == 'critical')
                                    <span class="badge badge-critical">⚫ حرج</span>
                                @elseif($risk->risk_level == 'high')
                                    <span class="badge badge-high">🔴 عالي</span>
                                @elseif($risk->risk_level == 'medium')
                                    <span class="badge badge-medium">🟡 متوسط</span>
                                @else
                                    <span class="badge badge-low">🟢 منخفض</span>
                                @endif
                            </td>
                            <td>{{ $risk->response_strategy_name }}</td>
                            <td>{{ $risk->owner ? $risk->owner->name : '-' }}</td>
                            <td>
                                <div class="action-links">
                                    <a href="{{ route('tender-risks.edit', [$tender->id, $risk->id]) }}" class="action-link">تعديل</a>
                                    <form method="POST" action="{{ route('tender-risks.destroy', [$tender->id, $risk->id]) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المخاطرة?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link delete" style="border: none; background: none; cursor: pointer;">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $risks->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">لا توجد مخاطر مسجلة</div>
                <p>ابدأ بإضافة مخاطرة جديدة للعطاء</p>
            </div>
        @endif
    </div>
</div>
@endsection
