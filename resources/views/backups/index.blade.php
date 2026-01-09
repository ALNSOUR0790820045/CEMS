@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%);
        color: white;
        padding: 20px 24px;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        opacity: 0.1;
        border-radius: 50%;
    }
    
    .stat-card.primary::before { background: #0071e3; }
    .stat-card.success::before { background: #34c759; }
    .stat-card.info::before { background: #00c4cc; }
    .stat-card.warning::before { background: #ff9500; }
    
    .stat-label {
        color: #86868b;
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,113,227,0.3);
    }
    
    .btn-success {
        background: #34c759;
        color: white;
    }
    
    .btn-success:hover {
        background: #30b350;
    }
    
    .btn-danger {
        background: #ff3b30;
        color: white;
    }
    
    .btn-danger:hover {
        background: #ff453a;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
    
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: #f5f5f7;
    }
    
    th {
        padding: 16px;
        text-align: right;
        font-weight: 600;
        color: #1d1d1f;
        font-size: 0.9rem;
        border-bottom: 2px solid #e5e5e7;
    }
    
    td {
        padding: 16px;
        border-bottom: 1px solid #e5e5e7;
        color: #1d1d1f;
    }
    
    tr:hover {
        background: #fafafa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .badge-database { background: #e3f2fd; color: #1976d2; }
    .badge-files { background: #fff3e0; color: #f57c00; }
    .badge-full { background: #f3e5f5; color: #7b1fa2; }
    .badge-success { background: #e8f5e9; color: #2e7d32; }
    .badge-danger { background: #ffebee; color: #c62828; }
    .badge-warning { background: #fff3e0; color: #ef6c00; }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    
    .alert-danger {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 20px;
        margin: 0;
        list-style: none;
    }
    
    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        color: #1d1d1f;
        transition: all 0.2s;
    }
    
    .pagination a:hover {
        background: #f5f5f7;
    }
    
    .pagination .active span {
        background: #0071e3;
        color: white;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1d1d1f; margin: 0;">
            <i data-lucide="shield-check" style="width: 32px; height: 32px; vertical-align: middle;"></i>
            النسخ الاحتياطية
        </h1>
        <a href="{{ route('backups.create') }}" class="btn btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            نسخة احتياطية جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card primary">
            <div class="stat-label">
                <i data-lucide="database" style="width: 20px; height: 20px;"></i>
                نسخ قاعدة البيانات
            </div>
            <div class="stat-value">{{ $stats['database_count'] }}</div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-label">
                <i data-lucide="folder" style="width: 20px; height: 20px;"></i>
                نسخ الملفات
            </div>
            <div class="stat-value">{{ $stats['files_count'] }}</div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-label">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
                إجمالي النسخ
            </div>
            <div class="stat-value">{{ $stats['total_count'] }}</div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-label">
                <i data-lucide="hard-drive" style="width: 20px; height: 20px;"></i>
                الحجم الإجمالي
            </div>
            <div class="stat-value" style="font-size: 1.5rem;">
                @php
                    $size = $stats['total_size'];
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $unit = 0;
                    while ($size >= 1024 && $unit < count($units) - 1) {
                        $size /= 1024;
                        $unit++;
                    }
                    echo round($size, 2) . ' ' . $units[$unit];
                @endphp
            </div>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="table-container">
        <div class="card-header">
            <i data-lucide="list" style="width: 20px; height: 20px;"></i>
            قائمة النسخ الاحتياطية
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>الحجم</th>
                    <th>الحالة</th>
                    <th>المدة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>المنشئ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr>
                        <td>
                            <strong>{{ $backup->name }}</strong><br>
                            <small style="color: #86868b;">{{ $backup->filename }}</small>
                        </td>
                        <td>
                            @if($backup->type === 'database')
                                <span class="badge badge-database">
                                    <i data-lucide="database" style="width: 12px; height: 12px;"></i>
                                    قاعدة بيانات
                                </span>
                            @elseif($backup->type === 'files')
                                <span class="badge badge-files">
                                    <i data-lucide="folder" style="width: 12px; height: 12px;"></i>
                                    ملفات
                                </span>
                            @else
                                <span class="badge badge-full">
                                    <i data-lucide="layers" style="width: 12px; height: 12px;"></i>
                                    كامل
                                </span>
                            @endif
                        </td>
                        <td>{{ $backup->formatted_size }}</td>
                        <td>
                            @if($backup->status === 'completed')
                                <span class="badge badge-success">مكتمل</span>
                            @elseif($backup->status === 'failed')
                                <span class="badge badge-danger">فشل</span>
                            @else
                                <span class="badge badge-warning">{{ $backup->status }}</span>
                            @endif
                        </td>
                        <td>{{ $backup->duration }}</td>
                        <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $backup->creator?->name ?? 'النظام' }}</td>
                        <td>
                            <div class="action-buttons">
                                @if($backup->status === 'completed')
                                    <a href="{{ route('backups.download', $backup) }}" class="btn btn-success btn-sm">
                                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                        تحميل
                                    </a>
                                    
                                    @if($backup->type === 'database')
                                        <form method="POST" action="{{ route('backups.restore', $backup) }}" style="display: inline; margin: 0;"
                                              onsubmit="return confirm('هل أنت متأكد من استرجاع هذه النسخة الاحتياطية؟ سيتم استبدال قاعدة البيانات الحالية!');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                                استرجاع
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                <form method="POST" action="{{ route('backups.destroy', $backup) }}" style="display: inline; margin: 0;"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #86868b;">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px;"></i>
                            <div>لا توجد نسخ احتياطية حتى الآن</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($backups->hasPages())
            <div style="border-top: 1px solid #e5e5e7;">
                {{ $backups->links() }}
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
