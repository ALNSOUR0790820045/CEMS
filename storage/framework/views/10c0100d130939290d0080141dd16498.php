<?php $__env->startSection('content'); ?>
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">المستخلصات الرئيسية</h1>
            <p style="color: #86868b; font-size: 0.9rem;">إدارة المستخلصات - دورة الموافقة 6 مراحل</p>
        </div>
        <a href="<?php echo e(route('main-ipcs.create')); ?>" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            مستخلص جديد
        </a>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <div style="width: 40px; height: 40px; background: rgba(0, 113, 227, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="file-text" style="width: 20px; height: 20px; color: var(--accent);"></i>
                </div>
                <span style="color: #86868b; font-size: 0.85rem;">إجمالي المستخلصات</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 700;"><?php echo e($statistics['total_count']); ?></div>
            <div style="color: #86868b; font-size: 0.8rem; margin-top: 5px;"><?php echo e(number_format($statistics['total_value'], 2)); ?> ر.س</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <div style="width: 40px; height: 40px; background: rgba(255, 149, 0, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="clock" style="width: 20px; height: 20px; color: #ff9500;"></i>
                </div>
                <span style="color: #86868b; font-size: 0.85rem;">معلقة</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 700;"><?php echo e($statistics['pending_count']); ?></div>
            <div style="color: #86868b; font-size: 0.8rem; margin-top: 5px;">قيد المراجعة</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <div style="width: 40px; height: 40px; background: rgba(52, 199, 89, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #34c759;"></i>
                </div>
                <span style="color: #86868b; font-size: 0.85rem;">معتمدة</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 700;"><?php echo e($statistics['approved_count']); ?></div>
            <div style="color: #86868b; font-size: 0.8rem; margin-top: 5px;">جاهزة للدفع</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <div style="width: 40px; height: 40px; background: rgba(52, 199, 89, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="coins" style="width: 20px; height: 20px; color: #34c759;"></i>
                </div>
                <span style="color: #86868b; font-size: 0.85rem;">مدفوعة</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 700;"><?php echo e($statistics['paid_count']); ?></div>
            <div style="color: #86868b; font-size: 0.8rem; margin-top: 5px;"><?php echo e(number_format($statistics['paid_value'], 2)); ?> ر.س</div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <form method="GET" action="<?php echo e(route('main-ipcs.index')); ?>" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل المشاريع</option>
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($project->id); ?>" <?php echo e(request('project_id') == $project->id ? 'selected' : ''); ?>>
                            <?php echo e($project->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">كل الحالات</option>
                    <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                    <option value="pending_pm" <?php echo e(request('status') == 'pending_pm' ? 'selected' : ''); ?>>معلق - مدير المشروع</option>
                    <option value="pending_technical" <?php echo e(request('status') == 'pending_technical' ? 'selected' : ''); ?>>معلق - المدير الفني</option>
                    <option value="pending_consultant" <?php echo e(request('status') == 'pending_consultant' ? 'selected' : ''); ?>>معلق - الاستشاري</option>
                    <option value="pending_client" <?php echo e(request('status') == 'pending_client' ? 'selected' : ''); ?>>معلق - العميل</option>
                    <option value="pending_finance" <?php echo e(request('status') == 'pending_finance' ? 'selected' : ''); ?>>معلق - المالية</option>
                    <option value="approved_for_payment" <?php echo e(request('status') == 'approved_for_payment' ? 'selected' : ''); ?>>جاهز للدفع</option>
                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>تم الدفع</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">من تاريخ</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">إلى تاريخ</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: var(--accent); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    بحث
                </button>
                <a href="<?php echo e(route('main-ipcs.index')); ?>" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- IPCs Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 2px solid #e5e5e7;">
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">رقم IPC</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الفترة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">القيمة التراكمية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الصافي للدفع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">التقدم</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $ipcs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ipc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="border-bottom: 1px solid #e5e5e7;">
                        <td style="padding: 15px;">
                            <div style="font-weight: 600; color: var(--accent);"><?php echo e($ipc->ipc_number); ?></div>
                            <div style="font-size: 0.75rem; color: #86868b;">تسلسل: <?php echo e($ipc->ipc_sequence); ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 500;"><?php echo e($ipc->project->name); ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-size: 0.85rem;"><?php echo e($ipc->period_from->format('Y/m/d')); ?></div>
                            <div style="font-size: 0.75rem; color: #86868b;">إلى <?php echo e($ipc->period_to->format('Y/m/d')); ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 600;"><?php echo e(number_format($ipc->current_cumulative, 2)); ?> ر.س</div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 600; color: #34c759;"><?php echo e(number_format($ipc->net_payable, 2)); ?> ر.س</div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; 
                                background: rgba(<?php echo e($ipc->status_badge['color'] === 'green' ? '52, 199, 89' : ($ipc->status_badge['color'] === 'blue' ? '0, 113, 227' : ($ipc->status_badge['color'] === 'yellow' ? '255, 204, 0' : ($ipc->status_badge['color'] === 'red' ? '255, 59, 48' : '134, 134, 139')))); ?>, 0.1);
                                color: <?php echo e($ipc->status_badge['color'] === 'green' ? '#34c759' : ($ipc->status_badge['color'] === 'blue' ? '#0071e3' : ($ipc->status_badge['color'] === 'yellow' ? '#ffcc00' : ($ipc->status_badge['color'] === 'red' ? '#ff3b30' : '#86868b')))); ?>;">
                                <?php echo e($ipc->status_badge['label']); ?>

                            </span>
                            <?php if($ipc->is_overdue): ?>
                                <div style="color: #ff3b30; font-size: 0.75rem; margin-top: 5px;">
                                    <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i> متأخر
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                <div style="width: 100%; height: 6px; background: #e5e5e7; border-radius: 3px; overflow: hidden;">
                                    <div style="width: <?php echo e($ipc->approval_progress); ?>%; height: 100%; background: linear-gradient(90deg, #0071e3, #00c4cc); transition: width 0.3s;"></div>
                                </div>
                                <div style="font-size: 0.75rem; color: #86868b;"><?php echo e($ipc->approval_progress); ?>% مكتمل</div>
                            </div>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="<?php echo e(route('main-ipcs.show', $ipc)); ?>" style="padding: 6px 12px; background: rgba(0, 113, 227, 0.1); color: var(--accent); border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                    عرض
                                </a>
                                <?php if(in_array($ipc->status, ['pending_pm', 'pending_technical', 'pending_consultant', 'pending_client', 'pending_finance'])): ?>
                                    <a href="<?php echo e(route('main-ipcs.approve', $ipc)); ?>" style="padding: 6px 12px; background: rgba(52, 199, 89, 0.1); color: #34c759; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                        مراجعة
                                    </a>
                                <?php endif; ?>
                                <?php if($ipc->status === 'approved_for_payment'): ?>
                                    <a href="<?php echo e(route('main-ipcs.payment', $ipc)); ?>" style="padding: 6px 12px; background: rgba(255, 149, 0, 0.1); color: #ff9500; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                        دفع
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #86868b;">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
                            <div>لا توجد مستخلصات</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($ipcs->hasPages()): ?>
            <div style="padding: 20px; border-top: 1px solid #e5e5e7;">
                <?php echo e($ipcs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/main-ipcs/index.blade.php ENDPATH**/ ?>