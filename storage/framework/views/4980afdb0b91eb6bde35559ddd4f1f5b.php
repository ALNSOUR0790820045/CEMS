<?php $__env->startSection('content'); ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #0077ED;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 6px;
        color: #86868b;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f5f5f7;
    }

    th {
        padding: 15px;
        text-align: right;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1d1d1f;
        border-bottom: 1px solid #d2d2d7;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #f5f5f7;
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
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-draft {
        background: #f0f0f0;
        color: #666;
    }

    .badge-sent {
        background: #e3f2fd;
        color: #1976d2;
    }

    .badge-confirmed {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-received {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-cancelled {
        background: #ffebee;
        color: #d32f2f;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-view {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-view:hover {
        background: #bbdefb;
    }

    .btn-edit {
        background: #fff3e0;
        color: #f57c00;
    }

    .btn-edit:hover {
        background: #ffe0b2;
    }

    .btn-delete {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background: #ffcdd2;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-success {
        background: #e8f5e9;
        color: #388e3c;
        border: 1px solid #c8e6c9;
    }

    .alert-error {
        background: #ffebee;
        color: #d32f2f;
        border: 1px solid #ffcdd2;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        padding: 20px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--text);
        font-weight: 500;
    }

    .pagination a:hover {
        background: #f5f5f7;
    }

    .pagination .active {
        background: var(--accent);
        color: white;
    }
</style>

<div class="page-header">
    <h1 class="page-title">أوامر الشراء</h1>
    <a href="<?php echo e(route('purchase-orders.create')); ?>" class="btn-primary">
        <i data-lucide="plus"></i>
        إنشاء أمر شراء جديد
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="filters-card">
    <form method="GET" action="<?php echo e(route('purchase-orders.index')); ?>">
        <div class="filters-grid">
            <div class="form-group">
                <label>المورد</label>
                <select name="supplier_id" class="form-control">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>" <?php echo e(request('supplier_id') == $supplier->id ? 'selected' : ''); ?>>
                            <?php echo e($supplier->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label>الحالة</label>
                <select name="status" class="form-control">
                    <option value="">الكل</option>
                    <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                    <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>مرسل</option>
                    <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>مؤكد</option>
                    <option value="received" <?php echo e(request('status') == 'received' ? 'selected' : ''); ?>>مستلم</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                </select>
            </div>

            <div class="form-group">
                <label>من تاريخ</label>
                <input type="date" name="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
            </div>

            <div class="form-group">
                <label>إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i data-lucide="filter"></i>
            تطبيق الفلاتر
        </button>
    </form>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>رقم الأمر</th>
                <th>المورد</th>
                <th>تاريخ الأمر</th>
                <th>المجموع</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($order->order_number); ?></strong></td>
                    <td><?php echo e($order->supplier->name); ?></td>
                    <td><?php echo e($order->order_date->format('Y-m-d')); ?></td>
                    <td><strong><?php echo e(number_format($order->total, 2)); ?> ريال</strong></td>
                    <td>
                        <?php
                            $statusMap = [
                                'draft' => ['class' => 'badge-draft', 'text' => 'مسودة'],
                                'sent' => ['class' => 'badge-sent', 'text' => 'مرسل'],
                                'confirmed' => ['class' => 'badge-confirmed', 'text' => 'مؤكد'],
                                'received' => ['class' => 'badge-received', 'text' => 'مستلم'],
                                'cancelled' => ['class' => 'badge-cancelled', 'text' => 'ملغي'],
                            ];
                        ?>
                        <span class="badge <?php echo e($statusMap[$order->status]['class']); ?>">
                            <?php echo e($statusMap[$order->status]['text']); ?>

                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('purchase-orders.show', $order)); ?>" class="btn-sm btn-view">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                عرض
                            </a>
                            <?php if(!in_array($order->status, ['confirmed', 'received', 'cancelled'])): ?>
                                <a href="<?php echo e(route('purchase-orders.edit', $order)); ?>" class="btn-sm btn-edit">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                    تعديل
                                </a>
                            <?php endif; ?>
                            <?php if($order->status !== 'received'): ?>
                                <form method="POST" action="<?php echo e(route('purchase-orders.destroy', $order)); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الأمر؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-sm btn-delete">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        حذف
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #86868b;">
                        لا توجد أوامر شراء
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($purchaseOrders->hasPages()): ?>
        <div class="pagination">
            <?php echo e($purchaseOrders->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/purchase-orders/index.blade.php ENDPATH**/ ?>