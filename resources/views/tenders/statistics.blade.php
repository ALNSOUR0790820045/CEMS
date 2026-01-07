@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 12px; text-align: center; }
    .stat-value { font-size: 2.5rem; font-weight: 700; color: #0071e3; margin: 10px 0; }
    .stat-label { font-size: 0.9rem; color: #666; font-weight: 600; }
    .chart-card { background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px; }
    .chart-card h3 { font-size: 1.2rem; font-weight: 700; margin: 0 0 20px 0; }
    .chart-bar { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
    .chart-label { min-width: 150px; font-weight: 600; font-size: 0.9rem; }
    .chart-progress { flex: 1; height: 30px; background: #f0f0f0; border-radius: 6px; overflow: hidden; position: relative; }
    .chart-fill { height: 100%; background: linear-gradient(90deg, #0071e3, #00c4cc); display: flex; align-items: center; justify-content: flex-end; padding-left: 10px; color: white; font-weight: 600; font-size: 0.85rem; }
</style>

<div class="container">
    <div class="header">
        <h1>إحصائيات المناقصات</h1>
        <a href="{{ route('tenders.index') }}" class="btn btn-secondary">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">إجمالي المناقصات</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">المناقصات النشطة</div>
            <div class="stat-value" style="color: #ff9500;">{{ $stats['active'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">المناقصات الفائزة</div>
            <div class="stat-value" style="color: #34c759;">{{ $stats['won'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">المناقصات الخاسرة</div>
            <div class="stat-value" style="color: #ff3b30;">{{ $stats['lost'] }}</div>
        </div>
        <div class="stat-card" style="grid-column: span 2;">
            <div class="stat-label">إجمالي قيمة المناقصات الفائزة</div>
            <div class="stat-value" style="font-size: 2rem;">{{ number_format($stats['total_value_won'], 0) }} <span style="font-size: 1.2rem; color: #666;">ريال</span></div>
        </div>
        <div class="stat-card" style="grid-column: span 2;">
            <div class="stat-label">معدل الفوز</div>
            <div class="stat-value" style="font-size: 2rem;">
                {{ $stats['won'] + $stats['lost'] > 0 ? number_format(($stats['won'] / ($stats['won'] + $stats['lost'])) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <div class="chart-card">
        <h3>المناقصات حسب الحالة</h3>
        @php
            $maxCount = $tendersByStatus->max('count') ?? 1;
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
        @foreach($tendersByStatus as $item)
        <div class="chart-bar">
            <div class="chart-label">{{ $statusLabels[$item->status] ?? $item->status }}</div>
            <div class="chart-progress">
                <div class="chart-fill" style="width: {{ ($item->count / $maxCount) * 100 }}%;">
                    {{ $item->count }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="chart-card">
        <h3>المناقصات حسب الفئة</h3>
        @php
            $maxCount = $tendersByCategory->max('count') ?? 1;
            $categoryLabels = [
                'building' => 'مباني',
                'infrastructure' => 'بنية تحتية',
                'industrial' => 'صناعي',
                'maintenance' => 'صيانة',
                'supply' => 'توريدات',
                'other' => 'أخرى',
            ];
        @endphp
        @foreach($tendersByCategory as $item)
        <div class="chart-bar">
            <div class="chart-label">{{ $categoryLabels[$item->category] ?? $item->category }}</div>
            <div class="chart-progress">
                <div class="chart-fill" style="width: {{ ($item->count / $maxCount) * 100 }}%;">
                    {{ $item->count }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
