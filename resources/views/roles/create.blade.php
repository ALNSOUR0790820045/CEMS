@extends('layouts.app')

@section('content')
<style>
    .container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }

    .breadcrumb {
        display: flex;
        gap: 8px;
        color: #86868b;
        font-size: 0.9rem;
    }

    .breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .permissions-section {
        margin-top: 32px;
    }

    .permissions-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--text);
    }

    .accordion {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .accordion-header {
        background: var(--apple-bg);
        padding: 16px 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .accordion-header:hover {
        background: rgba(0, 113, 227, 0.05);
    }

    .accordion-title {
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .accordion-icon {
        transition: transform 0.3s;
    }

    .accordion-header.active .accordion-icon {
        transform: rotate(180deg);
    }

    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding: 0 20px;
    }

    .accordion-header.active + .accordion-content {
        max-height: 500px;
        padding: 20px;
    }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--accent);
    }

    .checkbox-item label {
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn {
        padding: 12px 24px;
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
        font-size: 1rem;
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

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }

    .select-all-btn {
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
    }

    .select-all-btn:hover {
        background: rgba(0, 113, 227, 0.2);
    }

    .module-badge {
        background: var(--accent);
        color: white;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .error-message {
        color: #ff3b30;
        font-size: 0.85rem;
        margin-top: 4px;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">إضافة دور جديد</h1>
        <div class="breadcrumb">
            <a href="{{ route('roles.index') }}">الأدوار</a>
            <span>/</span>
            <span>إضافة جديد</span>
        </div>
    </div>

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf
        
        <div class="card">
            <div class="form-group">
                <label for="name" class="form-label">اسم الدور *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control" 
                    value="{{ old('name') }}" 
                    required
                    placeholder="مثال: مدير المشاريع"
                >
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="permissions-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 class="permissions-title">الصلاحيات</h3>
                    <button type="button" class="select-all-btn" onclick="toggleAllPermissions()">
                        <i data-lucide="check-square" style="width: 14px; height: 14px;"></i>
                        تحديد الكل
                    </button>
                </div>

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

                @foreach($permissions as $module => $modulePermissions)
                    <div class="accordion">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div class="accordion-title">
                                <span class="module-badge">{{ count($modulePermissions) }}</span>
                                <strong>{{ $moduleNames[$module] ?? $module }}</strong>
                            </div>
                            <i data-lucide="chevron-down" class="accordion-icon" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="checkbox-grid">
                                @foreach($modulePermissions as $permission)
                                    @php
                                        $parts = explode('.', $permission->name);
                                        $action = end($parts);
                                    @endphp
                                    <div class="checkbox-item">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->name }}" 
                                            id="permission_{{ $permission->id }}"
                                        >
                                        <label for="permission_{{ $permission->id }}">
                                            {{ $actionNames[$action] ?? $action }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                حفظ الدور
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function toggleAccordion(element) {
        element.classList.toggle('active');
        lucide.createIcons();
    }

    function toggleAllPermissions() {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }

    // Open first accordion by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstAccordion = document.querySelector('.accordion-header');
        if (firstAccordion) {
            firstAccordion.classList.add('active');
        }
    });
</script>
@endpush
@endsection
