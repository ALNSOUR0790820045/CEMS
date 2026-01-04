<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">إدارة المطالبات</h1>
        <a href="<?php echo e(route('claims.create')); ?>" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            إضافة مطالبة جديدة
        </a>
    </div>

    <?php if(session('success')): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">رقم المطالبة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">العنوان</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المبلغ المطالب</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $claims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $claim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?php echo e($claim->claim_number); ?></td>
                    <td style="padding: 15px;"><?php echo e($claim->title); ?></td>
                    <td style="padding: 15px;"><?php echo e($claim->project->name ?? '-'); ?></td>
                    <td style="padding: 15px;">
                        <span style="padding: 4px 12px; background: #e3f2fd; color: #1976d2; border-radius: 12px; font-size: 0.85rem;">
                            <?php echo e($claim->type_label); ?>

                        </span>
                    </td>
                    <td style="padding: 15px;"><?php echo e(number_format($claim->claimed_amount, 2)); ?> <?php echo e($claim->currency); ?></td>
                    <td style="padding: 15px;">
                        <span style="padding: 4px 12px; 
                            <?php if($claim->status == 'approved' || $claim->status == 'settled'): ?> background: #d4edda; color: #155724;
                            <?php elseif($claim->status == 'rejected'): ?> background: #f8d7da; color: #721c24;
                            <?php elseif($claim->status == 'submitted' || $claim->status == 'under_review'): ?> background: #fff3cd; color: #856404;
                            <?php else: ?> background: #e2e3e5; color: #383d41;
                            <?php endif; ?>
                            border-radius: 12px; font-size: 0.85rem;">
                            <?php echo e($claim->status_label); ?>

                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <a href="<?php echo e(route('claims.show', $claim)); ?>" style="color: #0071e3; text-decoration: none; margin-left: 10px;">
                            <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                        </a>
                        <a href="<?php echo e(route('claims.edit', $claim)); ?>" style="color: #0071e3; text-decoration: none; margin-left: 10px;">
                            <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                        </a>
                        <form method="POST" action="<?php echo e(route('claims.destroy', $claim)); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer;">
                                <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد مطالبات حالياً
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($claims->hasPages()): ?>
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <?php echo e($claims->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/claims/index.blade.php ENDPATH**/ ?>