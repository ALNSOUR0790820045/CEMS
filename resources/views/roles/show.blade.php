@extends('layouts.app')

@section('content')
<style>
    .container {
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
        color: var(--text);
    }

    .breadcrumb {
        display: flex;
        gap: 8px;
        color: #86868b;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #005bb5;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: #86868b;
        color: white;
    }

    .btn-secondary:hover {
        background: #6e6e73;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-label {
        font-size: 0.85rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-primary {
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
    }

    .badge-success {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .permission-module {
        background: var(--apple-bg);
        border-radius: 12px;
        padding: 16px;
    }

    .module-title {
        font-weight: 700;
        color: var(--text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .module-count {
        background: var(--accent);
        color: white;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    .permission-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .permission-tag {
        background: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        color: var(--text);
        border: 1px solid var(--border);
    }

    .users-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }

    .user-card {
        background: var(--apple-bg);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: var(--text);
        font-size: 0.95rem;
    }

    .user-email {
        font-size: 0.8rem;
        color: #86868b;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #86868b;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }
</style>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $role->name }}</h1>
            <div class="breadcrumb">
                <a href="{{ route('roles.index') }}">الأدوار</a>
                <span>/</span>
                <span>عرض</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                تعديل
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">
            <i data-lucide="info" style="width: 24px; height: 24px;"></i>
            معلومات الدور
        </h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">اسم الدور</div>
                <div class="info-value">{{ $role->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">عدد الصلاحيات</div>
                <div class="info-value">
                    <span class="badge badge-primary">
                        {{ $role->permissions->count() }} صلاحية
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">عدد المستخدمين</div>
                <div class="info-value">
                    <span class="badge badge-success">
                        {{ $role->users->count() }} مستخدم
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">تاريخ الإنشاء</div>
                <div class="info-value">{{ $role->created_at->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">
            <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
            الصلاحيات ({{ $role->permissions->count() }})
        </h2>

        @if($groupedPermissions->count() > 0)
            <div class="permissions-grid">
                @php
                    $moduleNames = [
                        'companies' => 'الشركات',
                        'countries' => 'الدول',
                        'cities' => 'المدن',
                        'currencies' => 'العملات',
                        'units' => 'الوحدات',
                        'payment-terms' => 'شروط الدفع',
                        'users' => 'المستخدمين',
                        'roles' => 'الأدوار',
                        'branches' => 'الفروع',
                        'departments' => 'الأقسام',
                        'projects' => 'المشاريع',
                        'sites' => 'المواقع',
                        'accounting' => 'المحاسبة',
                        'invoices' => 'الفواتير',
                        'contracts' => 'العقود',
                        'guarantees' => 'الضمانات',
                        'procurement' => 'المشتريات',
                        'warehouses' => 'المستودعات',
                        'employees' => 'الموظفون',
                        'payroll' => 'الرواتب',
                        'subcontractors' => 'مقاولو الباطن',
                        'consultants' => 'الاستشاريون',
                        'tenders' => 'العطاءات',
                        'quotes' => 'عروض الأسعار',
                        'archive' => 'الأرشيف',
                        'correspondence' => 'المراسلات',
                        'equipment' => 'المعدات',
                        'maintenance' => 'الصيانة',
                        'reports' => 'التقارير',
                        'settings' => 'الإعدادات',
                        'backups' => 'النسخ الاحتياطي',
                    ];

                    $actionNames = [
                        'view' => 'عرض',
                        'create' => 'إضافة',
                        'edit' => 'تعديل',
                        'delete' => 'حذف',
                        'restore' => 'استعادة',
                    ];
                @endphp

                @foreach($groupedPermissions as $module => $permissions)
                    <div class="permission-module">
                        <div class="module-title">
                            <span class="module-count">{{ count($permissions) }}</span>
                            {{ $moduleNames[$module] ?? $module }}
                        </div>
                        <div class="permission-list">
                            @foreach($permissions as $permission)
                                @php
                                    $parts = explode('.', $permission->name);
                                    $action = end($parts);
                                @endphp
                                <span class="permission-tag">{{ $actionNames[$action] ?? $action }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i data-lucide="shield-off" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                <p>لا توجد صلاحيات لهذا الدور</p>
            </div>
        @endif
    </div>

    <div class="card">
        <h2 class="card-title">
            <i data-lucide="users" style="width: 24px; height: 24px;"></i>
            المستخدمون ({{ $role->users->count() }})
        </h2>

        @if($role->users->count() > 0)
            <div class="users-list">
                @foreach($role->users as $user)
                    <div class="user-card">
                        <div class="user-avatar">{{ $user->initials }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i data-lucide="user-x" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                <p>لا يوجد مستخدمون لهذا الدور</p>
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
