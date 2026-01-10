@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <a href="{{ route('site-receipts.show', $siteReceipt) }}" style="color: #0071e3; text-decoration: none; font-weight: 600; margin-bottom: 10px; display: inline-block;">
            ← العودة لعرض الاستلام
        </a>
        <h1 style="font-size: 28px; font-weight: 700;">التحقق من استلام الموقع</h1>
        <p style="color: #666; margin-top: 10px;">رقم الاستلام: <strong>{{ $siteReceipt->receipt_number }}</strong></p>
    </div>

    <form method="POST" action="{{ route('site-receipts.process-verification', $siteReceipt) }}">
        @csrf

        <!-- Verification Checklist -->
        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">✅ قائمة التحقق</h2>
            
            <div style="display: grid; gap: 15px;">
                <div style="padding: 15px; background: {{ $siteReceipt->latitude ? '#d4edda' : '#f8d7da' }}; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 24px;">{{ $siteReceipt->latitude ? '✅' : '❌' }}</span>
                    <div>
                        <div style="font-weight: 600;">موقع GPS محدد</div>
                        @if($siteReceipt->latitude)
                            <div style="font-size: 14px; color: #666;">{{ $siteReceipt->latitude }}, {{ $siteReceipt->longitude }}</div>
                        @endif
                    </div>
                </div>

                <div style="padding: 15px; background: {{ $siteReceipt->hasAllDocuments() ? '#d4edda' : '#f8d7da' }}; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 24px;">{{ $siteReceipt->hasAllDocuments() ? '✅' : '❌' }}</span>
                    <div>
                        <div style="font-weight: 600;">المستندات الأربعة المطلوبة</div>
                        <div style="font-size: 14px; color: #666;">
                            {{ $siteReceipt->invoice_document ? '✓' : '✗' }} الفاتورة | 
                            {{ $siteReceipt->delivery_note ? '✓' : '✗' }} مذكرة التسليم | 
                            {{ $siteReceipt->packing_list ? '✓' : '✗' }} قائمة التعبئة | 
                            {{ $siteReceipt->quality_certificates ? '✓' : '✗' }} شهادات الجودة
                        </div>
                    </div>
                </div>

                <div style="padding: 15px; background: {{ $siteReceipt->hasAllSignatures() ? '#d4edda' : '#f8d7da' }}; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 24px;">{{ $siteReceipt->hasAllSignatures() ? '✅' : '❌' }}</span>
                    <div>
                        <div style="font-weight: 600;">التوقيعات الثلاثة مكتملة</div>
                        <div style="font-size: 14px; color: #666;">
                            {{ $siteReceipt->engineer_signature ? '✓' : '✗' }} المهندس | 
                            {{ $siteReceipt->storekeeper_signature ? '✓' : '✗' }} أمين المخزن | 
                            {{ $siteReceipt->driver_signature ? '✓' : '✗' }} السائق
                        </div>
                    </div>
                </div>

                <div style="padding: 15px; background: {{ $siteReceipt->items->count() > 0 ? '#d4edda' : '#f8d7da' }}; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 24px;">{{ $siteReceipt->items->count() > 0 ? '✅' : '❌' }}</span>
                    <div>
                        <div style="font-weight: 600;">بنود المواد</div>
                        <div style="font-size: 14px; color: #666;">{{ $siteReceipt->items->count() }} بند</div>
                    </div>
                </div>

                @if($siteReceipt->photos->count() > 0)
                <div style="padding: 15px; background: #d4edda; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 24px;">✅</span>
                    <div>
                        <div style="font-weight: 600;">الصور المرفقة</div>
                        <div style="font-size: 14px; color: #666;">{{ $siteReceipt->photos->count() }} صورة</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Basic Information Review -->
        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">معلومات الاستلام</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">المشروع</label>
                    <div style="font-size: 16px;">{{ $siteReceipt->project->name }}</div>
                </div>
                
                <div>
                    <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">المورد</label>
                    <div style="font-size: 16px;">{{ $siteReceipt->supplier->name }}</div>
                </div>
                
                <div>
                    <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">التاريخ والوقت</label>
                    <div style="font-size: 16px;">{{ $siteReceipt->receipt_date->format('Y-m-d') }} {{ $siteReceipt->receipt_time }}</div>
                </div>
                
                <div>
                    <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">الموقع</label>
                    <div style="font-size: 16px;">{{ $siteReceipt->location_name }}</div>
                </div>
            </div>
        </div>

        <!-- Materials Summary -->
        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">ملخص المواد</h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f7;">
                        <th style="padding: 12px; text-align: right; font-weight: 600;">#</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">المادة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الكمية</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الوحدة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siteReceipt->items as $index => $item)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 12px;">{{ $index + 1 }}</td>
                        <td style="padding: 12px;">{{ $item->product->name }}</td>
                        <td style="padding: 12px; font-weight: 600;">{{ number_format($item->received_quantity, 2) }}</td>
                        <td style="padding: 12px;">{{ $item->unit }}</td>
                        <td style="padding: 12px;">
                            @php
                                $conditionLabels = [
                                    'good' => 'سليم',
                                    'damaged' => 'تالف',
                                    'defective' => 'معيب',
                                    'partial' => 'جزئي'
                                ];
                            @endphp
                            {{ $conditionLabels[$item->condition] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signatures Review -->
        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">التوقيعات</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                @if($siteReceipt->engineer_signature)
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">مهندس الموقع</h3>
                    <img src="{{ $siteReceipt->engineer_signature }}" style="max-width: 100%; border: 1px solid #ddd; border-radius: 6px;">
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">
                        {{ $siteReceipt->engineer->name ?? 'مهندس' }}<br>
                        {{ $siteReceipt->engineer_signed_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                @endif

                @if($siteReceipt->storekeeper_signature)
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">أمين المخزن</h3>
                    <img src="{{ $siteReceipt->storekeeper_signature }}" style="max-width: 100%; border: 1px solid #ddd; border-radius: 6px;">
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">
                        {{ $siteReceipt->storekeeper->name ?? 'أمين مخزن' }}<br>
                        {{ $siteReceipt->storekeeper_signed_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                @endif

                @if($siteReceipt->driver_signature)
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">السائق/المورد</h3>
                    <img src="{{ $siteReceipt->driver_signature }}" style="max-width: 100%; border: 1px solid #ddd; border-radius: 6px;">
                    <div style="margin-top: 10px; font-size: 14px; color: #666;">
                        {{ $siteReceipt->driver_signature_name }}<br>
                        {{ $siteReceipt->driver_signed_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Verification Decision -->
        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">قرار التحقق</h2>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">الإجراء *</label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; padding: 15px 25px; border: 2px solid #34c759; border-radius: 8px; cursor: pointer; background: #d4edda;">
                        <input type="radio" name="action" value="approve" checked style="width: 20px; height: 20px;">
                        <span style="font-weight: 600; color: #155724;">✅ الموافقة</span>
                    </label>
                    
                    <label style="display: flex; align-items: center; gap: 10px; padding: 15px 25px; border: 2px solid #ff3b30; border-radius: 8px; cursor: pointer; background: #f8d7da;">
                        <input type="radio" name="action" value="reject" style="width: 20px; height: 20px;">
                        <span style="font-weight: 600; color: #721c24;">❌ الرفض</span>
                    </label>
                </div>
            </div>

            <div>
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">ملاحظات المدير</label>
                <textarea name="notes" style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; min-height: 120px;" placeholder="أضف أي ملاحظات أو تعليقات..."></textarea>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-right: 4px solid #ff9500;">
                <strong style="color: #856404;">ℹ️ ملاحظة:</strong>
                <p style="color: #856404; margin: 5px 0 0 0;">عند الموافقة، سيتم تلقائياً:</p>
                <ul style="color: #856404; margin: 10px 0 0 20px; padding: 0;">
                    <li>إنشاء إشعار استلام بضائع (GRN)</li>
                    <li>تحديث المخزون</li>
                    <li>إشعار قسم المالية</li>
                    <li>تحديث حالة الطلب</li>
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: space-between; gap: 15px;">
            <a href="{{ route('site-receipts.show', $siteReceipt) }}" style="padding: 15px 30px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-weight: 600; display: inline-block; text-align: center;">
                إلغاء
            </a>
            
            <button type="submit" style="padding: 15px 40px; background: #0071e3; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 16px;">
                تأكيد القرار
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="action"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('label').forEach(label => {
            if (label.querySelector('input[name="action"]')) {
                label.style.opacity = '0.5';
            }
        });
        this.parentElement.style.opacity = '1';
    });
});
</script>
@endsection
