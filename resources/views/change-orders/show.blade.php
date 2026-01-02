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
    .info-section {
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #eee;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #1d1d1f;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    .info-item {
        margin-bottom: 10px;
    }
    .info-label {
        font-weight: 500;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    .info-value {
        color: #1d1d1f;
        font-size: 1rem;
    }
    .badge {
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    .badge-green { background: #d4edda; color: #155724; }
    .badge-blue { background: #cce5ff; color: #004085; }
    .badge-yellow { background: #fff3cd; color: #856404; }
    .badge-purple { background: #e7d6f7; color: #6c2b9c; }
    .badge-orange { background: #ffe5cc; color: #cc5200; }
    .badge-red { background: #f8d7da; color: #721c24; }
    .badge-gray { background: #e2e3e5; color: #383d41; }
    .signature-timeline {
        position: relative;
        padding-right: 40px;
    }
    .signature-step {
        position: relative;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 2px solid #eee;
    }
    .signature-step.approved {
        border-color: #28a745;
        background: #f8fff8;
    }
    .signature-step.rejected {
        border-color: #dc3545;
        background: #fff8f8;
    }
    .signature-step.pending {
        border-color: #ffc107;
        background: #fffef8;
    }
    .signature-icon {
        position: absolute;
        right: -40px;
        top: 20px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
    }
    .signature-icon.approved { background: #28a745; }
    .signature-icon.rejected { background: #dc3545; }
    .signature-icon.pending { background: #ffc107; }
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
    .btn-primary { background: #0071e3; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #f8f9fa;
        font-weight: 600;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; color: #1d1d1f; margin-bottom: 5px;">
            أمر تغيير {{ $changeOrder->co_number }}
        </h1>
        <p style="color: #666;">{{ $changeOrder->title }}</p>
    </div>
    <div>
        <span class="badge badge-{{ $changeOrder->status_color }}">
            {{ $changeOrder->status_label }}
        </span>
    </div>
</div>

<div style="display: flex; gap: 10px; margin-bottom: 20px;">
    @if($changeOrder->status === 'draft')
        <a href="{{ route('change-orders.edit', $changeOrder) }}" class="btn btn-primary">
            <i data-lucide="edit" style="width: 16px; height: 16px; vertical-align: middle;"></i>
            تعديل
        </a>
        <form method="POST" action="{{ route('change-orders.submit', $changeOrder) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                <i data-lucide="send" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                إرسال للموافقة
            </button>
        </form>
    @endif

    @php
        $canApprove = false;
        if ($changeOrder->status === 'pending_pm' && $changeOrder->pm_user_id === Auth::id()) $canApprove = true;
        if ($changeOrder->status === 'pending_technical' && $changeOrder->technical_user_id === Auth::id()) $canApprove = true;
        if ($changeOrder->status === 'pending_consultant' && $changeOrder->consultant_user_id === Auth::id()) $canApprove = true;
        if ($changeOrder->status === 'pending_client' && $changeOrder->client_user_id === Auth::id()) $canApprove = true;
    @endphp

    @if($canApprove)
        <a href="{{ route('change-orders.approve', $changeOrder) }}" class="btn btn-success">
            <i data-lucide="check-circle" style="width: 16px; height: 16px; vertical-align: middle;"></i>
            الموافقة / الرفض
        </a>
    @endif

    <a href="{{ route('change-orders.export-pdf', $changeOrder) }}" class="btn btn-secondary">
        <i data-lucide="download" style="width: 16px; height: 16px; vertical-align: middle;"></i>
        تحميل PDF
    </a>
    <a href="{{ route('change-orders.index') }}" class="btn btn-secondary">العودة</a>
</div>

<div class="card">
    <div class="info-section">
        <h2 class="section-title">المعلومات الأساسية</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">رقم CO</div>
                <div class="info-value"><strong>{{ $changeOrder->co_number }}</strong></div>
            </div>
            <div class="info-item">
                <div class="info-label">التاريخ</div>
                <div class="info-value">{{ $changeOrder->issue_date->format('Y-m-d') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">المشروع</div>
                <div class="info-value">{{ $changeOrder->project->name ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">النوع</div>
                <div class="info-value">{{ $changeOrder->type_label }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">السبب</div>
                <div class="info-value">{{ $changeOrder->reason_label }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">منشئ الأمر</div>
                <div class="info-value">{{ $changeOrder->creator->name ?? '-' }}</div>
            </div>
        </div>
        <div style="margin-top: 15px;">
            <div class="info-label">الوصف</div>
            <div class="info-value">{{ $changeOrder->description }}</div>
        </div>
        @if($changeOrder->justification)
        <div style="margin-top: 15px;">
            <div class="info-label">التبرير</div>
            <div class="info-value">{{ $changeOrder->justification }}</div>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h2 class="section-title">التحليل المالي</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">قيمة العقد الأصلي</div>
                <div class="info-value" style="font-weight: 600;">{{ number_format($changeOrder->original_contract_value, 2) }} ر.س</div>
            </div>
            <div class="info-item">
                <div class="info-label">قيمة التغيير (net)</div>
                <div class="info-value" style="font-weight: 600; color: {{ $changeOrder->net_amount >= 0 ? '#28a745' : '#dc3545' }};">
                    {{ number_format($changeOrder->net_amount, 2) }} ر.س
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">الضريبة (15%)</div>
                <div class="info-value">{{ number_format($changeOrder->tax_amount, 2) }} ر.س</div>
            </div>
            <div class="info-item">
                <div class="info-label">الإجمالي</div>
                <div class="info-value" style="font-weight: 700; font-size: 1.1rem;">{{ number_format($changeOrder->total_amount, 2) }} ر.س</div>
            </div>
        </div>
        <div class="info-grid" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
            <div class="info-item">
                <div class="info-label">الرسوم المحسوبة</div>
                <div class="info-value">{{ number_format($changeOrder->calculated_fee, 2) }} ر.س</div>
            </div>
            <div class="info-item">
                <div class="info-label">رسوم الطوابع</div>
                <div class="info-value">{{ number_format($changeOrder->stamp_duty, 2) }} ر.س</div>
            </div>
            <div class="info-item">
                <div class="info-label">إجمالي الرسوم</div>
                <div class="info-value" style="font-weight: 600;">{{ number_format($changeOrder->total_fees, 2) }} ر.س</div>
            </div>
            <div class="info-item">
                <div class="info-label">القيمة المحدثة للعقد</div>
                <div class="info-value" style="font-weight: 700; font-size: 1.1rem; color: #0071e3;">
                    {{ number_format($changeOrder->updated_contract_value, 2) }} ر.س
                </div>
            </div>
        </div>
    </div>

    @if($changeOrder->items->count() > 0)
    <div class="info-section">
        <h2 class="section-title">بنود التغيير</h2>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>WBS</th>
                    <th>الكمية الأصلية</th>
                    <th>الكمية المعدلة</th>
                    <th>الفرق</th>
                    <th>الوحدة</th>
                    <th>سعر الوحدة</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changeOrder->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->wbs->name ?? '-' }}</td>
                    <td>{{ number_format($item->original_quantity, 3) }}</td>
                    <td>{{ number_format($item->changed_quantity, 3) }}</td>
                    <td style="color: {{ $item->quantity_difference >= 0 ? '#28a745' : '#dc3545' }};">
                        {{ number_format($item->quantity_difference, 3) }}
                    </td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td style="font-weight: 600;">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="info-section">
        <h2 class="section-title">الأثر على الجدول الزمني</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">عدد أيام التمديد</div>
                <div class="info-value" style="font-weight: 600;">{{ $changeOrder->time_extension_days }} يوم</div>
            </div>
            @if($changeOrder->new_completion_date)
            <div class="info-item">
                <div class="info-label">تاريخ الإنجاز الجديد</div>
                <div class="info-value">{{ $changeOrder->new_completion_date->format('Y-m-d') }}</div>
            </div>
            @endif
        </div>
        @if($changeOrder->schedule_impact_description)
        <div style="margin-top: 10px;">
            <div class="info-label">وصف الأثر</div>
            <div class="info-value">{{ $changeOrder->schedule_impact_description }}</div>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h2 class="section-title">سلسلة التوقيعات</h2>
        <div class="signature-timeline">
            <!-- PM Signature -->
            <div class="signature-step {{ $changeOrder->pm_decision === 'approved' ? 'approved' : ($changeOrder->pm_decision === 'rejected' ? 'rejected' : 'pending') }}">
                <div class="signature-icon {{ $changeOrder->pm_decision === 'approved' ? 'approved' : ($changeOrder->pm_decision === 'rejected' ? 'rejected' : 'pending') }}">
                    @if($changeOrder->pm_decision === 'approved')
                        ✓
                    @elseif($changeOrder->pm_decision === 'rejected')
                        ✗
                    @else
                        1
                    @endif
                </div>
                <div style="font-weight: 600; margin-bottom: 5px;">مدير المشروع</div>
                <div style="color: #666; font-size: 0.9rem;">{{ $changeOrder->pmUser->name ?? 'غير محدد' }}</div>
                @if($changeOrder->pm_signed_at)
                <div style="color: #666; font-size: 0.85rem; margin-top: 5px;">
                    {{ $changeOrder->pm_signed_at->format('Y-m-d H:i') }}
                </div>
                @endif
                @if($changeOrder->pm_comments)
                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 0.9rem;">
                    {{ $changeOrder->pm_comments }}
                </div>
                @endif
            </div>

            <!-- Technical Signature -->
            <div class="signature-step {{ $changeOrder->technical_decision === 'approved' ? 'approved' : ($changeOrder->technical_decision === 'rejected' ? 'rejected' : 'pending') }}">
                <div class="signature-icon {{ $changeOrder->technical_decision === 'approved' ? 'approved' : ($changeOrder->technical_decision === 'rejected' ? 'rejected' : 'pending') }}">
                    @if($changeOrder->technical_decision === 'approved')
                        ✓
                    @elseif($changeOrder->technical_decision === 'rejected')
                        ✗
                    @else
                        2
                    @endif
                </div>
                <div style="font-weight: 600; margin-bottom: 5px;">المدير الفني</div>
                <div style="color: #666; font-size: 0.9rem;">{{ $changeOrder->technicalUser->name ?? 'غير محدد' }}</div>
                @if($changeOrder->technical_signed_at)
                <div style="color: #666; font-size: 0.85rem; margin-top: 5px;">
                    {{ $changeOrder->technical_signed_at->format('Y-m-d H:i') }}
                </div>
                @endif
                @if($changeOrder->technical_comments)
                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 0.9rem;">
                    {{ $changeOrder->technical_comments }}
                </div>
                @endif
            </div>

            <!-- Consultant Signature -->
            <div class="signature-step {{ $changeOrder->consultant_decision === 'approved' ? 'approved' : ($changeOrder->consultant_decision === 'rejected' ? 'rejected' : 'pending') }}">
                <div class="signature-icon {{ $changeOrder->consultant_decision === 'approved' ? 'approved' : ($changeOrder->consultant_decision === 'rejected' ? 'rejected' : 'pending') }}">
                    @if($changeOrder->consultant_decision === 'approved')
                        ✓
                    @elseif($changeOrder->consultant_decision === 'rejected')
                        ✗
                    @else
                        3
                    @endif
                </div>
                <div style="font-weight: 600; margin-bottom: 5px;">الاستشاري</div>
                <div style="color: #666; font-size: 0.9rem;">{{ $changeOrder->consultantUser->name ?? 'غير محدد' }}</div>
                @if($changeOrder->consultant_signed_at)
                <div style="color: #666; font-size: 0.85rem; margin-top: 5px;">
                    {{ $changeOrder->consultant_signed_at->format('Y-m-d H:i') }}
                </div>
                @endif
                @if($changeOrder->consultant_comments)
                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 0.9rem;">
                    {{ $changeOrder->consultant_comments }}
                </div>
                @endif
            </div>

            <!-- Client Signature -->
            <div class="signature-step {{ $changeOrder->client_decision === 'approved' ? 'approved' : ($changeOrder->client_decision === 'rejected' ? 'rejected' : 'pending') }}">
                <div class="signature-icon {{ $changeOrder->client_decision === 'approved' ? 'approved' : ($changeOrder->client_decision === 'rejected' ? 'rejected' : 'pending') }}">
                    @if($changeOrder->client_decision === 'approved')
                        ✓
                    @elseif($changeOrder->client_decision === 'rejected')
                        ✗
                    @else
                        4
                    @endif
                </div>
                <div style="font-weight: 600; margin-bottom: 5px;">العميل</div>
                <div style="color: #666; font-size: 0.9rem;">{{ $changeOrder->clientUser->name ?? 'غير محدد' }}</div>
                @if($changeOrder->client_signed_at)
                <div style="color: #666; font-size: 0.85rem; margin-top: 5px;">
                    {{ $changeOrder->client_signed_at->format('Y-m-d H:i') }}
                </div>
                @endif
                @if($changeOrder->client_comments)
                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 0.9rem;">
                    {{ $changeOrder->client_comments }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($changeOrder->attachments && count($changeOrder->attachments) > 0)
    <div class="info-section" style="border-bottom: none;">
        <h2 class="section-title">المرفقات</h2>
        <ul style="list-style: none; padding: 0;">
            @foreach($changeOrder->attachments as $attachment)
            <li style="margin-bottom: 10px;">
                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" style="color: #0071e3; text-decoration: none;">
                    <i data-lucide="file" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    {{ $attachment['name'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
