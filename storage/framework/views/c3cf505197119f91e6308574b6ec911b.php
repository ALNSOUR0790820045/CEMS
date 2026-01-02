

<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة شركة جديدة</h1>
    
    <form method="POST" action="<?php echo e(route('companies.store')); ?>" style="background: white; padding: 30px; border-radius: 10px;">
        <?php echo csrf_field(); ?>
        
        <div style="margin-bottom:  20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الشركة *</label>
            <input type="text" name="name" required style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius:  5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
            <input type="email" name="email" style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
            <input type="text" name="phone" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:  5px; font-family:  'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدينة</label>
            <input type="text" name="city" style="width: 100%; padding: 10px; border:  1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة *</label>
            <select name="country" required style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر الدولة</option>
                <option value="SA">السعودية</option>
                <option value="AE">الإمارات</option>
                <option value="KW">الكويت</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                الشركة نشطة
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border:  none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="<?php echo e(route('companies.index')); ?>" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\cems-erp\resources\views/companies/create.blade.php ENDPATH**/ ?>