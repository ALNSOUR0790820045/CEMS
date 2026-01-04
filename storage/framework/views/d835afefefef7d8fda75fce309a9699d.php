<?php $__env->startSection('content'); ?>
<style>
    .page-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    
    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 24px;
    }
    
    .template-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
        border: 1px solid var(--border);
    }
    
    .template-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 16px;
    }
    
    .template-code {
        display: inline-block;
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    .template-type {
        display: inline-block;
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .template-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
        margin: 12px 0 8px 0;
    }
    
    .template-desc {
        color: #86868b;
        font-size: 0.9rem;
        margin-bottom: 16px;
        line-height: 1.6;
    }
    
    .template-meta {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        color: #86868b;
    }
    
    .template-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
        flex: 1;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
    }
    
    .btn-secondary:hover {
        background: rgba(0, 113, 227, 0.2);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }
    
    .empty-state i {
        width: 64px;
        height: 64px;
        color: #86868b;
        margin-bottom: 16px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">قوالب العقود</h1>
    <p style="color: #86868b; margin-top: 8px;">إدارة قوالب عقود نقابة المقاولين الأردنيين و FIDIC</p>
</div>

<?php if($templates->count() > 0): ?>
    <div class="templates-grid">
        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="template-card">
            <div class="template-header">
                <span class="template-code"><?php echo e($template->code); ?></span>
                <span class="template-type"><?php echo e(ucfirst(str_replace('_', ' ', $template->type))); ?></span>
            </div>
            
            <h3 class="template-name"><?php echo e($template->name); ?></h3>
            
            <?php if($template->description): ?>
                <p class="template-desc"><?php echo e(Str::limit($template->description, 100)); ?></p>
            <?php endif; ?>
            
            <div class="template-meta">
                <?php if($template->version): ?>
                    <div class="meta-item">
                        <i data-lucide="git-branch" style="width: 14px; height: 14px;"></i>
                        <span>الإصدار <?php echo e($template->version); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if($template->year): ?>
                    <div class="meta-item">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                        <span><?php echo e($template->year); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="meta-item">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                    <span><?php echo e($template->clauses->count()); ?> بند</span>
                </div>
            </div>
            
            <div class="template-actions">
                <a href="<?php echo e(route('contract-templates.show', $template->id)); ?>" class="btn btn-secondary">
                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                    عرض
                </a>
                <a href="<?php echo e(route('contract-templates.generate', $template->id)); ?>" class="btn btn-primary">
                    <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                    إنشاء عقد
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i data-lucide="file-text"></i>
        <h3 style="color: var(--text); margin-bottom: 8px;">لا توجد قوالب عقود</h3>
        <p style="color: #86868b;">قم بإضافة قوالب العقود لتبدأ</p>
    </div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/contract-templates/index.blade.php ENDPATH**/ ?>