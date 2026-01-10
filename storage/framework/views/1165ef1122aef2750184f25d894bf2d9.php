<?php $__env->startSection('content'); ?>
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
    <?php if(session('success')): ?>
        <div class="success-message">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="detail-header">
        <div class="header-info">
            <h1><?php echo e($equipment->name); ?></h1>
            <div class="equipment-meta">
                <div class="meta-item">
                    <i data-lucide="hash"></i>
                    <?php echo e($equipment->equipment_number); ?>

                </div>
                <div class="meta-item">
                    <i data-lucide="tag"></i>
                    <?php echo e($equipment->category->name); ?>

                </div>
                <?php if($equipment->brand): ?>
                    <div class="meta-item">
                        <i data-lucide="award"></i>
                        <?php echo e($equipment->brand); ?> <?php echo e($equipment->model); ?>

                    </div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 15px;">
                <span class="status-badge status-<?php echo e($equipment->status); ?>">
                    <?php switch($equipment->status):
                        case ('available'): ?> متاح <?php break; ?>
                        <?php case ('in_use'): ?> قيد الاستخدام <?php break; ?>
                        <?php case ('maintenance'): ?> صيانة <?php break; ?>
                        <?php case ('breakdown'): ?> عطل <?php break; ?>
                        <?php case ('disposed'): ?> متوقف <?php break; ?>
                        <?php case ('rented_out'): ?> مؤجر <?php break; ?>
                    <?php endswitch; ?>
                </span>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('equipment.edit', $equipment)); ?>" class="btn btn-primary">
                <i data-lucide="edit"></i>
                تعديل
            </a>
            <a href="<?php echo e(route('equipment.index')); ?>" class="btn btn-secondary">
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
                        <span class="info-value"><?php echo e($equipment->equipment_number); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">التصنيف</span>
                        <span class="info-value"><?php echo e($equipment->category->name); ?></span>
                    </div>
                    <?php if($equipment->brand): ?>
                        <div class="info-item">
                            <span class="info-label">العلامة التجارية</span>
                            <span class="info-value"><?php echo e($equipment->brand); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($equipment->model): ?>
                        <div class="info-item">
                            <span class="info-label">الموديل</span>
                            <span class="info-value"><?php echo e($equipment->model); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($equipment->year): ?>
                        <div class="info-item">
                            <span class="info-label">سنة الصنع</span>
                            <span class="info-value"><?php echo e($equipment->year); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($equipment->serial_number): ?>
                        <div class="info-item">
                            <span class="info-label">الرقم التسلسلي</span>
                            <span class="info-value"><?php echo e($equipment->serial_number); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">نوع الملكية</span>
                        <span class="info-value">
                            <?php if($equipment->ownership == 'owned'): ?> ملك
                            <?php elseif($equipment->ownership == 'rented'): ?> مستأجر
                            <?php else: ?> تأجير تمويلي
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- الموقع والمشغل -->
            <?php if($equipment->currentProject || $equipment->assignedOperator || $equipment->current_location): ?>
                <div class="info-card">
                    <h3 class="card-title">الموقع والتخصيص</h3>
                    <div class="info-grid">
                        <?php if($equipment->currentProject): ?>
                            <div class="info-item">
                                <span class="info-label">المشروع الحالي</span>
                                <span class="info-value"><?php echo e($equipment->currentProject->name); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($equipment->current_location): ?>
                            <div class="info-item">
                                <span class="info-label">الموقع</span>
                                <span class="info-value"><?php echo e($equipment->current_location); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($equipment->assignedOperator): ?>
                            <div class="info-item">
                                <span class="info-label">المشغل</span>
                                <span class="info-value"><?php echo e($equipment->assignedOperator->name); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- آخر الأنشطة -->
            <div class="info-card">
                <h3 class="card-title">آخر الأنشطة</h3>
                <?php if($equipment->usageLogs->count() > 0): ?>
                    <ul class="activity-list">
                        <?php $__currentLoopData = $equipment->usageLogs->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="activity-item">
                                <div>
                                    <strong>تسجيل استخدام:</strong> <?php echo e($usage->hours_worked); ?> ساعة
                                    <?php if($usage->project): ?>
                                        - <?php echo e($usage->project->name); ?>

                                    <?php endif; ?>
                                </div>
                                <span class="activity-date"><?php echo e($usage->usage_date->format('Y-m-d')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">لا توجد أنشطة مسجلة</p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <!-- إحصائيات -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo e(number_format($equipment->current_hours, 1)); ?></div>
                    <div class="stat-label">ساعات التشغيل الكلية</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ff9500, #ff5e3a);">
                    <div class="stat-value"><?php echo e(number_format($equipment->hours_since_last_maintenance, 1)); ?></div>
                    <div class="stat-label">ساعات منذ آخر صيانة</div>
                </div>
            </div>

            <!-- روابط سريعة -->
            <div class="info-card">
                <h3 class="card-title">إجراءات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?php echo e(route('equipment.usage', $equipment)); ?>" class="btn btn-secondary" style="justify-content: center;">
                        <i data-lucide="clock"></i>
                        سجل الاستخدام
                    </a>
                    <a href="<?php echo e(route('equipment.maintenance', $equipment)); ?>" class="btn btn-secondary" style="justify-content: center;">
                        <i data-lucide="wrench"></i>
                        سجل الصيانة
                    </a>
                    <?php if($equipment->status == 'available'): ?>
                        <button class="btn btn-primary" style="justify-content: center;">
                            <i data-lucide="briefcase"></i>
                            تخصيص لمشروع
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/equipment/show.blade.php ENDPATH**/ ?>