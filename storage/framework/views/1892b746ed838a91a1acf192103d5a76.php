<?php $__env->startSection('content'); ?>
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0;"><?php echo e($client->name); ?></h1>
            <p style="margin: 5px 0 0 0; color: #666;"><?php echo e($client->client_code); ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('clients.edit', $client)); ?>" style="background: #ff9500; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="edit" style="width: 18px; height: 18px; margin-left: 5px;"></i>
                تعديل
            </a>
            <a href="<?php echo e(route('clients.index')); ?>" style="background: #f5f5f7; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                رجوع
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">النوع</div>
            <?php
                $typeLabels = [
                    'government' => ['text' => 'حكومي', 'color' => '#0071e3'],
                    'semi_government' => ['text' => 'شبه حكومي', 'color' => '#5856d6'],
                    'private_sector' => ['text' => 'قطاع خاص', 'color' => '#34c759'],
                    'individual' => ['text' => 'فرد', 'color' => '#ff9500'],
                ];
                $type = $typeLabels[$client->client_type] ?? ['text' => $client->client_type, 'color' => '#999'];
            ?>
            <div style="font-weight: 600; font-size: 1.1rem; color: <?php echo e($type['color']); ?>;"><?php echo e($type['text']); ?></div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">الفئة</div>
            <?php
                $categoryLabels = [
                    'strategic' => ['text' => 'استراتيجي', 'color' => '#ff2d55'],
                    'preferred' => ['text' => 'مفضل', 'color' => '#5856d6'],
                    'regular' => ['text' => 'عادي', 'color' => '#34c759'],
                    'one_time' => ['text' => 'لمرة واحدة', 'color' => '#999'],
                ];
                $category = $categoryLabels[$client->client_category] ?? ['text' => $client->client_category, 'color' => '#999'];
            ?>
            <div style="font-weight: 600; font-size: 1.1rem; color: <?php echo e($category['color']); ?>;"><?php echo e($category['text']); ?></div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">التقييم</div>
            <?php if($client->rating): ?>
                <?php
                    $ratingStars = [
                        'excellent' => '⭐⭐⭐⭐⭐',
                        'good' => '⭐⭐⭐⭐',
                        'average' => '⭐⭐⭐',
                        'poor' => '⭐⭐',
                    ];
                ?>
                <div style="font-size: 1.2rem;"><?php echo e($ratingStars[$client->rating] ?? '-'); ?></div>
            <?php else: ?>
                <div style="color: #999;">غير مصنف</div>
            <?php endif; ?>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px;">
            <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">الحالة</div>
            <?php if($client->is_active): ?>
                <div style="font-weight: 600; font-size: 1.1rem; color: #34c759;">نشط</div>
            <?php else: ?>
                <div style="font-weight: 600; font-size: 1.1rem; color: #ff2d55;">غير نشط</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabs -->
    <div style="display: flex; gap: 10px; border-bottom: 2px solid #f0f0f0; margin-bottom: 20px;">
        <button type="button" class="tab-btn active" data-tab="overview" style="padding: 12px 24px; background: none; border: none; border-bottom: 2px solid #0071e3; color: #0071e3; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer; margin-bottom: -2px;">
            نظرة عامة
        </button>
        <button type="button" class="tab-btn" data-tab="contacts" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
            جهات الاتصال (<?php echo e($client->contacts->count()); ?>)
        </button>
        <button type="button" class="tab-btn" data-tab="bank_accounts" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
            الحسابات البنكية (<?php echo e($client->bankAccounts->count()); ?>)
        </button>
        <button type="button" class="tab-btn" data-tab="documents" style="padding: 12px 24px; background: none; border: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer;">
            المستندات (<?php echo e($client->documents->count()); ?>)
        </button>
    </div>

    <!-- Tab: Overview -->
    <div class="tab-content active" data-tab="overview">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Basic Information -->
            <div style="background: white; padding: 25px; border-radius: 10px;">
                <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">المعلومات الأساسية</h2>
                <div style="display: grid; gap: 15px;">
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الاسم (عربي)</div>
                        <div style="font-weight: 600;"><?php echo e($client->name); ?></div>
                    </div>
                    <?php if($client->name_en): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الاسم (English)</div>
                        <div style="font-weight: 600;"><?php echo e($client->name_en); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->tax_number): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الرقم الضريبي</div>
                        <div style="font-weight: 600;"><?php echo e($client->tax_number); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->commercial_registration): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">السجل التجاري</div>
                        <div style="font-weight: 600;"><?php echo e($client->commercial_registration); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Information -->
            <div style="background: white; padding: 25px; border-radius: 10px;">
                <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">معلومات الاتصال</h2>
                <div style="display: grid; gap: 15px;">
                    <?php if($client->phone): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الهاتف</div>
                        <div style="font-weight: 600;">
                            <a href="tel:<?php echo e($client->phone); ?>" style="color: #0071e3; text-decoration: none;"><?php echo e($client->phone); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->mobile): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الجوال</div>
                        <div style="font-weight: 600;">
                            <a href="tel:<?php echo e($client->mobile); ?>" style="color: #0071e3; text-decoration: none;"><?php echo e($client->mobile); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->email): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">البريد الإلكتروني</div>
                        <div style="font-weight: 600;">
                            <a href="mailto:<?php echo e($client->email); ?>" style="color: #0071e3; text-decoration: none;"><?php echo e($client->email); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->website): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الموقع الإلكتروني</div>
                        <div style="font-weight: 600;">
                            <a href="<?php echo e($client->website); ?>" target="_blank" style="color: #0071e3; text-decoration: none;"><?php echo e($client->website); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Address Information -->
            <?php if($client->address || $client->city || $client->country): ?>
            <div style="background: white; padding: 25px; border-radius: 10px;">
                <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">العنوان</h2>
                <div style="display: grid; gap: 15px;">
                    <?php if($client->address): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">العنوان</div>
                        <div><?php echo e($client->address); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->city || $client->country): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">المدينة / الدولة</div>
                        <div style="font-weight: 600;"><?php echo e($client->city ?? ''); ?><?php echo e($client->city && $client->country ? ', ' : ''); ?><?php echo e($client->country ?? ''); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Financial Information -->
            <div style="background: white; padding: 25px; border-radius: 10px;">
                <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">المعلومات المالية</h2>
                <div style="display: grid; gap: 15px;">
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">شروط الدفع</div>
                        <div style="font-weight: 600;">
                            <?php
                                $paymentTerms = [
                                    'immediate' => 'فوري',
                                    '7_days' => '7 أيام',
                                    '15_days' => '15 يوم',
                                    '30_days' => '30 يوم',
                                    '45_days' => '45 يوم',
                                    '60_days' => '60 يوم',
                                    '90_days' => '90 يوم',
                                    'custom' => 'مخصص',
                                ];
                            ?>
                            <?php echo e($paymentTerms[$client->payment_terms] ?? $client->payment_terms); ?>

                        </div>
                    </div>
                    <?php if($client->credit_limit): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">الحد الائتماني</div>
                        <div style="font-weight: 600;"><?php echo e(number_format($client->credit_limit, 2)); ?> <?php echo e($client->currency); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($client->currency): ?>
                    <div>
                        <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">العملة</div>
                        <div style="font-weight: 600;"><?php echo e($client->currency); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if($client->notes): ?>
            <div style="background: white; padding: 25px; border-radius: 10px; grid-column: 1 / -1;">
                <h2 style="margin: 0 0 15px 0; font-size: 1.2rem;">ملاحظات</h2>
                <div style="color: #666;"><?php echo e($client->notes); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Contacts -->
    <div class="tab-content" data-tab="contacts" style="display: none;">
        <div style="background: white; padding: 25px; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 1.2rem;">جهات الاتصال</h2>
                <button onclick="showAddContactModal()" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-left: 5px;"></i>
                    إضافة جهة اتصال
                </button>
            </div>

            <?php if($client->contacts->count() > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f7; border-bottom: 1px solid #ddd;">
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الاسم الكامل</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">المسمى الوظيفي</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الجوال</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">البريد</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الحالة</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $client->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 12px;">
                            <?php echo e($contact->full_name); ?>

                            <?php if($contact->is_primary): ?>
                                <span style="background: #0071e315; color: #0071e3; padding: 2px 8px; border-radius: 8px; font-size: 0.75rem; margin-right: 5px;">أساسي</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;"><?php echo e($contact->job_title ?? '-'); ?></td>
                        <td style="padding: 12px;">
                            <a href="tel:<?php echo e($contact->mobile); ?>" style="color: #0071e3; text-decoration: none;"><?php echo e($contact->mobile); ?></a>
                        </td>
                        <td style="padding: 12px;">
                            <?php if($contact->email): ?>
                                <a href="mailto:<?php echo e($contact->email); ?>" style="color: #0071e3; text-decoration: none;"><?php echo e($contact->email); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <?php if($contact->is_active): ?>
                                <span style="color: #34c759;">●</span> نشط
                            <?php else: ?>
                                <span style="color: #ff2d55;">●</span> غير نشط
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <?php if(!$contact->is_primary): ?>
                                <form method="POST" action="<?php echo e(route('clients.contacts.primary', [$client, $contact])); ?>" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" title="تعيين كأساسي" style="background: none; border: none; color: #0071e3; cursor: pointer; padding: 0;">
                                        <i data-lucide="star" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('clients.contacts.destroy', [$client, $contact])); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف جهة الاتصال؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" title="حذف" style="background: none; border: none; color: #ff2d55; cursor: pointer; padding: 0;">
                                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="padding: 40px; text-align: center; color: #999;">
                لا توجد جهات اتصال
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Bank Accounts -->
    <div class="tab-content" data-tab="bank_accounts" style="display: none;">
        <div style="background: white; padding: 25px; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 1.2rem;">الحسابات البنكية</h2>
                <button onclick="showAddBankAccountModal()" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-left: 5px;"></i>
                    إضافة حساب بنكي
                </button>
            </div>

            <?php if($client->bankAccounts->count() > 0): ?>
            <div style="display: grid; gap: 15px;">
                <?php $__currentLoopData = $client->bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="border: 1px solid #f0f0f0; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 1.1rem; margin-bottom: 10px;">
                                <?php echo e($account->bank_name); ?>

                                <?php if($account->is_primary): ?>
                                    <span style="background: #0071e315; color: #0071e3; padding: 2px 8px; border-radius: 8px; font-size: 0.75rem; margin-right: 5px;">أساسي</span>
                                <?php endif; ?>
                            </div>
                            <div style="display: grid; gap: 8px; color: #666;">
                                <?php if($account->branch_name): ?>
                                <div>الفرع: <?php echo e($account->branch_name); ?></div>
                                <?php endif; ?>
                                <div>رقم الحساب: <strong><?php echo e($account->account_number); ?></strong></div>
                                <?php if($account->iban): ?>
                                <div>IBAN: <?php echo e($account->iban); ?></div>
                                <?php endif; ?>
                                <?php if($account->swift_code): ?>
                                <div>SWIFT: <?php echo e($account->swift_code); ?></div>
                                <?php endif; ?>
                                <div>العملة: <?php echo e($account->currency); ?></div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <?php if(!$account->is_primary): ?>
                            <form method="POST" action="<?php echo e(route('clients.bank-accounts.primary', [$client, $account])); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" title="تعيين كأساسي" style="background: none; border: none; color: #0071e3; cursor: pointer; padding: 0;">
                                    <i data-lucide="star" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('clients.bank-accounts.destroy', [$client, $account])); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف الحساب البنكي؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" title="حذف" style="background: none; border: none; color: #ff2d55; cursor: pointer; padding: 0;">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div style="padding: 40px; text-align: center; color: #999;">
                لا توجد حسابات بنكية
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Documents -->
    <div class="tab-content" data-tab="documents" style="display: none;">
        <div style="background: white; padding: 25px; border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 1.2rem;">المستندات</h2>
                <button onclick="showAddDocumentModal()" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    <i data-lucide="plus" style="width: 16px; height: 16px; margin-left: 5px;"></i>
                    رفع مستند
                </button>
            </div>

            <?php if($client->documents->count() > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f7; border-bottom: 1px solid #ddd;">
                        <th style="padding: 12px; text-align: right; font-weight: 600;">اسم المستند</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">النوع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">تاريخ الانتهاء</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الحجم</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $client->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 12px; font-weight: 600;"><?php echo e($document->document_name); ?></td>
                        <td style="padding: 12px;">
                            <?php
                                $docTypes = [
                                    'commercial_registration' => 'سجل تجاري',
                                    'tax_certificate' => 'شهادة ضريبية',
                                    'license' => 'ترخيص',
                                    'contract' => 'عقد',
                                    'id_copy' => 'نسخة هوية',
                                    'other' => 'أخرى',
                                ];
                            ?>
                            <?php echo e($docTypes[$document->document_type] ?? $document->document_type); ?>

                        </td>
                        <td style="padding: 12px;">
                            <?php if($document->expiry_date): ?>
                                <?php echo e($document->expiry_date->format('Y-m-d')); ?>

                                <?php if($document->is_expired): ?>
                                    <span style="color: #ff2d55; font-weight: 600;">(منتهي)</span>
                                <?php elseif($document->is_expiring_soon): ?>
                                    <span style="color: #ff9500; font-weight: 600;">(قريب الانتهاء)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;"><?php echo e(number_format($document->file_size / 1024, 2)); ?> KB</td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="<?php echo e(route('clients.documents.download', [$client, $document])); ?>" title="تحميل" style="color: #0071e3; text-decoration: none;">
                                    <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('clients.documents.destroy', [$client, $document])); ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف المستند؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" title="حذف" style="background: none; border: none; color: #ff2d55; cursor: pointer; padding: 0;">
                                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="padding: 40px; text-align: center; color: #999;">
                لا توجد مستندات
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal placeholders (simplified - would need proper modal implementation) -->
<div id="addContactModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%;">
        <h2 style="margin: 0 0 20px 0;">إضافة جهة اتصال</h2>
        <form method="POST" action="<?php echo e(route('clients.contacts.store', $client)); ?>">
            <?php echo csrf_field(); ?>
            <div style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم الكامل *</label>
                    <input type="text" name="full_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المسمى الوظيفي</label>
                    <input type="text" name="job_title" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجوال *</label>
                    <input type="text" name="mobile" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
                    <input type="email" name="email" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="is_primary" value="1">
                        جهة اتصال أساسية
                    </label>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
                    <button type="button" onclick="hideAddContactModal()" style="flex: 1; background: #f5f5f7; color: #666; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="addBankAccountModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%;">
        <h2 style="margin: 0 0 20px 0;">إضافة حساب بنكي</h2>
        <form method="POST" action="<?php echo e(route('clients.bank-accounts.store', $client)); ?>">
            <?php echo csrf_field(); ?>
            <div style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم البنك *</label>
                    <input type="text" name="bank_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الفرع</label>
                    <input type="text" name="branch_name" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم الحساب *</label>
                    <input type="text" name="account_number" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">IBAN</label>
                    <input type="text" name="iban" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة</label>
                    <input type="text" name="currency" value="JOD" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="is_primary" value="1">
                        حساب أساسي
                    </label>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
                    <button type="button" onclick="hideAddBankAccountModal()" style="flex: 1; background: #f5f5f7; color: #666; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="addDocumentModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%;">
        <h2 style="margin: 0 0 20px 0;">رفع مستند</h2>
        <form method="POST" action="<?php echo e(route('clients.documents.store', $client)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع المستند *</label>
                    <select name="document_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="commercial_registration">سجل تجاري</option>
                        <option value="tax_certificate">شهادة ضريبية</option>
                        <option value="license">ترخيص</option>
                        <option value="contract">عقد</option>
                        <option value="id_copy">نسخة هوية</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المستند *</label>
                    <input type="text" name="document_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الملف *</label>
                    <input type="file" name="file" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإصدار</label>
                    <input type="date" name="issue_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">رفع</button>
                    <button type="button" onclick="hideAddDocumentModal()" style="flex: 1; background: #f5f5f7; color: #666; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Update buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.borderBottom = 'none';
            b.style.color = '#666';
            b.classList.remove('active');
        });
        this.style.borderBottom = '2px solid #0071e3';
        this.style.color = '#0071e3';
        this.classList.add('active');
        
        // Update content
        document.querySelectorAll('.tab-content').forEach(c => {
            c.style.display = 'none';
            c.classList.remove('active');
        });
        const content = document.querySelector(`.tab-content[data-tab="${tab}"]`);
        content.style.display = 'block';
        content.classList.add('active');
    });
});

// Modal functions
function showAddContactModal() {
    document.getElementById('addContactModal').style.display = 'flex';
}
function hideAddContactModal() {
    document.getElementById('addContactModal').style.display = 'none';
}
function showAddBankAccountModal() {
    document.getElementById('addBankAccountModal').style.display = 'flex';
}
function hideAddBankAccountModal() {
    document.getElementById('addBankAccountModal').style.display = 'none';
}
function showAddDocumentModal() {
    document.getElementById('addDocumentModal').style.display = 'flex';
}
function hideAddDocumentModal() {
    document.getElementById('addDocumentModal').style.display = 'none';
}

lucide.createIcons();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/clients/show.blade.php ENDPATH**/ ?>