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
    .approval-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #eee;
    }
    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-success {
        background: #28a745;
        color: white;
    }
    .btn-success:hover {
        background: #218838;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: 'Cairo', sans-serif;
        min-height: 120px;
        resize: vertical;
    }
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
    }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 20px 0;
    }
    .summary-item {
        text-align: center;
    }
    .summary-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 5px;
    }
    .summary-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1d1d1f;
    }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 1.8rem; font-weight: 700; color: #1d1d1f; margin-bottom: 10px;">
        الموافقة على أمر التغيير {{ $changeOrder->co_number }}
    </h1>
    <p style="color: #666;">قم بمراجعة أمر التغيير واتخاذ القرار المناسب</p>
</div>

<div class="alert alert-warning">
    <strong>تنبيه:</strong> قرارك سيؤثر على سير العمل. يرجى المراجعة بعناية قبل اتخاذ القرار.
</div>

<div class="card">
    <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px;">ملخص أمر التغيير</h2>
    
    <div style="margin-bottom: 20px;">
        <p><strong>العنوان:</strong> {{ $changeOrder->title }}</p>
        <p><strong>المشروع:</strong> {{ $changeOrder->project->name ?? '-' }}</p>
        <p><strong>النوع:</strong> {{ $changeOrder->type_label }}</p>
        <p><strong>السبب:</strong> {{ $changeOrder->reason_label }}</p>
    </div>

    <div style="margin-bottom: 20px;">
        <p><strong>الوصف:</strong></p>
        <p style="color: #666; line-height: 1.6;">{{ $changeOrder->description }}</p>
    </div>

    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-label">قيمة التغيير</div>
            <div class="summary-value" style="color: {{ $changeOrder->net_amount >= 0 ? '#28a745' : '#dc3545' }};">
                {{ number_format($changeOrder->net_amount, 2) }} ر.س
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">الإجمالي (شامل الضريبة)</div>
            <div class="summary-value">
                {{ number_format($changeOrder->total_amount, 2) }} ر.س
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">إجمالي الرسوم</div>
            <div class="summary-value">
                {{ number_format($changeOrder->total_fees, 2) }} ر.س
            </div>
        </div>
        <div class="summary-item">
            <div class="summary-label">تمديد الوقت</div>
            <div class="summary-value">
                {{ $changeOrder->time_extension_days }} يوم
            </div>
        </div>
    </div>

    @if($changeOrder->items->count() > 0)
    <div style="margin-top: 30px;">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;">بنود التغيير</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">الوصف</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">الفرق</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changeOrder->items as $item)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $item->description }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ number_format($item->quantity_difference, 3) }} {{ $item->unit }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ number_format($item->amount, 2) }} ر.س</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <form method="POST" action="{{ route('change-orders.process-approval', $changeOrder) }}" id="approvalForm">
        @csrf
        <input type="hidden" name="level" value="{{ $approvalLevel }}">
        <input type="hidden" name="decision" id="decision" value="">

        <div class="form-group" style="margin-top: 30px;">
            <label for="comments">التعليقات (إلزامي) *</label>
            <textarea name="comments" id="comments" required placeholder="أدخل تعليقاتك وملاحظاتك حول أمر التغيير..."></textarea>
        </div>

        <div class="approval-actions">
            <button type="button" onclick="submitDecision('approved')" class="btn btn-success">
                <i data-lucide="check-circle" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                ✅ الموافقة
            </button>
            <button type="button" onclick="submitDecision('rejected')" class="btn btn-danger">
                <i data-lucide="x-circle" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                ❌ الرفض
            </button>
            <a href="{{ route('change-orders.show', $changeOrder) }}" class="btn btn-secondary">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function submitDecision(decision) {
        const comments = document.getElementById('comments').value.trim();
        
        if (!comments) {
            alert('يرجى إدخال التعليقات قبل اتخاذ القرار');
            return;
        }

        const confirmMessage = decision === 'approved' 
            ? 'هل أنت متأكد من الموافقة على أمر التغيير؟' 
            : 'هل أنت متأكد من رفض أمر التغيير؟';

        if (confirm(confirmMessage)) {
            document.getElementById('decision').value = decision;
            document.getElementById('approvalForm').submit();
        }
    }
</script>
@endpush
@endsection
