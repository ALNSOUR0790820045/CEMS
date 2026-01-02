<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">تعديل بيانات الشركة</h1>
        <p style="color: #86868b;">تحديث بيانات <?php echo e($company->name); ?></p>
    </div>

    <?php if($errors->any()): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="list-style: none; margin: 0; padding: 0;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>• <?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('companies.update', $company)); ?>" enctype="multipart/form-data" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                    اسم الشركة (عربي) <span style="color: #ff3b30;">*</span>
                </label>
                <input type="text" name="name" value="<?php echo e(old('name', $company->name)); ?>" required 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                    اسم الشركة (English)
                </label>
                <input type="text" name="name_en" value="<?php echo e(old('name_en', $company->name_en)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">البريد الإلكتروني</label>
                <input type="email" name="email" value="<?php echo e(old('email', $company->email)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">رقم الهاتف</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $company->phone)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">العنوان</label>
            <textarea name="address" rows="3" 
                style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif; resize: vertical;"><?php echo e(old('address', $company->address)); ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">المدينة</label>
                <input type="text" name="city" value="<?php echo e(old('city', $company->city)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                    الدولة <span style="color: #ff3b30;">*</span>
                </label>
                <select name="country" required 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر الدولة</option>
                    <option value="SA" <?php echo e(old('country', $company->country) == 'SA' ? 'selected' : ''); ?>>السعودية</option>
                    <option value="AE" <?php echo e(old('country', $company->country) == 'AE' ? 'selected' : ''); ?>>الإمارات</option>
                    <option value="KW" <?php echo e(old('country', $company->country) == 'KW' ? 'selected' : ''); ?>>الكويت</option>
                    <option value="QA" <?php echo e(old('country', $company->country) == 'QA' ? 'selected' : ''); ?>>قطر</option>
                    <option value="BH" <?php echo e(old('country', $company->country) == 'BH' ? 'selected' : ''); ?>>البحرين</option>
                    <option value="OM" <?php echo e(old('country', $company->country) == 'OM' ? 'selected' : ''); ?>>عُمان</option>
                    <option value="JO" <?php echo e(old('country', $company->country) == 'JO' ? 'selected' : ''); ?>>الأردن</option>
                    <option value="EG" <?php echo e(old('country', $company->country) == 'EG' ? 'selected' : ''); ?>>مصر</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">السجل التجاري</label>
                <input type="text" name="commercial_registration" value="<?php echo e(old('commercial_registration', $company->commercial_registration)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">الرقم الضريبي</label>
                <input type="text" name="tax_number" value="<?php echo e(old('tax_number', $company->tax_number)); ?>" 
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">شعار الشركة</label>
            
            <?php if($company->logo): ?>
            <div style="margin-bottom: 15px; padding: 15px; background: #f5f5f7; border-radius: 8px;">
                <p style="color: #1d1d1f; font-weight: 600; margin-bottom: 10px;">الشعار الحالي:</p>
                <img src="<?php echo e(asset('storage/' . $company->logo)); ?>" alt="Logo" 
                    style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 1px solid #d2d2d7;">
            </div>
            <?php endif; ?>
            
            <input type="file" name="logo" accept="image/*" 
                style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            <p style="color: #86868b; font-size: 0.85rem; margin-top: 5px;">يُقبل: jpeg, png, jpg, gif, svg - الحد الأقصى: 2MB</p>
            <?php if($company->logo): ?>
            <p style="color: #86868b; font-size: 0.85rem;">اترك الحقل فارغاً للاحتفاظ بالشعار الحالي</p>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $company->is_active) ? 'checked' : ''); ?> 
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: #1d1d1f; font-weight: 500;">الشركة نشطة</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="<?php echo e(route('companies.index')); ?>" 
                style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إلغاء
            </a>
            <button type="submit" 
                style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-family: 'Cairo', sans-serif;">
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/companies/edit.blade.php ENDPATH**/ ?>