<?php $__env->startSection('content'); ?>
<style>
    .equipment-container {
        padding: 40px;
        max-width: 1400px;
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

    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #666;
    }

    .filter-input, .filter-select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: 'Cairo', sans-serif;
        transition: border-color 0.2s;
    }

    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #0071e3;
    }

    .equipment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .equipment-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        cursor: pointer;
    }

    .equipment-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .equipment-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .equipment-number {
        font-size: 0.8rem;
        color: #0071e3;
        font-weight: 600;
        background: rgba(0, 113, 227, 0.1);
        padding: 4px 8px;
        border-radius: 4px;
    }

    .equipment-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin: 10px 0;
    }

    .equipment-category {
        font-size: 0.85rem;
        color: #666;
    }

    .equipment-details {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: 15px 0;
        padding: 15px 0;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
    }

    .detail-label {
        color: #666;
    }

    .detail-value {
        font-weight: 600;
        color: var(--text);
    }

    .equipment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
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

    .status-breakdown {
        background: #f5c6cb;
        color: #721c24;
    }

    .equipment-actions {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        background: #f5f5f5;
        border-color: #0071e3;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
    }

    .empty-state-text {
        color: #999;
        margin-bottom: 20px;
    }
</style>

<div class="equipment-container">
    <div class="page-header">
        <h1 class="page-title">إدارة المعدات</h1>
        <a href="<?php echo e(route('equipment.create')); ?>" class="btn-primary">
            <i data-lucide="plus"></i>
            إضافة معدة جديدة
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="success-message">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="filters-section">
        <form method="GET" action="<?php echo e(route('equipment.index')); ?>">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">البحث</label>
                    <input type="text" name="search" class="filter-input" placeholder="رقم المعدة، الاسم، الرقم التسلسلي..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="filter-group">
                    <label class="filter-label">الحالة</label>
                    <select name="status" class="filter-select">
                        <option value="">كل الحالات</option>
                        <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>>متاح</option>
                        <option value="in_use" <?php echo e(request('status') == 'in_use' ? 'selected' : ''); ?>>قيد الاستخدام</option>
                        <option value="maintenance" <?php echo e(request('status') == 'maintenance' ? 'selected' : ''); ?>>صيانة</option>
                        <option value="breakdown" <?php echo e(request('status') == 'breakdown' ? 'selected' : ''); ?>>عطل</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">التصنيف</label>
                    <select name="category_id" class="filter-select">
                        <option value="">كل التصنيفات</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <i data-lucide="search"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if($equipment->count() > 0): ?>
        <div class="equipment-grid">
            <?php $__currentLoopData = $equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="equipment-card" onclick="window.location='<?php echo e(route('equipment.show', $item)); ?>'">
                    <div class="equipment-header">
                        <div>
                            <div class="equipment-number"><?php echo e($item->equipment_number); ?></div>
                            <h3 class="equipment-name"><?php echo e($item->name); ?></h3>
                            <div class="equipment-category"><?php echo e($item->category->name); ?></div>
                        </div>
                    </div>

                    <div class="equipment-details">
                        <?php if($item->brand): ?>
                            <div class="detail-row">
                                <span class="detail-label">العلامة التجارية:</span>
                                <span class="detail-value"><?php echo e($item->brand); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($item->model): ?>
                            <div class="detail-row">
                                <span class="detail-label">الموديل:</span>
                                <span class="detail-value"><?php echo e($item->model); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($item->currentProject): ?>
                            <div class="detail-row">
                                <span class="detail-label">المشروع:</span>
                                <span class="detail-value"><?php echo e($item->currentProject->name); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="detail-label">ساعات التشغيل:</span>
                            <span class="detail-value"><?php echo e(number_format($item->current_hours, 1)); ?> ساعة</span>
                        </div>
                    </div>

                    <div class="equipment-footer">
                        <span class="status-badge status-<?php echo e($item->status); ?>">
                            <?php switch($item->status):
                                case ('available'): ?> متاح <?php break; ?>
                                <?php case ('in_use'): ?> قيد الاستخدام <?php break; ?>
                                <?php case ('maintenance'): ?> صيانة <?php break; ?>
                                <?php case ('breakdown'): ?> عطل <?php break; ?>
                                <?php case ('disposed'): ?> متوقف <?php break; ?>
                                <?php case ('rented_out'): ?> مؤجر <?php break; ?>
                            <?php endswitch; ?>
                        </span>
                        <div class="equipment-actions" onclick="event.stopPropagation()">
                            <a href="<?php echo e(route('equipment.edit', $item)); ?>" class="btn-icon" title="تعديل">
                                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                            </a>
                            <a href="<?php echo e(route('equipment.show', $item)); ?>" class="btn-icon" title="عرض">
                                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: center;">
            <?php echo e($equipment->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <h2 class="empty-state-title">لا توجد معدات</h2>
            <p class="empty-state-text">ابدأ بإضافة معدات جديدة لإدارة أصولك</p>
            <a href="<?php echo e(route('equipment.create')); ?>" class="btn-primary">
                <i data-lucide="plus"></i>
                إضافة معدة جديدة
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/equipment/index.blade.php ENDPATH**/ ?>