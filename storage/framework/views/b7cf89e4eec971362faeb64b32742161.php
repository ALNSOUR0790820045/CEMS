<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 2rem; font-weight: 700;">المستودعات</h1>
        <a href="<?php echo e(route('warehouses.create')); ?>" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة مستودع جديد
        </a>
    </div>

    <?php if(session('success')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <form method="GET" action="<?php echo e(route('warehouses.index')); ?>" style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">البحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث بالكود أو الاسم..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الفرع</label>
                <select name="branch_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الفروع</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>>
                        <?php echo e($branch->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                تصفية
            </button>
            <a href="<?php echo e(route('warehouses.index')); ?>" style="background: #f5f5f7; color: #333; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                إعادة تعيين
            </a>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الكود</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الفرع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">المدير</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e5e5e7;">رئيسي؟</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid #e5e5e7; transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">
                    <td style="padding: 15px; font-weight: 600; color: #0071e3;"><?php echo e($warehouse->code); ?></td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600;"><?php echo e($warehouse->name); ?></div>
                        <?php if($warehouse->name_en): ?>
                        <div style="font-size: 0.85rem; color: #666;"><?php echo e($warehouse->name_en); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;"><?php echo e($warehouse->branch?->name ?? 'غير محدد'); ?></td>
                    <td style="padding: 15px;"><?php echo e($warehouse->manager?->name ?? 'غير محدد'); ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <?php if($warehouse->is_main): ?>
                        <span style="background: linear-gradient(135deg, #FFD700, #FFA500); color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; box-shadow: 0 2px 4px rgba(255,165,0,0.3);">
                            ⭐ رئيسي
                        </span>
                        <?php else: ?>
                        <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <?php if($warehouse->is_active): ?>
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">نشط</span>
                        <?php else: ?>
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">غير نشط</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="<?php echo e(route('warehouses.show', $warehouse)); ?>" style="color: #0071e3; text-decoration: none; padding: 6px 10px; border-radius: 6px; transition: all 0.2s; display: inline-flex; align-items: center;" title="عرض">
                                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                            </a>
                            <a href="<?php echo e(route('warehouses.edit', $warehouse)); ?>" style="color: #28a745; text-decoration: none; padding: 6px 10px; border-radius: 6px; transition: all 0.2s; display: inline-flex; align-items: center;" title="تعديل">
                                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                            </a>
                            <form method="POST" action="<?php echo e(route('warehouses.destroy', $warehouse)); ?>" style="display: inline; margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستودع؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: all 0.2s; display: inline-flex; align-items: center;" title="حذف">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                        لا توجد مستودعات
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/warehouses/index.blade.php ENDPATH**/ ?>