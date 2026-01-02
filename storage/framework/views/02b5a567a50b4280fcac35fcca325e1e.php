<?php $__env->startSection('content'); ?>
<style>
    .page-header {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #005bb5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        flex: 1;
    }

    .filter-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: #666;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: var(--accent);
    }

    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
        font-size: 0.85rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 16px;
        border-top: 1px solid #eee;
        font-size: 0.9rem;
    }

    tbody tr {
        transition: background 0.2s;
    }

    tbody tr:hover {
        background: #f9f9f9;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-gold {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
    }

    .badge-success {
        background: #34c759;
        color: white;
    }

    .badge-danger {
        background: #ff3b30;
        color: white;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .btn-edit {
        background: #007aff;
        color: white;
    }

    .btn-edit:hover {
        background: #0051d5;
    }

    .btn-delete {
        background: #ff3b30;
        color: white;
    }

    .btn-delete:hover {
        background: #cc0000;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1f2eb;
        color: #0c5540;
        border: 1px solid #a3cfbb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        width: 64px;
        height: 64px;
        color: #ccc;
        margin-bottom: 20px;
    }
</style>

<div class="page-header">
    <div class="header-top">
        <h1 class="page-title">الفروع</h1>
        <a href="<?php echo e(route('branches.create')); ?>" class="btn btn-primary">
            <i data-lucide="plus"></i>
            إضافة فرع
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('branches.index')); ?>" class="filters-section">
        <div class="filter-group">
            <label class="filter-label">الشركة</label>
            <select name="company_id" class="filter-select" onchange="this.form.submit()">
                <option value="">جميع الشركات</option>
                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($company->id); ?>" <?php echo e(request('company_id') == $company->id ? 'selected' : ''); ?>>
                        <?php echo e($company->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">بحث</label>
            <input type="text" name="search" class="filter-input" placeholder="ابحث بالاسم أو الكود..." value="<?php echo e(request('search')); ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <i data-lucide="search"></i>
            بحث
        </button>
    </form>

    <div class="table-container">
        <?php if($branches->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>الشركة</th>
                        <th>المدينة</th>
                        <th>رئيسي؟</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($branch->code); ?></strong></td>
                            <td>
                                <?php echo e($branch->name); ?>

                                <?php if($branch->is_main): ?>
                                    <span class="badge badge-gold">رئيسي</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($branch->company->name ?? '-'); ?></td>
                            <td><?php echo e($branch->city->name ?? '-'); ?></td>
                            <td>
                                <?php if($branch->is_main): ?>
                                    <span style="color: #FFD700;">✓</span>
                                <?php else: ?>
                                    <span style="color: #ccc;">✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($branch->is_active): ?>
                                    <span class="badge badge-success">نشط</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">غير نشط</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?php echo e(route('branches.edit', $branch)); ?>" class="btn btn-sm btn-edit">
                                        <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                        تعديل
                                    </a>
                                    <form method="POST" action="<?php echo e(route('branches.destroy', $branch)); ?>" style="display: inline; margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا الفرع؟')">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <p>لا توجد فروع</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/branches/index.blade.php ENDPATH**/ ?>