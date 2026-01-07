<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>المشاريع</h1>
        <a href="<?php echo e(route('projects.create')); ?>" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة مشروع جديد
        </a>
    </div>

    <?php if(session('success')): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;" 
                 onmouseover="this.style.transform='translateY(-4px)'" 
                 onmouseout="this.style.transform='translateY(0)'">
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0 0 5px 0; font-size: 18px;"><?php echo e($project->name); ?></h3>
                        <p style="margin: 0; color: #666; font-size: 14px;"><?php echo e($project->project_number); ?></p>
                    </div>
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                 <?php if($project->status == 'in_progress'): ?> background: #d4edda; color: #155724;
                                 <?php elseif($project->status == 'not_started'): ?> background: #f8f9fa; color: #6c757d;
                                 <?php elseif($project->status == 'completed'): ?> background: #d1ecf1; color: #0c5460;
                                 <?php else: ?> background: #fff3cd; color: #856404;
                                 <?php endif; ?>">
                        <?php switch($project->status):
                            case ('not_started'): ?> لم يبدأ <?php break; ?>
                            <?php case ('mobilization'): ?> تجهيز الموقع <?php break; ?>
                            <?php case ('in_progress'): ?> قيد التنفيذ <?php break; ?>
                            <?php case ('on_hold'): ?> متوقف <?php break; ?>
                            <?php case ('suspended'): ?> معلق <?php break; ?>
                            <?php case ('completed'): ?> منتهي <?php break; ?>
                            <?php case ('handed_over'): ?> تم التسليم <?php break; ?>
                            <?php case ('closed'): ?> مغلق <?php break; ?>
                            <?php default: ?> <?php echo e($project->status); ?>

                        <?php endswitch; ?>
                    </span>
                </div>

                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin: 0 0 5px 0; font-size: 14px; color: #666;">العميل</p>
                    <p style="margin: 0; font-weight: 600;"><?php echo e($project->client->name); ?></p>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 14px; color: #666;">نسبة الإنجاز</span>
                        <span style="font-weight: 600;"><?php echo e(number_format($project->physical_progress, 1)); ?>%</span>
                    </div>
                    <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: #0071e3; height: 100%; width: <?php echo e($project->physical_progress); ?>%; transition: width 0.3s;"></div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 12px; color: #666;">القيمة</p>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;"><?php echo e(number_format($project->revised_contract_value, 0)); ?> <?php echo e($project->currency); ?></p>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 12px; color: #666;">المدينة</p>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;"><?php echo e($project->city); ?></p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px;
                                 <?php if($project->health == 'on_track'): ?> background: #d4edda; color: #155724;
                                 <?php elseif($project->health == 'at_risk'): ?> background: #fff3cd; color: #856404;
                                 <?php elseif($project->health == 'delayed'): ?> background: #f8d7da; color: #721c24;
                                 <?php else: ?> background: #dc3545; color: white;
                                 <?php endif; ?>">
                        <?php switch($project->health):
                            case ('on_track'): ?> في المسار <?php break; ?>
                            <?php case ('at_risk'): ?> في خطر <?php break; ?>
                            <?php case ('delayed'): ?> متأخر <?php break; ?>
                            <?php case ('critical'): ?> حرج <?php break; ?>
                        <?php endswitch; ?>
                    </span>
                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; background: #e9ecef; color: #495057;">
                        <?php switch($project->priority):
                            case ('low'): ?> منخفضة <?php break; ?>
                            <?php case ('medium'): ?> متوسطة <?php break; ?>
                            <?php case ('high'): ?> عالية <?php break; ?>
                            <?php case ('critical'): ?> حرجة <?php break; ?>
                        <?php endswitch; ?>
                    </span>
                </div>

                <div style="display: flex; gap: 10px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                    <a href="<?php echo e(route('projects.show', $project)); ?>" 
                       style="flex: 1; text-align: center; background: #0071e3; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                        عرض
                    </a>
                    <a href="<?php echo e(route('projects.edit', $project)); ?>" 
                       style="flex: 1; text-align: center; background: #6c757d; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                        تعديل
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 12px;">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">لا توجد مشاريع حالياً</p>
                <a href="<?php echo e(route('projects.create')); ?>" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    إضافة مشروع جديد
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/projects/index.blade.php ENDPATH**/ ?>