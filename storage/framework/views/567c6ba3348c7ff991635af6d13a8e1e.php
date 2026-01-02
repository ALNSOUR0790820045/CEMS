<?php $__env->startSection('content'); ?>
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-size: 2rem; font-weight: 700;">تفاصيل المستودع</h1>
                <a href="<?php echo e(route('warehouses.index')); ?>" style="color: #0071e3; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                    العودة للقائمة
                </a>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo e(route('warehouses.edit', $warehouse)); ?>" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                    تعديل
                </a>
            </div>
        </div>
    </div>

    <!-- Header Card -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h2 style="margin: 0 0 10px 0; font-size: 1.5rem; font-weight: 700;"><?php echo e($warehouse->name); ?></h2>
                <?php if($warehouse->name_en): ?>
                <p style="margin: 0 0 10px 0; color: #666;"><?php echo e($warehouse->name_en); ?></p>
                <?php endif; ?>
                <p style="margin: 0; color: #0071e3; font-weight: 600; font-size: 1.1rem;"><?php echo e($warehouse->code); ?></p>
            </div>
            <div style="display: flex; gap: 10px;">
                <?php if($warehouse->is_main): ?>
                <span style="background: linear-gradient(135deg, #FFD700, #FFA500); color: white; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; box-shadow: 0 2px 4px rgba(255,165,0,0.3); display: inline-flex; align-items: center; gap: 5px;">
                    ⭐ مستودع رئيسي
                </span>
                <?php endif; ?>
                <?php if($warehouse->is_active): ?>
                <span style="background: #d4edda; color: #155724; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 600;">نشط</span>
                <?php else: ?>
                <span style="background: #f8d7da; color: #721c24; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 600;">غير نشط</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div style="margin-bottom: 20px;">
        <div style="display: flex; gap: 5px; border-bottom: 2px solid #e5e5e7;">
            <button onclick="switchTab('info')" id="tab-info" class="tab-button active" style="background: #0071e3; color: white; padding: 12px 24px; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; transition: all 0.2s;">
                معلومات
            </button>
            <button onclick="switchTab('stock')" id="tab-stock" class="tab-button" style="background: white; color: #666; padding: 12px 24px; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; transition: all 0.2s;">
                مخزون
            </button>
            <button onclick="switchTab('movements')" id="tab-movements" class="tab-button" style="background: white; color: #666; padding: 12px 24px; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; transition: all 0.2s;">
                حركات
            </button>
        </div>
    </div>

    <!-- Tab Content: Info -->
    <div id="content-info" class="tab-content">
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; font-weight: 700; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">المعلومات الأساسية</h3>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">الشركة</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->company?->name ?? 'غير محدد'); ?></div>
                </div>

                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">الفرع</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->branch?->name ?? 'غير محدد'); ?></div>
                </div>

                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">المدير</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->manager?->name ?? 'غير محدد'); ?></div>
                </div>

                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">الهاتف</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->phone ?? 'غير محدد'); ?></div>
                </div>

                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">المدينة</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->city?->name ?? 'غير محدد'); ?></div>
                </div>

                <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">تاريخ الإضافة</div>
                    <div style="font-weight: 600;"><?php echo e($warehouse->created_at->format('Y-m-d H:i')); ?></div>
                </div>
            </div>

            <?php if($warehouse->address): ?>
            <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                <div style="font-size: 0.85rem; color: #666; margin-bottom: 5px;">العنوان</div>
                <div style="font-weight: 600;"><?php echo e($warehouse->address); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Content: Stock -->
    <div id="content-stock" class="tab-content" style="display: none;">
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; font-weight: 700; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">المخزون الحالي</h3>
            <div style="padding: 40px; text-align: center; color: #999;">
                <i data-lucide="package" style="width: 48px; height: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                <p>لا توجد بيانات مخزون حالياً</p>
            </div>
        </div>
    </div>

    <!-- Tab Content: Movements -->
    <div id="content-movements" class="tab-content" style="display: none;">
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.2rem; font-weight: 700; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">آخر الحركات</h3>
            
            <?php if($recentMovements->count() > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">النوع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">المرجع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">الملاحظات</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">بواسطة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #e5e5e7;">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom: 1px solid #e5e5e7;">
                        <td style="padding: 12px;">
                            <?php if($movement->type == 'in'): ?>
                            <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">إدخال</span>
                            <?php elseif($movement->type == 'out'): ?>
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">إخراج</span>
                            <?php else: ?>
                            <span style="background: #d1ecf1; color: #0c5460; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">تحويل</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;"><?php echo e($movement->reference ?? '-'); ?></td>
                        <td style="padding: 12px;"><?php echo e($movement->notes ?? '-'); ?></td>
                        <td style="padding: 12px;"><?php echo e($movement->creator?->name ?? '-'); ?></td>
                        <td style="padding: 12px;"><?php echo e($movement->created_at->format('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="padding: 40px; text-align: center; color: #999;">
                <i data-lucide="arrow-right-left" style="width: 48px; height: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                <p>لا توجد حركات مخزون</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();

    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.style.background = 'white';
            button.style.color = '#666';
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).style.display = 'block';

        // Add active class to selected button
        const activeButton = document.getElementById('tab-' + tabName);
        activeButton.style.background = '#0071e3';
        activeButton.style.color = 'white';

        // Reinitialize icons for the newly displayed content
        lucide.createIcons();
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/warehouses/show.blade.php ENDPATH**/ ?>