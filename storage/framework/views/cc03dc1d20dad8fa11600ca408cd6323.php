<?php $__env->startSection('content'); ?>
<style>
    .page-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--accent);
        text-decoration: none;
        font-size: 0.9rem;
        margin-bottom: 16px;
        font-weight: 600;
    }
    
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .form-section {
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-label .required {
        color: #ff3b30;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d0d0d0;
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }
    
    textarea.form-input {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .btn-secondary {
        background: #f0f0f0;
        color: var(--text);
    }
    
    .btn-secondary:hover {
        background: #e0e0e0;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
    }
</style>

<div class="page-header">
    <a href="<?php echo e(route('contract-templates.show', $contractTemplate->id)); ?>" class="back-link">
        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        العودة
    </a>
    <h1 class="page-title">إنشاء عقد من قالب: <?php echo e($contractTemplate->name); ?></h1>
    <p style="color: #86868b; margin-top: 8px;">قم بتعبئة البيانات المطلوبة لإنشاء العقد</p>
</div>

<form action="<?php echo e(route('contract-templates.store-generated')); ?>" method="POST" class="form-container">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="template_id" value="<?php echo e($contractTemplate->id); ?>">
    
    <!-- معلومات العقد الأساسية -->
    <div class="form-section">
        <h3 class="section-title">
            <i data-lucide="file-text" style="width: 24px; height: 24px; color: var(--accent);"></i>
            معلومات العقد
        </h3>
        
        <div class="form-group">
            <label class="form-label">عنوان العقد <span class="required">*</span></label>
            <input type="text" name="contract_title" class="form-input" required>
        </div>
    </div>
    
    <!-- أطراف العقد -->
    <div class="form-section">
        <h3 class="section-title">
            <i data-lucide="users" style="width: 24px; height: 24px; color: var(--accent);"></i>
            أطراف العقد
        </h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">الطرف الأول (صاحب العمل) <span class="required">*</span></label>
                <input type="text" name="parties[employer_name]" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">الطرف الثاني (المقاول) <span class="required">*</span></label>
                <input type="text" name="parties[contractor_name]" class="form-input" required>
            </div>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">عنوان صاحب العمل</label>
                <input type="text" name="parties[employer_address]" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">عنوان المقاول</label>
                <input type="text" name="parties[contractor_address]" class="form-input">
            </div>
        </div>
    </div>
    
    <!-- المتغيرات -->
    <?php if($contractTemplate->variables->count() > 0): ?>
    <div class="form-section">
        <h3 class="section-title">
            <i data-lucide="database" style="width: 24px; height: 24px; color: var(--accent);"></i>
            البيانات المطلوبة
        </h3>
        
        <div class="form-grid">
            <?php $__currentLoopData = $contractTemplate->variables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group">
                <label class="form-label">
                    <?php echo e($variable->variable_label); ?>

                    <?php if($variable->is_required): ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                </label>
                
                <?php if($variable->data_type === 'text'): ?>
                    <input type="text" 
                           name="filled_data[<?php echo e($variable->variable_key); ?>]" 
                           class="form-input"
                           <?php if($variable->is_required): ?> required <?php endif; ?>
                           value="<?php echo e($variable->default_value); ?>">
                <?php elseif($variable->data_type === 'number'): ?>
                    <input type="number" 
                           name="filled_data[<?php echo e($variable->variable_key); ?>]" 
                           class="form-input"
                           <?php if($variable->is_required): ?> required <?php endif; ?>
                           value="<?php echo e($variable->default_value); ?>">
                <?php elseif($variable->data_type === 'date'): ?>
                    <input type="date" 
                           name="filled_data[<?php echo e($variable->variable_key); ?>]" 
                           class="form-input"
                           <?php if($variable->is_required): ?> required <?php endif; ?>
                           value="<?php echo e($variable->default_value); ?>">
                <?php elseif($variable->data_type === 'currency'): ?>
                    <input type="number" 
                           step="0.01"
                           name="filled_data[<?php echo e($variable->variable_key); ?>]" 
                           class="form-input"
                           <?php if($variable->is_required): ?> required <?php endif; ?>
                           value="<?php echo e($variable->default_value); ?>"
                           placeholder="0.00">
                <?php elseif($variable->data_type === 'percentage'): ?>
                    <input type="number" 
                           step="0.01"
                           min="0"
                           max="100"
                           name="filled_data[<?php echo e($variable->variable_key); ?>]" 
                           class="form-input"
                           <?php if($variable->is_required): ?> required <?php endif; ?>
                           value="<?php echo e($variable->default_value); ?>"
                           placeholder="%">
                <?php endif; ?>
                
                <?php if($variable->description): ?>
                    <small style="color: #86868b; font-size: 0.85rem; margin-top: 4px; display: block;">
                        <?php echo e($variable->description); ?>

                    </small>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- الإجراءات -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
            إنشاء العقد
        </button>
        <a href="<?php echo e(route('contract-templates.show', $contractTemplate->id)); ?>" class="btn btn-secondary">
            <i data-lucide="x" style="width: 18px; height: 18px;"></i>
            إلغاء
        </a>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/contract-templates/generate.blade.php ENDPATH**/ ?>