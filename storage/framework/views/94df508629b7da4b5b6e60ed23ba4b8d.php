<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إدارة الدول</h1>
            <p style="color: #86868b;">عرض وإدارة جميع الدول في النظام</p>
        </div>
        <a href="<?php echo e(route('countries.create')); ?>" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة دولة جديدة
        </a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="<?php echo e(route('countries.index')); ?>" style="display: flex; gap: 10px; align-items: center;">
            <div style="flex: 1;">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث عن دولة..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">بحث</button>
            <?php if(request('search')): ?>
            <a href="<?php echo e(route('countries.index')); ?>" style="background: #86868b; color: white; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">مسح</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Countries Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php if($countries->count() > 0): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الكود</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">كود الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">العملة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px; font-weight: 700; color: #0071e3;"><?php echo e($country->code); ?></td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600; color: #1d1d1f;"><?php echo e($country->name); ?></div>
                        <div style="font-size: 0.85rem; color: #86868b;"><?php echo e($country->name_en); ?></div>
                    </td>
                    <td style="padding: 15px; color: #86868b; direction: ltr; text-align: right;"><?php echo e($country->phone_code); ?></td>
                    <td style="padding: 15px; color: #86868b;"><?php echo e($country->currency_code ?? '-'); ?></td>
                    <td style="padding: 15px;">
                        <?php if($country->is_active): ?>
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">نشط</span>
                        <?php else: ?>
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">غير نشط</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="<?php echo e(route('countries.edit', $country)); ?>" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            <form method="POST" action="<?php echo e(route('countries.destroy', $country)); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدولة؟');">
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
            <i data-lucide="globe" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد دول</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة دولة جديدة</p>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/countries/index.blade.php ENDPATH**/ ?>