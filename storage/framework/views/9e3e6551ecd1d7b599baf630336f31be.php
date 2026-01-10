<?php $__env->startSection('content'); ?>
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">إنشاء مستخلص جديد</h1>
        <p style="color: #86868b; font-size: 0.9rem;">Main IPC - دورة الموافقة 6 مراحل</p>
    </div>

    <form id="ipcForm" method="POST" action="<?php echo e(route('main-ipcs.store')); ?>" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <?php echo csrf_field(); ?>

        <!-- 1. Basic Information -->
        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">
                <i data-lucide="info" style="width: 20px; height: 20px; color: var(--accent);"></i>
                1. معلومات أساسية
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">المشروع *</label>
                    <select name="project_id" id="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($project->id); ?>"><?php echo e($project->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">من تاريخ *</label>
                    <input type="date" name="period_from" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">إلى تاريخ *</label>
                    <input type="date" name="period_to" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">تاريخ التقديم *</label>
                    <input type="date" name="submission_date" value="<?php echo e(date('Y-m-d')); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <!-- 2. BOQ Items -->
        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">
                <i data-lucide="list" style="width: 20px; height: 20px; color: var(--accent);"></i>
                2. بنود الأعمال
            </h3>

            <div style="margin-bottom: 15px;">
                <button type="button" id="loadBoqBtn" style="background: var(--accent); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="download" style="width: 18px; height: 18px;"></i>
                    تحميل بنود BOQ
                </button>
            </div>

            <div id="boqItemsContainer" style="overflow-x: auto;">
                <table id="boqItemsTable" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #f5f5f7;">
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">كود البند</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الوصف</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الوحدة</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الكمية التعاقدية</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الكمية السابقة</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الكمية الحالية</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">سعر الوحدة</th>
                            <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">القيمة الحالية</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; border: 1px solid #e5e5e7;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="boqItemsBody">
                        <tr>
                            <td colspan="9" style="padding: 30px; text-align: center; color: #86868b; border: 1px solid #e5e5e7;">
                                اختر مشروع وانقر على "تحميل بنود BOQ"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Deductions -->
        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e5e5e7;">
                <i data-lucide="minus-circle" style="width: 20px; height: 20px; color: var(--accent);"></i>
                3. الخصومات
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">نسبة الاستبقاء (%) *</label>
                    <input type="number" name="retention_percent" id="retention_percent" value="10" step="0.01" min="0" max="100" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">استرداد دفعة مقدمة</label>
                    <input type="number" name="advance_payment_deduction" id="advance_payment_deduction" value="0" step="0.01" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">خصومات أخرى</label>
                    <input type="number" name="other_deductions" id="other_deductions" value="0" step="0.01" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">نسبة الضريبة (%) *</label>
                    <input type="number" name="tax_rate" id="tax_rate" value="16" step="0.01" min="0" max="100" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">ملاحظات الخصومات</label>
                <textarea name="deductions_notes" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;"></textarea>
            </div>
        </div>

        <!-- 4. Summary -->
        <div style="background: #f5f5f7; padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">
                <i data-lucide="calculator" style="width: 20px; height: 20px; color: var(--accent);"></i>
                ملخص الحسابات
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="background: white; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الأعمال الحالية</div>
                    <div id="summary_current_work" style="font-size: 1.4rem; font-weight: 700; color: var(--accent);">0.00 ر.س</div>
                </div>

                <div style="background: white; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الاستبقاء</div>
                    <div id="summary_retention" style="font-size: 1.4rem; font-weight: 700; color: #ff9500;">0.00 ر.س</div>
                </div>

                <div style="background: white; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الضريبة</div>
                    <div id="summary_tax" style="font-size: 1.4rem; font-weight: 700; color: #ff9500;">0.00 ر.س</div>
                </div>

                <div style="background: white; padding: 15px; border-radius: 8px; border: 2px solid #34c759;">
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الصافي للدفع</div>
                    <div id="summary_net_payable" style="font-size: 1.4rem; font-weight: 700; color: #34c759;">0.00 ر.س</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="<?php echo e(route('main-ipcs.index')); ?>" style="padding: 12px 30px; background: #f5f5f7; color: #1d1d1f; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إلغاء
            </a>
            <button type="submit" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ المستخلص
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
    
    let boqItems = [];
    let itemIndex = 0;

    // Load BOQ Items
    document.getElementById('loadBoqBtn').addEventListener('click', async function() {
        const projectId = document.getElementById('project_id').value;
        
        if (!projectId) {
            alert('يرجى اختيار المشروع أولاً');
            return;
        }

        try {
            const response = await fetch(`/main-ipcs/boq-items?project_id=${projectId}`);
            boqItems = await response.json();
            
            if (boqItems.length === 0) {
                alert('لا توجد بنود BOQ لهذا المشروع');
                return;
            }

            renderBoqItems();
        } catch (error) {
            alert('حدث خطأ أثناء تحميل البنود');
            console.error(error);
        }
    });

    function renderBoqItems() {
        const tbody = document.getElementById('boqItemsBody');
        tbody.innerHTML = '';

        boqItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${item.item_code}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${item.description}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${item.unit}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${parseFloat(item.contract_quantity).toFixed(3)}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${parseFloat(item.previous_quantity).toFixed(3)}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">
                    <input type="hidden" name="items[${index}][boq_item_id]" value="${item.id}">
                    <input type="number" name="items[${index}][current_quantity]" 
                           class="current-qty" 
                           data-index="${index}" 
                           data-unit-price="${item.unit_price}"
                           step="0.001" min="0" value="0"
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">${parseFloat(item.unit_price).toFixed(2)}</td>
                <td style="padding: 10px; border: 1px solid #e5e5e7;">
                    <span class="item-amount" data-index="${index}">0.00</span>
                </td>
                <td style="padding: 10px; border: 1px solid #e5e5e7; text-align: center;">
                    <button type="button" onclick="removeItem(${index})" style="background: rgba(255, 59, 48, 0.1); color: #ff3b30; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        lucide.createIcons();
        attachQuantityListeners();
        updateSummary();
    }

    function attachQuantityListeners() {
        document.querySelectorAll('.current-qty').forEach(input => {
            input.addEventListener('input', function() {
                const index = this.dataset.index;
                const unitPrice = parseFloat(this.dataset.unitPrice);
                const quantity = parseFloat(this.value) || 0;
                const amount = quantity * unitPrice;
                
                document.querySelector(`.item-amount[data-index="${index}"]`).textContent = amount.toFixed(2);
                updateSummary();
            });
        });
    }

    function removeItem(index) {
        boqItems.splice(index, 1);
        renderBoqItems();
    }

    function updateSummary() {
        let currentWork = 0;
        document.querySelectorAll('.item-amount').forEach(span => {
            currentWork += parseFloat(span.textContent) || 0;
        });

        const retentionPercent = parseFloat(document.getElementById('retention_percent').value) || 0;
        const advanceDeduction = parseFloat(document.getElementById('advance_payment_deduction').value) || 0;
        const otherDeductions = parseFloat(document.getElementById('other_deductions').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;

        const retention = currentWork * (retentionPercent / 100);
        const taxableAmount = currentWork - retention;
        const tax = taxableAmount * (taxRate / 100);
        const netPayable = currentWork - retention - advanceDeduction - otherDeductions + tax;

        document.getElementById('summary_current_work').textContent = currentWork.toFixed(2) + ' ر.س';
        document.getElementById('summary_retention').textContent = retention.toFixed(2) + ' ر.س';
        document.getElementById('summary_tax').textContent = tax.toFixed(2) + ' ر.س';
        document.getElementById('summary_net_payable').textContent = netPayable.toFixed(2) + ' ر.س';
    }

    // Update summary on deduction changes
    ['retention_percent', 'advance_payment_deduction', 'other_deductions', 'tax_rate'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateSummary);
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/main-ipcs/create.blade.php ENDPATH**/ ?>