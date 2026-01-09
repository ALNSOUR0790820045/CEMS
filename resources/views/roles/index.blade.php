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

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }

    .btn-edit {
        background: #34c759;
        color: white;
    }

    .btn-edit:hover {
        background: #2db34c;
    }

    .btn-delete {
        background: #ff3b30;
        color: white;
    }

    .btn-delete:hover {
        background: #e62e24;
    }

    .btn-view {
        background: #007aff;
        color: white;
    }

    .btn-view:hover {
        background: #0062cc;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: var(--apple-bg);
    }

    .table th {
        padding: 16px;
        text-align: right;
        font-weight: 600;
        color: #86868b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 16px;
        border-top: 1px solid var(--border);
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background: rgba(0, 113, 227, 0.03);
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

    .actions {
        display: flex;
        gap: 8px;
    }

    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
        border: 1px solid rgba(52, 199, 89, 0.2);
    }

    .alert-error {
        background: rgba(255, 59, 48, 0.1);
        color: #ff3b30;
        border: 1px solid rgba(255, 59, 48, 0.2);
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">إدارة الأدوار</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة دور جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>اسم الدور</th>
                    <th>عدد الصلاحيات</th>
                    <th>عدد المستخدمين</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>
                        <strong>{{ $role->name }}</strong>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $role->permissions_count }} صلاحية
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-success">
                            {{ $role->users_count }} مستخدم
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-view">
                                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                عرض
                            </a>
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-edit">
                                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                                تعديل
                            </a>
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-delete">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #86868b;">
                        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                        <p>لا توجد أدوار</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
