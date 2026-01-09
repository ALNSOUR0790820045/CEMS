@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .alert-card { background: #fff3e0; border: 2px solid #ff9500; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
    .alert-card h3 { color: #f57c00; margin: 0 0 10px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
    .tender-card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; border-right: 4px solid #ff9500; }
    .tender-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
    .tender-title { font-weight: 700; font-size: 1.1rem; }
    .tender-number { color: #666; font-size: 0.9rem; }
    .deadline-badge { background: #ff3b30; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .deadline-warning { background: #ff9500; }
    .deadline-ok { background: #34c759; }
    .tender-meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px; }
    .meta-item { display: flex; flex-direction: column; gap: 3px; }
    .meta-label { font-size: 0.85rem; color: #666; font-weight: 600; }
    .meta-value { font-size: 0.95rem; }
    .actions { display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid #f0f0f0; }
    .action-btn { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; transition: all 0.2s; }
    .action-view { background: #e3f2fd; color: #1976d2; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
</style>

<div class="container">
    <div class="header">
        <h1>المناقصات القريبة من الموعد</h1>
        <a href="{{ route('tenders.index') }}" class="btn btn-secondary">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="alert-card">
        <h3>
            <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
            تنبيه: مناقصات تقترب مواعيدها
        </h3>
        <p style="margin: 0; color: #666;">
            يوجد <strong>{{ $tenders->count() }}</strong> مناقصة/مناقصات تنتهي خلال {{ $days }} أيام القادمة. يرجى المتابعة والتأكد من إتمام جميع الإجراءات المطلوبة.
        </p>
    </div>

    @if($tenders->count() > 0)
        @foreach($tenders as $tender)
        @php
            $daysLeft = $tender->submission_deadline->diffInDays(now());
            $badgeClass = 'deadline-badge ';
            if ($daysLeft <= 1) {
                $badgeClass .= 'deadline-critical';
            } elseif ($daysLeft <= 3) {
                $badgeClass .= 'deadline-warning';
            } else {
                $badgeClass .= 'deadline-ok';
            }
        @endphp
        <div class="tender-card">
            <div class="tender-header">
                <div>
                    <div class="tender-title">{{ $tender->name }}</div>
                    <div class="tender-number">{{ $tender->tender_number }}</div>
                </div>
                <span class="{{ $badgeClass }}">
                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                    @if($daysLeft == 0)
                        ينتهي اليوم
                    @elseif($daysLeft == 1)
                        ينتهي غداً
                    @else
                        {{ $daysLeft }} أيام متبقية
                    @endif
                </span>
            </div>

            <div class="tender-meta">
                <div class="meta-item">
                    <div class="meta-label">آخر موعد للتقديم</div>
                    <div class="meta-value">
                        <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                        {{ $tender->submission_deadline->format('Y-m-d') }}
                        @if($tender->submission_time)
                            - {{ $tender->submission_time }}
                        @endif
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">الجهة المالكة</div>
                    <div class="meta-value">
                        <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                        {{ $tender->client?->name ?? $tender->client_name ?? '-' }}
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">الحالة</div>
                    <div class="meta-value">
                        @php
                            $statusLabels = [
                                'identified' => 'تم اكتشافها',
                                'studying' => 'قيد الدراسة',
                                'go' => 'قرار المشاركة',
                                'documents_purchased' => 'تم شراء الكراسة',
                                'pricing' => 'قيد التسعير',
                            ];
                        @endphp
                        {{ $statusLabels[$tender->status] ?? $tender->status }}
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">المكلف بالمتابعة</div>
                    <div class="meta-value">
                        <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        {{ $tender->assignedTo?->name ?? '-' }}
                    </div>
                </div>
                @if($tender->estimated_value)
                <div class="meta-item">
                    <div class="meta-label">القيمة المقدرة</div>
                    <div class="meta-value">
                        <i data-lucide="dollar-sign" style="width: 16px; height: 16px;"></i>
                        {{ number_format($tender->estimated_value, 0) }} {{ $tender->currency }}
                    </div>
                </div>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('tenders.show', $tender) }}" class="action-btn action-view">
                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                    عرض التفاصيل
                </a>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <i data-lucide="check-circle" style="width: 48px; height: 48px; color: #34c759;"></i>
            <h3 style="color: #34c759; margin: 15px 0;">رائع!</h3>
            <p>لا توجد مناقصات قريبة من الموعد خلال ال {{ $days }} أيام القادمة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
