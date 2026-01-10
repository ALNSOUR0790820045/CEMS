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

    .badge-success {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
    }

    .badge-warning {
        background: rgba(255, 149, 0, 0.1);
        color: #ff9500;
    }

    .badge-secondary {
        background: rgba(134, 134, 139, 0.1);
        color: #86868b;
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
        margin-left: 12px;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: var(--text);
    }

    .user-email {
        font-size: 0.85rem;
        color: #86868b;
    }

    .roles-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">إدارة المستخدمين</h1>
        @can('users.create')
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
            إضافة مستخدم جديد
        </a>
        @endcan
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
                    <th>المستخدم</th>
                    <th>الوظيفة</th>
                    <th>الأدوار</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">{{ $user->initials }}</div>
                            <div class="user-details">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->job_title ?? '-' }}</td>
                    <td>
                        <div class="roles-list">
                            @forelse($user->roles as $role)
                                <span class="badge badge-secondary">{{ $role->name }}</span>
                            @empty
                                <span class="badge badge-warning">لا يوجد دور</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-warning">غير نشط</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            @can('users.view')
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-view">
                                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                عرض
                            </a>
                            @endcan
                            @can('users.edit')
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-edit">
                                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                                تعديل
                            </a>
                            @endcan
                            @can('users.delete')
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-delete">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                    حذف
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #86868b;">
                        <i data-lucide="users" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                        <p>لا يوجد مستخدمون</p>
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
