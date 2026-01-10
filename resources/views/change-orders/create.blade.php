@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f0f0f0;
    }
    .form-section:last-child {
        border-bottom: none;
    }
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1d1d1f;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
    }
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .calculated-field {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 10px 12px;
        border-radius: 6px;
        font-weight: 600;
        color: #495057;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .items-table th,
    .items-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: right;
    }
    .items-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    .items-table input {
        width: 100%;
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 1.8rem; font-weight: 700; color: #1d1d1f; margin-bottom: 10px;">إنشاء أمر تغيير جديد</h1>
    <p style="color: #666;">قم بملء جميع الحقول المطلوبة لإنشاء أمر تغيير</p>
</div>

<form method="POST" action="{{ route('change-orders.store') }}" enctype="multipart/form-data" id="coForm">
    @csrf

    <div class="card">
        <div class="form-section">
            <h2 class="section-title">1. المعلومات الأساسية</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>رقم CO *</label>
                    <input type="text" value="{{ $coNumber }}" readonly class="calculated-field">
                </div>
                <div class="form-group">
                    <label>التاريخ *</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>المشروع *</label>
                    <select name="project_id" required id="project_id">
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>العطاء الأصلي</label>
                    <select name="tender_id" id="tender_id">
                        <option value="">اختر العطاء</option>
                        @foreach($tenders as $tender)
                        <option value="{{ $tender->id }}">{{ $tender->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>العقد الأصلي</label>
                    <select name="original_contract_id" id="contract_id">
                        <option value="">اختر العقد</option>
                        @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}" data-value="{{ $contract->contract_value }}">{{ $contract->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>النوع *</label>
                    <select name="type" required>
                        <option value="scope_change">تغيير في النطاق</option>
                        <option value="quantity_change">تغيير في الكميات</option>
                        <option value="design_change">تغيير في التصميم</option>
                        <option value="specification_change">تغيير في المواصفات</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>السبب *</label>
                    <select name="reason" required>
                        <option value="client_request">طلب العميل</option>
                        <option value="design_error">خطأ تصميم</option>
                        <option value="site_condition">ظروف الموقع</option>
                        <option value="regulatory">متطلبات تنظيمية</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>العنوان *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="عنوان أمر التغيير">
            </div>

            <div class="form-group">
                <label>الوصف التفصيلي *</label>
                <textarea name="description" required placeholder="اشرح التغييرات المطلوبة بالتفصيل">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>التبرير</label>
                <textarea name="justification" placeholder="أسباب وتبريرات التغيير">{{ old('justification') }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-title">2. التحليل المالي</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>قيمة العقد الأصلي *</label>
                    <input type="number" name="original_contract_value" id="original_value" step="0.01" value="{{ old('original_contract_value', 0) }}" required>
                </div>
                <div class="form-group">
                    <label>قيمة التغيير (net) *</label>
                    <input type="number" name="net_amount" id="net_amount" step="0.01" value="{{ old('net_amount', 0) }}" required>
                </div>
                <div class="form-group">
                    <label>الضريبة (15%) *</label>
                    <input type="number" name="tax_amount" id="tax_amount" step="0.01" value="{{ old('tax_amount', 0) }}" required readonly class="calculated-field">
                </div>
                <div class="form-group">
                    <label>الإجمالي *</label>
                    <input type="number" name="total_amount" id="total_amount" step="0.01" value="{{ old('total_amount', 0) }}" required readonly class="calculated-field">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>نسبة الرسوم (%)</label>
                    <input type="number" name="fee_percentage" id="fee_percentage" step="0.001" value="{{ old('fee_percentage', 0.3) }}">
                </div>
                <div class="form-group">
                    <label>الرسوم المحسوبة</label>
                    <input type="text" id="calculated_fee" readonly class="calculated-field" value="0.00 ر.س">
                </div>
                <div class="form-group">
                    <label>رسوم الطوابع</label>
                    <input type="text" id="stamp_duty" readonly class="calculated-field" value="0.00 ر.س">
                </div>
                <div class="form-group">
                    <label>إجمالي الرسوم</label>
                    <input type="text" id="total_fees" readonly class="calculated-field" value="0.00 ر.س">
                </div>
            </div>

            <div class="form-group">
                <label>القيمة المحدثة للعقد</label>
                <input type="text" id="updated_value" readonly class="calculated-field" value="0.00 ر.س">
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-title">3. الأثر على الجدول الزمني</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>عدد أيام التمديد</label>
                    <input type="number" name="time_extension_days" value="{{ old('time_extension_days', 0) }}" min="0">
                </div>
            </div>
            <div class="form-group">
                <label>وصف الأثر على الجدول</label>
                <textarea name="schedule_impact_description" placeholder="اشرح تأثير التغيير على الجدول الزمني">{{ old('schedule_impact_description') }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-title">4. المرفقات</h2>
            <div class="form-group">
                <label>رفع الملفات</label>
                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <small style="color: #666; display: block; margin-top: 5px;">يمكنك رفع عدة ملفات (الرسومات، الحسابات، المستندات الداعمة)</small>
            </div>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-start;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                حفظ أمر التغيير
            </button>
            <a href="{{ route('change-orders.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </div>
</form>

@push('scripts')
<script>
    lucide.createIcons();

    // Auto-fill contract value when contract is selected
    document.getElementById('contract_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const contractValue = selectedOption.getAttribute('data-value');
        if (contractValue) {
            document.getElementById('original_value').value = contractValue;
            calculateAll();
        }
    });

    // Calculate tax and total when net amount changes
    document.getElementById('net_amount').addEventListener('input', calculateAll);
    document.getElementById('original_value').addEventListener('input', calculateAll);
    document.getElementById('fee_percentage').addEventListener('input', calculateAll);

    function calculateAll() {
        const netAmount = parseFloat(document.getElementById('net_amount').value) || 0;
        const originalValue = parseFloat(document.getElementById('original_value').value) || 0;
        const feePercentage = parseFloat(document.getElementById('fee_percentage').value) || 0;

        // Calculate tax (15%)
        const taxAmount = netAmount * 0.15;
        document.getElementById('tax_amount').value = taxAmount.toFixed(2);

        // Calculate total
        const totalAmount = netAmount + taxAmount;
        document.getElementById('total_amount').value = totalAmount.toFixed(2);

        // Calculate fees
        const calculatedFee = Math.abs(netAmount) * (feePercentage / 100);
        document.getElementById('calculated_fee').value = calculatedFee.toFixed(2) + ' ر.س';

        // Calculate stamp duty (simplified: 0.1% with min/max)
        let stampDuty = Math.abs(totalAmount) * 0.001;
        stampDuty = Math.min(Math.max(stampDuty, 50), 10000);
        document.getElementById('stamp_duty').value = stampDuty.toFixed(2) + ' ر.س';

        // Total fees
        const totalFees = calculatedFee + stampDuty;
        document.getElementById('total_fees').value = totalFees.toFixed(2) + ' ر.س';

        // Updated contract value
        const updatedValue = originalValue + totalAmount;
        document.getElementById('updated_value').value = updatedValue.toFixed(2) + ' ر.س';
    }

    // Initial calculation
    calculateAll();
</script>
@endpush
@endsection
