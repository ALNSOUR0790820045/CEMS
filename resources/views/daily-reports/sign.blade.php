@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="margin-bottom: 10px;">توقيع التقرير اليومي</h1>
        <p style="color: #666;">{{ $dailyReport->report_number }}</p>
    </div>

    <!-- Report Summary -->
    <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">ملخص التقرير</h3>
        <div style="display: grid; gap: 15px;">
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                <div style="color: #666; font-weight: 600;">المشروع:</div>
                <div>{{ $dailyReport->project->name }}</div>
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                <div style="color: #666; font-weight: 600;">التاريخ:</div>
                <div>{{ $dailyReport->report_date->format('Y-m-d') }}</div>
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                <div style="color: #666; font-weight: 600;">عدد العمال:</div>
                <div>{{ $dailyReport->workers_count }}</div>
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                <div style="color: #666; font-weight: 600;">ساعات العمل:</div>
                <div>{{ $dailyReport->total_work_hours }} ساعة</div>
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                <div style="color: #666; font-weight: 600;">عدد الصور:</div>
                <div>{{ $dailyReport->photos->count() }} صورة</div>
            </div>
        </div>

        @if($dailyReport->work_executed)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <div style="color: #666; font-weight: 600; margin-bottom: 10px;">الأعمال المنفذة:</div>
                <div style="line-height: 1.8; color: #333;">{{ Str::limit($dailyReport->work_executed, 300) }}</div>
            </div>
        @endif

        @if($dailyReport->problems)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <div style="color: #dc3545; font-weight: 600; margin-bottom: 10px;">⚠️ المشاكل:</div>
                <div style="line-height: 1.8; color: #333;">{{ $dailyReport->problems }}</div>
            </div>
        @endif
    </div>

    <!-- Signature Actions -->
    <div style="background: white; padding: 25px; border-radius: 10px;">
        <h3 style="margin-bottom: 20px;">التوقيع</h3>

        <form method="POST" action="{{ route('daily-reports.sign.post', $dailyReport) }}">
            @csrf

            <!-- Signature Type Selection -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">نوع التوقيع *</label>
                <div style="display: grid; gap: 10px;">
                    @if(!$dailyReport->reviewed_by)
                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="signature_type" value="review" required 
                                   style="width: 20px; height: 20px;">
                            <div>
                                <div style="font-weight: 600;">مراجعة مدير المشروع</div>
                                <div style="color: #666; font-size: 0.85rem;">المراجعة والموافقة على التقرير</div>
                            </div>
                        </label>
                    @endif

                    @if($dailyReport->reviewed_by && !$dailyReport->consultant_approved_by)
                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="signature_type" value="consultant" required 
                                   style="width: 20px; height: 20px;">
                            <div>
                                <div style="font-weight: 600;">اعتماد الاستشاري</div>
                                <div style="color: #666; font-size: 0.85rem;">الموافقة النهائية من الاستشاري</div>
                            </div>
                        </label>
                    @endif

                    @if($dailyReport->consultant_approved_by && !$dailyReport->client_approved_by)
                        <label style="display: flex; align-items: center; gap: 10px; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="signature_type" value="client" required 
                                   style="width: 20px; height: 20px;">
                            <div>
                                <div style="font-weight: 600;">توقيع العميل</div>
                                <div style="color: #666; font-size: 0.85rem;">الموافقة من العميل (اختياري)</div>
                            </div>
                        </label>
                    @endif
                </div>
            </div>

            <!-- Comments -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات (اختياري)</label>
                <textarea name="comments" rows="4" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;" 
                          placeholder="أضف أي ملاحظات أو تعليقات"></textarea>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; justify-content: center; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <button type="submit" name="action" value="reject" 
                        style="background: #dc3545; color: white; padding: 12px 40px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: flex; align-items: center; gap: 8px;"
                        onclick="return confirm('هل أنت متأكد من رفض التقرير؟')">
                    <i data-lucide="x-circle" style="width: 18px; height: 18px;"></i>
                    رفض
                </button>
                <a href="{{ route('daily-reports.show', $dailyReport) }}" 
                   style="background: #f5f5f7; color: #333; padding: 12px 40px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    إلغاء
                </a>
                <button type="submit" name="action" value="approve" 
                        style="background: #28a745; color: white; padding: 12px 40px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                    وافق ووقع
                </button>
            </div>
        </form>
    </div>

    <!-- Current Signatures Status -->
    <div style="background: white; padding: 25px; border-radius: 10px; margin-top: 20px;">
        <h4 style="margin-bottom: 15px;">حالة التوقيعات</h4>
        <div style="display: grid; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f5f5f7; border-radius: 6px;">
                <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                <div>
                    <div style="font-weight: 600;">مهندس الموقع</div>
                    <div style="color: #666; font-size: 0.85rem;">
                        {{ $dailyReport->preparedBy->name }} - {{ $dailyReport->prepared_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: {{ $dailyReport->reviewed_by ? '#d4edda' : '#f5f5f7' }}; border-radius: 6px;">
                @if($dailyReport->reviewed_by)
                    <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                    <div>
                        <div style="font-weight: 600;">مدير المشروع</div>
                        <div style="color: #666; font-size: 0.85rem;">
                            {{ $dailyReport->reviewedBy->name }} - {{ $dailyReport->reviewed_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @else
                    <span style="color: #999; font-size: 1.2rem;">○</span>
                    <div>
                        <div style="font-weight: 600; color: #666;">مدير المشروع</div>
                        <div style="color: #999; font-size: 0.85rem;">بانتظار التوقيع</div>
                    </div>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: {{ $dailyReport->consultant_approved_by ? '#d4edda' : '#f5f5f7' }}; border-radius: 6px;">
                @if($dailyReport->consultant_approved_by)
                    <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                    <div>
                        <div style="font-weight: 600;">الاستشاري</div>
                        <div style="color: #666; font-size: 0.85rem;">
                            {{ $dailyReport->consultantApprovedBy->name }} - {{ $dailyReport->consultant_approved_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @else
                    <span style="color: #999; font-size: 1.2rem;">○</span>
                    <div>
                        <div style="font-weight: 600; color: #666;">الاستشاري</div>
                        <div style="color: #999; font-size: 0.85rem;">بانتظار التوقيع</div>
                    </div>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: {{ $dailyReport->client_approved_by ? '#d4edda' : '#f5f5f7' }}; border-radius: 6px;">
                @if($dailyReport->client_approved_by)
                    <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                    <div>
                        <div style="font-weight: 600;">العميل</div>
                        <div style="color: #666; font-size: 0.85rem;">
                            {{ $dailyReport->clientApprovedBy->name }} - {{ $dailyReport->client_approved_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @else
                    <span style="color: #999; font-size: 1.2rem;">○</span>
                    <div>
                        <div style="font-weight: 600; color: #666;">العميل</div>
                        <div style="color: #999; font-size: 0.85rem;">غير موقع (اختياري)</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    // Highlight selected radio button
    document.querySelectorAll('input[name="signature_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="signature_type"]').forEach(r => {
                r.parentElement.style.borderColor = '#ddd';
                r.parentElement.style.background = 'white';
            });
            this.parentElement.style.borderColor = '#0071e3';
            this.parentElement.style.background = '#e8f4fd';
        });
    });
</script>
@endpush
@endsection
