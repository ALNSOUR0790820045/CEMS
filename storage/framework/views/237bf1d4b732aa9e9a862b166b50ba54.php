<div class="account-row" style="padding-right: <?php echo e($level * 30 + 15); ?>px;">
    <div style="font-family: 'Courier New', monospace; font-weight: 600; color: #0071e3;">
        <?php echo e($account->code); ?>

    </div>
    <div class="account-name">
        <?php if($account->is_parent): ?>
            <i data-lucide="folder" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: #f57c00;"></i>
        <?php else: ?>
            <i data-lucide="file-text" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: #666;"></i>
        <?php endif; ?>
        <?php echo e($account->name); ?>

        <?php if($account->name_en): ?>
            <span style="color: #999; font-size: 0.85rem;">(<?php echo e($account->name_en); ?>)</span>
        <?php endif; ?>
    </div>
    <div>
        <span class="badge badge-<?php echo e($account->type); ?>">
            <?php if($account->type === 'asset'): ?>
                أصول
            <?php elseif($account->type === 'liability'): ?>
                خصوم
            <?php elseif($account->type === 'equity'): ?>
                حقوق ملكية
            <?php elseif($account->type === 'revenue'): ?>
                إيرادات
            <?php else: ?>
                مصروفات
            <?php endif; ?>
        </span>
    </div>
    <div>
        <span class="badge badge-<?php echo e($account->nature); ?>">
            <?php echo e($account->nature === 'debit' ? 'مدين' : 'دائن'); ?>

        </span>
    </div>
    <div style="text-align: center; font-weight: 600;">
        <?php echo e($account->level); ?>

    </div>
    <div>
        <span class="badge badge-<?php echo e($account->is_active ? 'active' : 'inactive'); ?>">
            <?php echo e($account->is_active ? 'نشط' : 'غير نشط'); ?>

        </span>
    </div>
    <div style="text-align: center;">
        <a href="<?php echo e(route('accounts.show', $account->id)); ?>" class="action-btn" title="عرض">
            <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
        </a>
        <a href="<?php echo e(route('accounts.edit', $account->id)); ?>" class="action-btn" title="تعديل">
            <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
        </a>
        <form id="delete-form-<?php echo e($account->id); ?>" action="<?php echo e(route('accounts.destroy', $account->id)); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="button" class="action-btn delete" onclick="confirmDelete(<?php echo e($account->id); ?>, '<?php echo e($account->name); ?>')" title="حذف">
                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
            </button>
        </form>
    </div>
</div>

<?php if($account->children && $account->children->count() > 0): ?>
    <?php $__currentLoopData = $account->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('accounts.partials.tree-item', ['account' => $child, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/accounts/partials/tree-item.blade.php ENDPATH**/ ?>