@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">إضافة عقد Cost Plus جديد</h1>
        <p style="color: #666; font-size: 16px;">إنشاء عقد تكلفة + ربح جديد</p>
    </div>

    <form action="{{ route('cost-plus.contracts.store') }}" method="POST" style="background: white; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        @csrf

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">العقد الأساسي</label>
            <select name="contract_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر العقد</option>
                @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}">{{ $contract->contract_number }} - {{ $contract->contract_name }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">المشروع</label>
            <select name="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر المشروع</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->code }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع الربح</label>
            <select name="fee_type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;" onchange="toggleFeeFields(this.value)">
                <option value="percentage">نسبة مئوية</option>
                <option value="fixed_fee">مبلغ مقطوع</option>
                <option value="incentive">حوافز أداء</option>
                <option value="hybrid">هجين</option>
            </select>
        </div>

        <div id="percentage_field" style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">نسبة الربح (%)</label>
            <input type="number" name="fee_percentage" step="0.01" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div id="fixed_fee_field" style="margin-bottom: 24px; display: none;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">مبلغ الربح المقطوع</label>
            <input type="number" name="fixed_fee_amount" step="0.01" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 24px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="has_gmp" value="1" style="margin-left: 8px;" onchange="toggleGMPFields(this.checked)">
                <span style="font-weight: 600;">تفعيل السقف الأقصى (GMP)</span>
            </label>
        </div>

        <div id="gmp_fields" style="display: none;">
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">السقف الأقصى للسعر (GMP)</label>
                <input type="number" name="guaranteed_maximum_price" step="0.01" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">حصة المقاول من الوفورات (%)</label>
                <input type="number" name="gmp_savings_share" value="50" step="0.01" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">طريقة المصاريف غير المباشرة</label>
            <select name="overhead_method" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="percentage">نسبة مئوية</option>
                <option value="actual">فعلية</option>
                <option value="allocated">موزعة</option>
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">نسبة المصاريف غير المباشرة (%)</label>
            <input type="number" name="overhead_percentage" step="0.01" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">العملة</label>
            <select name="currency" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="JOD" selected>دينار أردني (JOD)</option>
                <option value="USD">دولار أمريكي (USD)</option>
                <option value="EUR">يورو (EUR)</option>
            </select>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;"></textarea>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" style="flex: 1; background: var(--accent); color: white; padding: 14px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                حفظ العقد
            </button>
            <a href="{{ route('cost-plus.contracts.index') }}" style="flex: 1; background: #6c757d; color: white; padding: 14px; border: none; border-radius: 8px; font-weight: 600; text-align: center; text-decoration: none; display: block;">
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
function toggleFeeFields(type) {
    const percentageField = document.getElementById('percentage_field');
    const fixedFeeField = document.getElementById('fixed_fee_field');
    
    percentageField.style.display = (type === 'percentage' || type === 'hybrid') ? 'block' : 'none';
    fixedFeeField.style.display = (type === 'fixed_fee' || type === 'hybrid') ? 'block' : 'none';
}

function toggleGMPFields(enabled) {
    document.getElementById('gmp_fields').style.display = enabled ? 'block' : 'none';
}
</script>
@endsection
