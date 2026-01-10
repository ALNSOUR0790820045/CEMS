<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة حساب جديد</h1>
    
    <form method="POST" action="<?php echo e(route('accounts.store')); ?>" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <?php echo csrf_field(); ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود الحساب *</label>
                <input type="text" name="code" value="<?php echo e(old('code')); ?>" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                    placeholder="مثال: 1-1-001">
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحساب الأب</label>
                <select name="parent_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">بدون (حساب رئيسي)</option>
                    <?php $__currentLoopData = $parentAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($parent->id); ?>" <?php echo e(old('parent_id') == $parent->id ? 'selected' : ''); ?>>
                            <?php echo e($parent->code); ?> - <?php echo e($parent->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['parent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الحساب (عربي) *</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                placeholder="مثال: الصندوق">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الحساب (إنجليزي)</label>
            <input type="text" name="name_en" value="<?php echo e(old('name_en')); ?>" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                placeholder="Example: Cash">
            <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع الحساب *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر النوع</option>
                    <option value="asset" <?php echo e(old('type') == 'asset' ? 'selected' : ''); ?>>أصول (Assets)</option>
                    <option value="liability" <?php echo e(old('type') == 'liability' ? 'selected' : ''); ?>>خصوم (Liabilities)</option>
                    <option value="equity" <?php echo e(old('type') == 'equity' ? 'selected' : ''); ?>>حقوق ملكية (Equity)</option>
                    <option value="revenue" <?php echo e(old('type') == 'revenue' ? 'selected' : ''); ?>>إيرادات (Revenue)</option>
                    <option value="expense" <?php echo e(old('type') == 'expense' ? 'selected' : ''); ?>>مصروفات (Expenses)</option>
                </select>
                <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">طبيعة الحساب *</label>
                <select name="nature" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الطبيعة</option>
                    <option value="debit" <?php echo e(old('nature') == 'debit' ? 'selected' : ''); ?>>مدين (Debit)</option>
                    <option value="credit" <?php echo e(old('nature') == 'credit' ? 'selected' : ''); ?>>دائن (Credit)</option>
                </select>
                <?php $__errorArgs = ['nature'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرصيد الافتتاحي</label>
                <input type="number" name="opening_balance" value="<?php echo e(old('opening_balance', 0)); ?>" step="0.01"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <?php $__errorArgs = ['opening_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرصيد الحالي</label>
                <input type="number" name="current_balance" value="<?php echo e(old('current_balance', 0)); ?>" step="0.01"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <?php $__errorArgs = ['current_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
            <textarea name="description" rows="3" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;"
                placeholder="وصف اختياري للحساب"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span style="color: #dc3545; font-size: 0.85rem;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="margin-bottom: 20px; display: flex; gap: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_parent" value="1" <?php echo e(old('is_parent') ? 'checked' : ''); ?>

                    style="width: 18px; height: 18px; cursor: pointer;">
                <span>حساب أب (يحتوي على حسابات فرعية)</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                    style="width: 18px; height: 18px; cursor: pointer;">
                <span>الحساب نشط</span>
            </label>
        </div>

        <div style="padding-top: 20px; border-top: 1px solid #eee;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                حفظ
            </button>
            <a href="<?php echo e(route('accounts.index')); ?>" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; display: inline-block;">
                إلغاء
            </a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/accounts/create.blade.php ENDPATH**/ ?>