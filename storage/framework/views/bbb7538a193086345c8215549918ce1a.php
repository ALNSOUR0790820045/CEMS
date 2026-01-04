<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المطالبة - <?php echo e($claim->claim_number); ?></title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            line-height: 1.6;
        }
        h1 {
            color: #0071e3;
            border-bottom: 3px solid #0071e3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            background: #f5f5f7;
            padding: 10px;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        table th {
            background: #f5f5f7;
            font-weight: bold;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 8px;
            background: #f5f5f7;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .section {
            margin-bottom: 30px;
        }
        .timeline-item {
            border-right: 3px solid #0071e3;
            padding-right: 15px;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        .timeline-date {
            color: #666;
            font-size: 0.9em;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-default {
            background: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <h1>تقرير المطالبة</h1>
    
    <div class="section">
        <h2>المعلومات الأساسية</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">رقم المطالبة</div>
                <div class="info-value"><?php echo e($claim->claim_number); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">العنوان</div>
                <div class="info-value"><?php echo e($claim->title); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">المشروع</div>
                <div class="info-value"><?php echo e($claim->project->name ?? '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">العقد</div>
                <div class="info-value"><?php echo e($claim->contract->title ?? '-'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">النوع</div>
                <div class="info-value"><?php echo e($claim->type_label); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">السبب</div>
                <div class="info-value"><?php echo e($claim->cause_label); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">الحالة</div>
                <div class="info-value"><?php echo e($claim->status_label); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">الأولوية</div>
                <div class="info-value">
                    <?php if($claim->priority == 'critical'): ?> حرجة
                    <?php elseif($claim->priority == 'high'): ?> عالية
                    <?php elseif($claim->priority == 'medium'): ?> متوسطة
                    <?php else: ?> منخفضة
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>الوصف والتفاصيل</h2>
        <p><strong>الوصف:</strong></p>
        <p><?php echo e($claim->description); ?></p>
        
        <?php if($claim->contractual_basis): ?>
        <p><strong>الأساس التعاقدي:</strong></p>
        <p><?php echo e($claim->contractual_basis); ?></p>
        <?php endif; ?>
        
        <?php if($claim->facts): ?>
        <p><strong>الوقائع:</strong></p>
        <p><?php echo e($claim->facts); ?></p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>القيم المالية والزمنية</h2>
        <table>
            <thead>
                <tr>
                    <th>البند</th>
                    <th>المبلغ (<?php echo e($claim->currency); ?>)</th>
                    <th>عدد الأيام</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>المطالب</strong></td>
                    <td><?php echo e(number_format($claim->claimed_amount, 2)); ?></td>
                    <td><?php echo e($claim->claimed_days); ?></td>
                </tr>
                <tr>
                    <td><strong>المقيم</strong></td>
                    <td><?php echo e(number_format($claim->assessed_amount, 2)); ?></td>
                    <td><?php echo e($claim->assessed_days); ?></td>
                </tr>
                <tr>
                    <td><strong>المعتمد</strong></td>
                    <td><?php echo e(number_format($claim->approved_amount, 2)); ?></td>
                    <td><?php echo e($claim->approved_days); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>التواريخ الهامة</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">بداية الحدث</div>
                <div class="info-value"><?php echo e($claim->event_start_date->format('Y-m-d')); ?></div>
            </div>
            <?php if($claim->event_end_date): ?>
            <div class="info-row">
                <div class="info-label">نهاية الحدث</div>
                <div class="info-value"><?php echo e($claim->event_end_date->format('Y-m-d')); ?></div>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="info-label">تاريخ الإشعار</div>
                <div class="info-value"><?php echo e($claim->notice_date->format('Y-m-d')); ?></div>
            </div>
            <?php if($claim->submission_date): ?>
            <div class="info-row">
                <div class="info-label">تاريخ التقديم</div>
                <div class="info-value"><?php echo e($claim->submission_date->format('Y-m-d')); ?></div>
            </div>
            <?php endif; ?>
            <?php if($claim->response_due_date): ?>
            <div class="info-row">
                <div class="info-label">تاريخ استحقاق الرد</div>
                <div class="info-value"><?php echo e($claim->response_due_date->format('Y-m-d')); ?></div>
            </div>
            <?php endif; ?>
            <?php if($claim->response_date): ?>
            <div class="info-row">
                <div class="info-label">تاريخ الرد</div>
                <div class="info-value"><?php echo e($claim->response_date->format('Y-m-d')); ?></div>
            </div>
            <?php endif; ?>
            <?php if($claim->resolution_date): ?>
            <div class="info-row">
                <div class="info-label">تاريخ التسوية</div>
                <div class="info-value"><?php echo e($claim->resolution_date->format('Y-m-d')); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($claim->timeline->count() > 0): ?>
    <div class="section">
        <h2>Timeline</h2>
        <?php $__currentLoopData = $claim->timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="timeline-item">
            <strong><?php echo e($event->action); ?></strong><br>
            <span class="timeline-date">
                <?php echo e($event->performedBy->name); ?> • <?php echo e($event->created_at->format('Y-m-d H:i')); ?>

            </span>
            <?php if($event->notes): ?>
            <p style="margin-top: 5px;"><?php echo e($event->notes); ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <?php if($claim->client_response): ?>
    <div class="section">
        <h2>رد العميل</h2>
        <p><?php echo e($claim->client_response); ?></p>
    </div>
    <?php endif; ?>

    <?php if($claim->resolution_notes): ?>
    <div class="section">
        <h2>ملاحظات التسوية</h2>
        <p><?php echo e($claim->resolution_notes); ?></p>
    </div>
    <?php endif; ?>

    <?php if($claim->lessons_learned): ?>
    <div class="section">
        <h2>الدروس المستفادة</h2>
        <p><?php echo e($claim->lessons_learned); ?></p>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>الفريق</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">أعده</div>
                <div class="info-value"><?php echo e($claim->preparedBy->name); ?></div>
            </div>
            <?php if($claim->reviewedBy): ?>
            <div class="info-row">
                <div class="info-label">راجعه</div>
                <div class="info-value"><?php echo e($claim->reviewedBy->name); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; color: #666; font-size: 0.9em;">
        <p>تم إنشاء هذا التقرير في <?php echo e(now()->format('Y-m-d H:i')); ?></p>
        <p>CEMS - نظام إدارة المشاريع الإنشائية</p>
    </div>
</body>
</html>
<?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/claims/report.blade.php ENDPATH**/ ?>