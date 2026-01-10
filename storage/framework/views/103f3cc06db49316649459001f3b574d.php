<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0 0 5px 0;"><?php echo e($project->name); ?></h1>
            <p style="margin: 0; color: #666;"><?php echo e($project->project_number); ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('projects.edit', $project)); ?>" 
               style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                تعديل
            </a>
            <a href="<?php echo e(route('projects.index')); ?>" 
               style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                العودة للقائمة
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">نسبة الإنجاز</p>
            <h2 style="margin: 0; color: #0071e3;"><?php echo e(number_format($project->physical_progress, 1)); ?>%</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">قيمة المشروع</p>
            <h2 style="margin: 0; color: #0071e3;"><?php echo e(number_format($project->revised_contract_value, 0)); ?></h2>
            <p style="margin: 0; font-size: 12px; color: #999;"><?php echo e($project->currency); ?></p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الحالة</p>
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
                         <?php if($project->status == 'in_progress'): ?> background: #d4edda; color: #155724;
                         <?php elseif($project->status == 'not_started'): ?> background: #f8f9fa; color: #6c757d;
                         <?php elseif($project->status == 'completed'): ?> background: #d1ecf1; color: #0c5460;
                         <?php else: ?> background: #fff3cd; color: #856404;
                         <?php endif; ?>">
                <?php switch($project->status):
                    case ('not_started'): ?> لم يبدأ <?php break; ?>
                    <?php case ('mobilization'): ?> تجهيز الموقع <?php break; ?>
                    <?php case ('in_progress'): ?> قيد التنفيذ <?php break; ?>
                    <?php case ('completed'): ?> منتهي <?php break; ?>
                    <?php default: ?> <?php echo e($project->status); ?>

                <?php endswitch; ?>
            </span>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الصحة</p>
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
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
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- Project Info -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; padding-bottom: 15px; border-bottom: 2px solid #0071e3;">معلومات المشروع</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">العميل</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;"><?php echo e($project->client->name); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">الموقع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;"><?php echo e($project->city); ?>, <?php echo e($project->country); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">تاريخ البدء</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;"><?php echo e($project->commencement_date->format('Y-m-d')); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">تاريخ الانتهاء المخطط</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;"><?php echo e($project->original_completion_date->format('Y-m-d')); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">النوع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            <?php switch($project->type):
                                case ('building'): ?> مباني <?php break; ?>
                                <?php case ('infrastructure'): ?> بنية تحتية <?php break; ?>
                                <?php case ('industrial'): ?> صناعي <?php break; ?>
                                <?php case ('maintenance'): ?> صيانة <?php break; ?>
                                <?php case ('fit_out'): ?> تشطيبات <?php break; ?>
                                <?php default: ?> <?php echo e($project->type); ?>

                            <?php endswitch; ?>
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">التصنيف</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            <?php switch($project->category):
                                case ('new_construction'): ?> إنشاء جديد <?php break; ?>
                                <?php case ('renovation'): ?> تجديد <?php break; ?>
                                <?php case ('expansion'): ?> توسعة <?php break; ?>
                                <?php case ('maintenance'): ?> صيانة <?php break; ?>
                                <?php default: ?> <?php echo e($project->category); ?>

                            <?php endswitch; ?>
                        </p>
                    </div>
                </div>

                <?php if($project->description): ?>
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                        <p style="margin: 0; color: #666; font-size: 14px;">الوصف</p>
                        <p style="margin: 10px 0 0 0;"><?php echo e($project->description); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Team -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">الفريق</h3>
                    <a href="<?php echo e(route('projects.team', $project)); ?>" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        عرض الكل →
                    </a>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <?php if($project->projectManager): ?>
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مدير المشروع</p>
                            <p style="margin: 0; font-weight: 600;"><?php echo e($project->projectManager->name); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($project->siteEngineer): ?>
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مهندس الموقع</p>
                            <p style="margin: 0; font-weight: 600;"><?php echo e($project->siteEngineer->name); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Progress Reports -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">آخر تقارير التقدم</h3>
                    <a href="<?php echo e(route('projects.progress', $project)); ?>" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        عرض الكل →
                    </a>
                </div>
                
                <?php $__empty_1 = true; $__currentLoopData = $project->progressReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <p style="margin: 0; font-weight: 600;">تقرير #<?php echo e($report->report_number); ?></p>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;"><?php echo e($report->report_date->format('Y-m-d')); ?></p>
                            </div>
                            <span style="padding: 4px 12px; background: white; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                <?php echo e(number_format($report->physical_progress, 1)); ?>%
                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p style="text-align: center; color: #666; padding: 20px;">لا توجد تقارير تقدم حتى الآن</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0;">إجراءات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?php echo e(route('projects.progress', $project)); ?>" 
                       style="padding: 12px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        إضافة تقرير تقدم
                    </a>
                    <a href="<?php echo e(route('projects.milestones', $project)); ?>" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        المعالم الرئيسية
                    </a>
                    <a href="<?php echo e(route('projects.issues', $project)); ?>" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        المشاكل
                    </a>
                    <a href="<?php echo e(route('projects.team', $project)); ?>" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        الفريق
                    </a>
                </div>
            </div>

            <!-- Milestones -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">المعالم القادمة</h3>
                    <a href="<?php echo e(route('projects.milestones', $project)); ?>" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        الكل →
                    </a>
                </div>
                
                <?php $__empty_1 = true; $__currentLoopData = $project->milestones->where('status', 'pending')->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $milestone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <p style="margin: 0; font-weight: 600; font-size: 14px;"><?php echo e($milestone->name); ?></p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;"><?php echo e($milestone->target_date->format('Y-m-d')); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p style="text-align: center; color: #666; font-size: 14px; padding: 15px;">لا توجد معالم</p>
                <?php endif; ?>
            </div>

            <!-- Open Issues -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">المشاكل المفتوحة</h3>
                    <span style="padding: 4px 10px; background: #f8d7da; color: #721c24; border-radius: 12px; font-size: 13px; font-weight: 600;">
                        <?php echo e($project->issues->whereIn('status', ['open', 'in_progress'])->count()); ?>

                    </span>
                </div>
                
                <?php $__empty_1 = true; $__currentLoopData = $project->issues->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <p style="margin: 0; font-weight: 600; font-size: 14px;"><?php echo e($issue->title); ?></p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;"><?php echo e($issue->identified_date->format('Y-m-d')); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p style="text-align: center; color: #666; font-size: 14px; padding: 15px;">لا توجد مشاكل</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/projects/show.blade.php ENDPATH**/ ?>