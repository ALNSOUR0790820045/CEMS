<?php $__env->startSection('content'); ?>
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 30px;
    }

    .form-card {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 0.95rem;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: #f5f5f7;
        border-radius: 8px;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .checkbox-wrapper label {
        cursor: pointer;
        font-weight: 500;
        margin: 0;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #0071e3, #00a0e3);
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 113, 227, 0.2);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-cancel {
        padding: 14px 32px;
        text-decoration: none;
        color: #666;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s;
        background: #f5f5f7;
    }

    .btn-cancel:hover {
        background: #e8e8ea;
    }

    .error-message {
        color: #ff3b30;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .form-hint {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-container">
    <h1 class="page-title">تعديل العملة: <?php echo e($currency->name); ?></h1>

    <div class="form-card">
        <form method="POST" action="<?php echo e(route('currencies.update', $currency)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label required">الاسم</label>
                    <input type="text" name="name" class="form-input" 
                           value="<?php echo e(old('name', $currency->name)); ?>" required 
                           placeholder="مثال: ريال سعودي">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-message"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" class="form-input" 
                           value="<?php echo e(old('name_en', $currency->name_en)); ?>" 
                           placeholder="Example: Saudi Riyal">
                    <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-message"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label required">كود العملة</label>
                    <input type="text" name="code" class="form-input" 
                           value="<?php echo e(old('code', $currency->code)); ?>" required 
                           placeholder="SAR" maxlength="3" 
                           style="text-transform: uppercase;">
                    <div class="form-hint">كود العملة المكون من 3 أحرف (ISO 4217)</div>
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-message"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label required">رمز العملة</label>
                    <input type="text" name="symbol" class="form-input" 
                           value="<?php echo e(old('symbol', $currency->symbol)); ?>" required 
                           placeholder="ر.س" maxlength="10">
                    <?php $__errorArgs = ['symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-message"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">سعر الصرف</label>
                <input type="number" name="exchange_rate" class="form-input" 
                       value="<?php echo e(old('exchange_rate', $currency->exchange_rate)); ?>" 
                       step="0.000001" min="0" required>
                <div class="form-hint">سعر الصرف مقابل العملة الأساسية</div>
                <?php $__errorArgs = ['exchange_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="is_base" id="is_base" 
                           value="1" <?php echo e(old('is_base', $currency->is_base) ? 'checked' : ''); ?>>
                    <label for="is_base">عملة أساسية</label>
                </div>
                <div class="form-hint">العملة الأساسية هي العملة المرجعية لحساب أسعار الصرف</div>
                <?php $__errorArgs = ['is_base'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="is_active" id="is_active" 
                           value="1" <?php echo e(old('is_active', $currency->is_active) ? 'checked' : ''); ?>>
                    <label for="is_active">العملة نشطة</label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">تحديث</button>
                <a href="<?php echo e(route('currencies.index')); ?>" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/currencies/edit.blade.php ENDPATH**/ ?>