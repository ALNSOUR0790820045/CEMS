<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <h1 style="margin-bottom: 30px;">محفظة المشاريع</h1>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">إجمالي المشاريع</p>
            <h2 style="margin: 0; color: #0071e3;"><?php echo e($stats['total_projects']); ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">المشاريع النشطة</p>
            <h2 style="margin: 0; color: #28a745;"><?php echo e($stats['active_projects']); ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">المشاريع المكتملة</p>
            <h2 style="margin: 0; color: #6c757d;"><?php echo e($stats['completed_projects']); ?></h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">القيمة الإجمالية</p>
            <h2 style="margin: 0; color: #0071e3; font-size: 20px;"><?php echo e(number_format($stats['total_value'], 0)); ?></h2>
            <p style="margin: 0; font-size: 12px; color: #999;">SAR</p>
        </div>
    </div>

    <!-- Active Projects -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">المشاريع النشطة</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="margin-bottom: 15px;">
                        <h4 style="margin: 0 0 5px 0;"><?php echo e($project->name); ?></h4>
                        <p style="margin: 0; color: #666; font-size: 13px;"><?php echo e($project->project_number); ?></p>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px;">
                            <span>التقدم</span>
                            <span style="font-weight: 600;"><?php echo e(number_format($project->physical_progress, 1)); ?>%</span>
                        </div>
                        <div style="background: #e9ecef; height: 6px; border-radius: 3px; overflow: hidden;">
                            <div style="background: #0071e3; height: 100%; width: <?php echo e($project->physical_progress); ?>%;"></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #666; margin-bottom: 10px;">
                        <span><?php echo e($project->client->name); ?></span>
                        <span><?php echo e($project->city); ?></span>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <span style="padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
                                     <?php if($project->health == 'on_track'): ?> background: #d4edda; color: #155724;
                                     <?php elseif($project->health == 'at_risk'): ?> background: #fff3cd; color: #856404;
                                     <?php else: ?> background: #f8d7da; color: #721c24;
                                     <?php endif; ?>">
                            <?php switch($project->health):
                                case ('on_track'): ?> في المسار <?php break; ?>
                                <?php case ('at_risk'): ?> في خطر <?php break; ?>
                                <?php case ('delayed'): ?> متأخر <?php break; ?>
                                <?php case ('critical'): ?> حرج <?php break; ?>
                            <?php endswitch; ?>
                        </span>
                        <a href="<?php echo e(route('projects.show', $project)); ?>" 
                           style="padding: 4px 10px; background: #0071e3; color: white; border-radius: 12px; font-size: 11px; font-weight: 600; text-decoration: none;">
                            عرض
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="color: #666;">لا توجد مشاريع نشطة</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/projects/portfolio.blade.php ENDPATH**/ ?>