<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px; color: #1d1d1f;">تعديل الدولة: <?php echo e($country->name); ?></h1>
    
    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-right: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo e(route('countries.update', $country)); ?>" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الاسم بالعربية *</label>
            <input type="text" name="name" value="<?php echo e(old('name', $country->name)); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الاسم بالإنجليزية *</label>
            <input type="text" name="name_en" value="<?php echo e(old('name_en', $country->name_en)); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الكود (حرفين) *</label>
                <input type="text" name="code" value="<?php echo e(old('code', $country->code)); ?>" required maxlength="2" placeholder="SA" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; text-transform: uppercase;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">رمز الهاتف *</label>
                <input type="text" name="phone_code" value="<?php echo e(old('phone_code', $country->phone_code)); ?>" required placeholder="+966" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">رمز العملة (3 أحرف)</label>
            <input type="text" name="currency_code" value="<?php echo e(old('currency_code', $country->currency_code)); ?>" maxlength="3" placeholder="SAR" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; text-transform: uppercase;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $country->is_active) ? 'checked' : ''); ?> style="width: 18px; height: 18px;">
                <span style="font-weight: 600; color: #1d1d1f;">الدولة نشطة</span>
            </label>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ التغييرات</button>
            <a href="<?php echo e(route('countries.index')); ?>" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/countries/edit.blade.php ENDPATH**/ ?>