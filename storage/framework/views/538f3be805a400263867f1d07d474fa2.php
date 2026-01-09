<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة مدينة جديدة</h1>
    
    <?php if($errors->any()): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-right: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo e(route('cities.store')); ?>" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php echo csrf_field(); ?>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة *</label>
            <select name="country_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; background: white;">
                <option value="">اختر الدولة</option>
                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($country->id); ?>" <?php echo e(old('country_id') == $country->id ? 'selected' : ''); ?>>
                    <?php echo e($country->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المدينة *</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المدينة (بالإنجليزية)</label>
            <input type="text" name="name_en" value="<?php echo e(old('name_en')); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                المدينة نشطة
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="<?php echo e(route('cities.index')); ?>" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/cities/create.blade.php ENDPATH**/ ?>