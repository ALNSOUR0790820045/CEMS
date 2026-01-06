<?php $__env->startSection('content'); ?>
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: #005bb5;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-success {
        background: #28a745;
        color: white;
    }
    
    .btn-danger {
        background: #dc3545;
        color: white;
        padding: 6px 12px;
        font-size: 0.8rem;
    }
    
    .filters {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        min-width: 200px;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table th {
        padding: 12px;
        text-align: right;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        text-align: right;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-critical {
        background: #dc3545;
        color: white;
    }
    
    .badge-success {
        background: #28a745;
        color: white;
    }
    
    .badge-warning {
        background: #ffc107;
        color: #000;
    }
    
    .badge-info {
        background: #17a2b8;
        color: white;
    }
    
    .actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }
    
    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .breadcrumb {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
    }
    
    .stat-card:nth-child(2) {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card:nth-child(3) {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card:nth-child(4) {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

<div class="breadcrumb">
    <a href="<?php echo e(route('dashboard')); ?>">الرئيسية</a> / 
    <a href="<?php echo e(route('tenders.index')); ?>">العطاءات</a> / 
    <a href="<?php echo e(route('tenders.show', $tender)); ?>"><?php echo e($tender->name); ?></a> / 
    <span>الأنشطة</span>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">أنشطة العطاء: <?php echo e($tender->name); ?></h1>
        <p style="color: #6c757d; margin-top: 8px;">رمز العطاء: <?php echo e($tender->tender_code); ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?php echo e(route('tender-activities.gantt', $tender)); ?>" class="btn btn-success">
            <i data-lucide="gantt-chart"></i>
            مخطط جانت
        </a>
        <a href="<?php echo e(route('tender-activities.cpm-analysis', $tender)); ?>" class="btn btn-secondary">
            <i data-lucide="git-branch"></i>
            تحليل CPM
        </a>
        <a href="<?php echo e(route('tender-activities.create', $tender)); ?>" class="btn btn-primary">
            <i data-lucide="plus"></i>
            إضافة نشاط
        </a>
    </div>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?php echo e($activities->total()); ?></div>
        <div class="stat-label">إجمالي الأنشطة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo e($activities->where('is_critical', true)->count()); ?></div>
        <div class="stat-label">أنشطة حرجة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo e($activities->sum('duration_days')); ?></div>
        <div class="stat-label">إجمالي المدة (أيام)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo e(number_format($activities->sum('estimated_cost'), 0)); ?></div>
        <div class="stat-label">التكلفة المقدرة (ريال)</div>
    </div>
</div>

<div class="card">
    <form method="GET" action="<?php echo e(route('tender-activities.index', $tender)); ?>">
        <div class="filters">
            <input type="text" name="search" class="filter-input" placeholder="بحث (رمز أو اسم النشاط)..." value="<?php echo e(request('search')); ?>">
            
            <select name="wbs_id" class="filter-input">
                <option value="">كل عناصر WBS</option>
                <?php $__currentLoopData = $wbsItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wbs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($wbs->id); ?>" <?php echo e(request('wbs_id') == $wbs->id ? 'selected' : ''); ?>>
                        <?php echo e($wbs->wbs_code); ?> - <?php echo e($wbs->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            
            <select name="type" class="filter-input">
                <option value="">كل الأنواع</option>
                <option value="task" <?php echo e(request('type') == 'task' ? 'selected' : ''); ?>>مهمة</option>
                <option value="milestone" <?php echo e(request('type') == 'milestone' ? 'selected' : ''); ?>>معلم</option>
                <option value="summary" <?php echo e(request('type') == 'summary' ? 'selected' : ''); ?>>ملخص</option>
            </select>
            
            <select name="is_critical" class="filter-input">
                <option value="">الكل</option>
                <option value="1" <?php echo e(request('is_critical') == '1' ? 'selected' : ''); ?>>حرج فقط</option>
                <option value="0" <?php echo e(request('is_critical') == '0' ? 'selected' : ''); ?>>غير حرج</option>
            </select>
            
            <button type="submit" class="btn btn-primary">بحث</button>
        </div>
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th>الكود</th>
                <th>اسم النشاط</th>
                <th>WBS</th>
                <th>المدة (أيام)</th>
                <th>البداية المبكرة</th>
                <th>النهاية المبكرة</th>
                <th>Float الكلي</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><strong><?php echo e($activity->activity_code); ?></strong></td>
                <td><?php echo e($activity->name); ?></td>
                <td><?php echo e($activity->wbs ? $activity->wbs->wbs_code : '-'); ?></td>
                <td><?php echo e($activity->duration_days); ?></td>
                <td><?php echo e($activity->early_start ?? '-'); ?></td>
                <td><?php echo e($activity->early_finish ?? '-'); ?></td>
                <td><?php echo e($activity->total_float ?? 0); ?></td>
                <td>
                    <?php if($activity->is_critical): ?>
                        <span class="badge badge-critical">حرج</span>
                    <?php else: ?>
                        <span class="badge badge-success">عادي</span>
                    <?php endif; ?>
                    
                    <?php if($activity->type == 'milestone'): ?>
                        <span class="badge badge-info">معلم</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="actions">
                        <a href="<?php echo e(route('tender-activities.edit', [$tender, $activity])); ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                            <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                            تعديل
                        </a>
                        <form method="POST" action="<?php echo e(route('tender-activities.destroy', [$tender, $activity])); ?>" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                حذف
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px; color: #6c757d;">
                    لا توجد أنشطة. <a href="<?php echo e(route('tender-activities.create', $tender)); ?>">أضف نشاط جديد</a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <?php echo e($activities->links()); ?>

    </div>
</div>

<script>
    lucide.createIcons();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/tender-activities/index.blade.php ENDPATH**/ ?>