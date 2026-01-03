<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">العملاء</h1>
        <a href="<?php echo e(route('clients.create')); ?>" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; margin-left: 5px;"></i>
            إضافة عميل جديد
        </a>
    </div>

    <?php if(session('success')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="<?php echo e(route('clients.index')); ?>" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث (الكود، الاسم، الرقم الضريبي...)" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <select name="client_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأنواع</option>
                    <option value="government" <?php echo e(request('client_type') == 'government' ? 'selected' : ''); ?>>حكومي</option>
                    <option value="semi_government" <?php echo e(request('client_type') == 'semi_government' ? 'selected' : ''); ?>>شبه حكومي</option>
                    <option value="private_sector" <?php echo e(request('client_type') == 'private_sector' ? 'selected' : ''); ?>>قطاع خاص</option>
                    <option value="individual" <?php echo e(request('client_type') == 'individual' ? 'selected' : ''); ?>>فرد</option>
                </select>
            </div>
            
            <div>
                <select name="client_category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الفئات</option>
                    <option value="strategic" <?php echo e(request('client_category') == 'strategic' ? 'selected' : ''); ?>>استراتيجي</option>
                    <option value="preferred" <?php echo e(request('client_category') == 'preferred' ? 'selected' : ''); ?>>مفضل</option>
                    <option value="regular" <?php echo e(request('client_category') == 'regular' ? 'selected' : ''); ?>>عادي</option>
                    <option value="one_time" <?php echo e(request('client_category') == 'one_time' ? 'selected' : ''); ?>>لمرة واحدة</option>
                </select>
            </div>
            
            <div>
                <select name="is_active" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="1" <?php echo e(request('is_active') == '1' ? 'selected' : ''); ?>>نشط</option>
                    <option value="0" <?php echo e(request('is_active') == '0' ? 'selected' : ''); ?>>غير نشط</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    بحث
                </button>
                <a href="<?php echo e(route('clients.index')); ?>" style="flex: 1; background: #f5f5f7; color: #666; padding: 10px; border: none; border-radius: 5px; text-decoration: none; text-align: center; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 1px solid #ddd;">
                    <th style="padding: 15px; text-align: right; font-weight: 600;">كود العميل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الفئة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">البريد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">التقييم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;"><?php echo e($client->client_code); ?></td>
                    <td style="padding: 15px; font-weight: 600;"><?php echo e($client->name); ?></td>
                    <td style="padding: 15px;">
                        <?php
                            $typeLabels = [
                                'government' => ['text' => 'حكومي', 'color' => '#0071e3'],
                                'semi_government' => ['text' => 'شبه حكومي', 'color' => '#5856d6'],
                                'private_sector' => ['text' => 'قطاع خاص', 'color' => '#34c759'],
                                'individual' => ['text' => 'فرد', 'color' => '#ff9500'],
                            ];
                            $type = $typeLabels[$client->client_type] ?? ['text' => $client->client_type, 'color' => '#999'];
                        ?>
                        <span style="background: <?php echo e($type['color']); ?>15; color: <?php echo e($type['color']); ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                            <?php echo e($type['text']); ?>

                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <?php
                            $categoryLabels = [
                                'strategic' => ['text' => 'استراتيجي', 'color' => '#ff2d55'],
                                'preferred' => ['text' => 'مفضل', 'color' => '#5856d6'],
                                'regular' => ['text' => 'عادي', 'color' => '#34c759'],
                                'one_time' => ['text' => 'لمرة واحدة', 'color' => '#999'],
                            ];
                            $category = $categoryLabels[$client->client_category] ?? ['text' => $client->client_category, 'color' => '#999'];
                        ?>
                        <span style="background: <?php echo e($category['color']); ?>15; color: <?php echo e($category['color']); ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                            <?php echo e($category['text']); ?>

                        </span>
                    </td>
                    <td style="padding: 15px;"><?php echo e($client->phone ?? '-'); ?></td>
                    <td style="padding: 15px;"><?php echo e($client->email ?? '-'); ?></td>
                    <td style="padding: 15px;">
                        <?php if($client->rating): ?>
                            <?php
                                $ratingStars = [
                                    'excellent' => '⭐⭐⭐⭐⭐',
                                    'good' => '⭐⭐⭐⭐',
                                    'average' => '⭐⭐⭐',
                                    'poor' => '⭐⭐',
                                ];
                            ?>
                            <span title="<?php echo e($client->rating); ?>"><?php echo e($ratingStars[$client->rating] ?? '-'); ?></span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if($client->is_active): ?>
                            <span style="background: #34c75915; color: #34c759; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">نشط</span>
                        <?php else: ?>
                            <span style="background: #ff2d5515; color: #ff2d55; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">غير نشط</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="<?php echo e(route('clients.show', $client)); ?>" title="عرض" style="color: #0071e3; text-decoration: none;">
                                <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                            </a>
                            <a href="<?php echo e(route('clients.edit', $client)); ?>" title="تعديل" style="color: #ff9500; text-decoration: none;">
                                <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                            </a>
                            <form method="POST" action="<?php echo e(route('clients.destroy', $client)); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" title="حذف" style="background: none; border: none; color: #ff2d55; cursor: pointer; padding: 0;">
                                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                        لا توجد عملاء
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($clients->hasPages()): ?>
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <?php echo e($clients->links()); ?>

    </div>
    <?php endif; ?>
</div>

<script>
    lucide.createIcons();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/clients/index.blade.php ENDPATH**/ ?>