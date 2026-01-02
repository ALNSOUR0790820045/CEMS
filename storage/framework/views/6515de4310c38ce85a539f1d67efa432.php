<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">وحدات القياس</h1>
            <p style="color: #86868b;">عرض وإدارة جميع وحدات القياس في النظام</p>
        </div>
        <a href="<?php echo e(route('units.create')); ?>" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة وحدة جديدة
        </a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="<?php echo e(route('units.index')); ?>" style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="ابحث بالاسم أو الكود..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">النوع</label>
                <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأنواع</option>
                    <option value="weight" <?php echo e(request('type') == 'weight' ? 'selected' : ''); ?>>وزن</option>
                    <option value="length" <?php echo e(request('type') == 'length' ? 'selected' : ''); ?>>طول</option>
                    <option value="volume" <?php echo e(request('type') == 'volume' ? 'selected' : ''); ?>>حجم</option>
                    <option value="quantity" <?php echo e(request('type') == 'quantity' ? 'selected' : ''); ?>>كمية</option>
                </select>
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                بحث
            </button>
            <?php if(request('search') || request('type')): ?>
            <a href="<?php echo e(route('units.index')); ?>" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إعادة تعيين
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Units Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php if($units->count() > 0): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">#</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم الإنجليزي</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الكود</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;"><?php echo e($loop->iteration); ?></td>
                    <td style="padding: 15px; font-weight: 600;"><?php echo e($unit->name); ?></td>
                    <td style="padding: 15px; color: #86868b;"><?php echo e($unit->name_en ?? '-'); ?></td>
                    <td style="padding: 15px;">
                        <span style="background: #f5f5f7; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem;"><?php echo e($unit->code); ?></span>
                    </td>
                    <td style="padding: 15px;">
                        <?php if($unit->type === 'weight'): ?>
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">وزن</span>
                        <?php elseif($unit->type === 'length'): ?>
                        <span style="background: #fff3e0; color: #f57c00; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">طول</span>
                        <?php elseif($unit->type === 'volume'): ?>
                        <span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">حجم</span>
                        <?php else: ?>
                        <span style="background: #e8f5e9; color: #388e3c; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">كمية</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if($unit->is_active): ?>
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">نشط</span>
                        <?php else: ?>
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">غير نشط</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="<?php echo e(route('units.edit', $unit)); ?>" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            <form method="POST" action="<?php echo e(route('units.destroy', $unit)); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="ruler" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد وحدات قياس</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة وحدة قياس جديدة</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/units/index.blade.php ENDPATH**/ ?>