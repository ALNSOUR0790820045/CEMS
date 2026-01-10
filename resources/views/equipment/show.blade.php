@extends('layouts.app')

@section('content')
<style>
    .detail-container {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 30px;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .header-info h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .equipment-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #666;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-available {
        background: #d4edda;
        color: #155724;
    }

    .status-in_use {
        background: #fff3cd;
        color: #856404;
    }

    .status-maintenance {
        background: #f8d7da;
        color: #721c24;
    }

    .header-actions {
        display: flex;
        gap: 10px;
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
    }

    .btn-primary {
        background: #0071e3;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #0077ed;
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .info-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0071e3;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.85rem;
        color: #666;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .stat-card {
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .activity-list {
        list-style: none;
    }

    .activity-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-date {
        font-size: 0.8rem;
        color: #999;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }
</style>

<div class="detail-container">
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="detail-header">
        <div class="header-info">
            <h1>{{ $equipment->name }}</h1>
            <div class="equipment-meta">
                <div class="meta-item">
                    <i data-lucide="hash"></i>
                    {{ $equipment->equipment_number }}
                </div>
                <div class="meta-item">
                    <i data-lucide="tag"></i>
                    {{ $equipment->category->name }}
                </div>
                @if($equipment->brand)
                    <div class="meta-item">
                        <i data-lucide="award"></i>
                        {{ $equipment->brand }} {{ $equipment->model }}
                    </div>
                @endif
            </div>
            <div style="margin-top: 15px;">
                <span class="status-badge status-{{ $equipment->status }}">
                    @switch($equipment->status)
                        @case('available') متاح @break
                        @case('in_use') قيد الاستخدام @break
                        @case('maintenance') صيانة @break
                        @case('breakdown') عطل @break
                        @case('disposed') متوقف @break
                        @case('rented_out') مؤجر @break
                    @endswitch
                </span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-primary">
                <i data-lucide="edit"></i>
                تعديل
            </a>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="content-grid">
        <div>
            <!-- معلومات أساسية -->
            <div class="info-card">
                <h3 class="card-title">معلومات أساسية</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">رقم المعدة</span>
                        <span class="info-value">{{ $equipment->equipment_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">التصنيف</span>
                        <span class="info-value">{{ $equipment->category->name }}</span>
                    </div>
                    @if($equipment->brand)
                        <div class="info-item">
                            <span class="info-label">العلامة التجارية</span>
                            <span class="info-value">{{ $equipment->brand }}</span>
                        </div>
                    @endif
                    @if($equipment->model)
                        <div class="info-item">
                            <span class="info-label">الموديل</span>
                            <span class="info-value">{{ $equipment->model }}</span>
                        </div>
                    @endif
                    @if($equipment->year)
                        <div class="info-item">
                            <span class="info-label">سنة الصنع</span>
                            <span class="info-value">{{ $equipment->year }}</span>
                        </div>
                    @endif
                    @if($equipment->serial_number)
                        <div class="info-item">
                            <span class="info-label">الرقم التسلسلي</span>
                            <span class="info-value">{{ $equipment->serial_number }}</span>
                        </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">نوع الملكية</span>
                        <span class="info-value">
                            @if($equipment->ownership == 'owned') ملك
                            @elseif($equipment->ownership == 'rented') مستأجر
                            @else تأجير تمويلي
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- الموقع والمشغل -->
            @if($equipment->currentProject || $equipment->assignedOperator || $equipment->current_location)
                <div class="info-card">
                    <h3 class="card-title">الموقع والتخصيص</h3>
                    <div class="info-grid">
                        @if($equipment->currentProject)
                            <div class="info-item">
                                <span class="info-label">المشروع الحالي</span>
                                <span class="info-value">{{ $equipment->currentProject->name }}</span>
                            </div>
                        @endif
                        @if($equipment->current_location)
                            <div class="info-item">
                                <span class="info-label">الموقع</span>
                                <span class="info-value">{{ $equipment->current_location }}</span>
                            </div>
                        @endif
                        @if($equipment->assignedOperator)
                            <div class="info-item">
                                <span class="info-label">المشغل</span>
                                <span class="info-value">{{ $equipment->assignedOperator->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- آخر الأنشطة -->
            <div class="info-card">
                <h3 class="card-title">آخر الأنشطة</h3>
                @if($equipment->usageLogs->count() > 0)
                    <ul class="activity-list">
                        @foreach($equipment->usageLogs->take(5) as $usage)
                            <li class="activity-item">
                                <div>
                                    <strong>تسجيل استخدام:</strong> {{ $usage->hours_worked }} ساعة
                                    @if($usage->project)
                                        - {{ $usage->project->name }}
                                    @endif
                                </div>
                                <span class="activity-date">{{ $usage->usage_date->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="text-align: center; color: #999; padding: 20px;">لا توجد أنشطة مسجلة</p>
                @endif
            </div>
        </div>

        <div>
            <!-- إحصائيات -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($equipment->current_hours, 1) }}</div>
                    <div class="stat-label">ساعات التشغيل الكلية</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ff9500, #ff5e3a);">
                    <div class="stat-value">{{ number_format($equipment->hours_since_last_maintenance, 1) }}</div>
                    <div class="stat-label">ساعات منذ آخر صيانة</div>
                </div>
            </div>

            <!-- روابط سريعة -->
            <div class="info-card">
                <h3 class="card-title">إجراءات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('equipment.usage', $equipment) }}" class="btn btn-secondary" style="justify-content: center;">
                        <i data-lucide="clock"></i>
                        سجل الاستخدام
                    </a>
                    <a href="{{ route('equipment.maintenance', $equipment) }}" class="btn btn-secondary" style="justify-content: center;">
                        <i data-lucide="wrench"></i>
                        سجل الصيانة
                    </a>
                    @if($equipment->status == 'available')
                        <button class="btn btn-primary" style="justify-content: center;">
                            <i data-lucide="briefcase"></i>
                            تخصيص لمشروع
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
