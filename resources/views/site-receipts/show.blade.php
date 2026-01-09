@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <a href="{{ route('site-receipts.index') }}" style="color: #0071e3; text-decoration: none; font-weight: 600; margin-bottom: 10px; display: inline-block;">
                ← العودة للقائمة
            </a>
            <h1 style="font-size: 28px; font-weight: 700;">استلام موقع: {{ $siteReceipt->receipt_number }}</h1>
        </div>
        @if($siteReceipt->status === 'pending_verification')
            <a href="{{ route('site-receipts.verify', $siteReceipt) }}" style="background: #ff9500; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                التحقق والموافقة
            </a>
        @endif
    </div>

    @php
        $statusColors = [
            'draft' => '#999',
            'pending_verification' => '#ff9500',
            'verified' => '#34c759',
            'grn_created' => '#007aff',
            'rejected' => '#ff3b30'
        ];
        $statusLabels = [
            'draft' => 'مسودة',
            'pending_verification' => 'بانتظار التحقق',
            'verified' => 'تم التحقق',
            'grn_created' => 'تم إنشاء GRN',
            'rejected' => 'مرفوض'
        ];
    @endphp

    <!-- Status Badge -->
    <div style="margin-bottom: 20px;">
        <span style="padding: 8px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; background: {{ $statusColors[$siteReceipt->status] }}22; color: {{ $statusColors[$siteReceipt->status] }};">
            {{ $statusLabels[$siteReceipt->status] }}
        </span>
    </div>

    <!-- Section 1: Basic Information -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #0071e3; padding-bottom: 10px;">معلومات الاستلام</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">رقم الاستلام</label>
                <div style="font-size: 18px; font-weight: 600;">{{ $siteReceipt->receipt_number }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">التاريخ</label>
                <div style="font-size: 16px;">{{ $siteReceipt->receipt_date->format('Y-m-d') }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">الوقت</label>
                <div style="font-size: 16px;">{{ $siteReceipt->receipt_time }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">المشروع</label>
                <div style="font-size: 16px;">{{ $siteReceipt->project->name }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">المورد</label>
                <div style="font-size: 16px;">{{ $siteReceipt->supplier->name }}</div>
            </div>
            
            @if($siteReceipt->vehicle_number)
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">رقم السيارة</label>
                <div style="font-size: 16px;">{{ $siteReceipt->vehicle_number }}</div>
            </div>
            @endif
            
            @if($siteReceipt->driver_name)
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">السائق</label>
                <div style="font-size: 16px;">{{ $siteReceipt->driver_name }}</div>
            </div>
            @endif
            
            @if($siteReceipt->driver_phone)
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">هاتف السائق</label>
                <div style="font-size: 16px;">{{ $siteReceipt->driver_phone }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Section 2: GPS Location -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #34c759; padding-bottom: 10px;">📍 الموقع (GPS)</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">اسم الموقع</label>
                <div style="font-size: 16px;">{{ $siteReceipt->location_name }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">خط العرض</label>
                <div style="font-size: 16px; font-family: monospace;">{{ $siteReceipt->latitude }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">خط الطول</label>
                <div style="font-size: 16px; font-family: monospace;">{{ $siteReceipt->longitude }}</div>
            </div>
            
            <div>
                <label style="font-weight: 600; color: #666; display: block; margin-bottom: 5px;">وقت التقاط GPS</label>
                <div style="font-size: 16px;">{{ $siteReceipt->gps_captured_at->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
        
        <div style="height: 300px; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
            <div style="text-align: center;">
                <p>عرض الخريطة</p>
                <p style="font-size: 14px;">{{ $siteReceipt->latitude }}, {{ $siteReceipt->longitude }}</p>
                <a href="https://www.google.com/maps?q={{ $siteReceipt->latitude }},{{ $siteReceipt->longitude }}" target="_blank" style="color: #0071e3; text-decoration: none; font-weight: 600;">فتح في Google Maps →</a>
            </div>
        </div>
    </div>

    <!-- Section 3: Materials Received -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #ff9500; padding-bottom: 10px;">📦 المواد المستلمة</h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7;">
                    <th style="padding: 12px; text-align: right; font-weight: 600;">#</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600;">المادة</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600;">الكمية المستلمة</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600;">المقبولة</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600;">المرفوضة</th>
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
                    <td style="padding: 12px; color: #34c759;">{{ number_format($item->accepted_quantity, 2) }}</td>
                    <td style="padding: 12px; color: #ff3b30;">{{ number_format($item->rejected_quantity, 2) }}</td>
                    <td style="padding: 12px;">{{ $item->unit }}</td>
                    <td style="padding: 12px;">
                        @php
                            $conditionLabels = [
                                'good' => 'سليم',
                                'damaged' => 'تالف',
                                'defective' => 'معيب',
                                'partial' => 'جزئي'
                            ];
                            $conditionColors = [
                                'good' => '#34c759',
                                'damaged' => '#ff3b30',
                                'defective' => '#ff9500',
                                'partial' => '#ffcc00'
                            ];
                        @endphp
                        <span style="padding: 4px 8px; border-radius: 6px; font-size: 12px; background: {{ $conditionColors[$item->condition] }}22; color: {{ $conditionColors[$item->condition] }};">
                            {{ $conditionLabels[$item->condition] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Section 4: Documents -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #007aff; padding-bottom: 10px;">📄 المستندات المرفقة</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">1. الفاتورة الأصلية</h3>
                @if($siteReceipt->invoice_document)
                    <a href="{{ Storage::url($siteReceipt->invoice_document) }}" target="_blank" style="color: #0071e3; text-decoration: none; font-weight: 600;">
                        📥 تحميل / عرض
                    </a>
                @else
                    <span style="color: #999;">غير متوفر</span>
                @endif
            </div>
            
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">2. مذكرة التسليم</h3>
                @if($siteReceipt->delivery_note)
                    <a href="{{ Storage::url($siteReceipt->delivery_note) }}" target="_blank" style="color: #0071e3; text-decoration: none; font-weight: 600;">
                        📥 تحميل / عرض
                    </a>
                @else
                    <span style="color: #999;">غير متوفر</span>
                @endif
            </div>
            
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">3. قائمة التعبئة</h3>
                @if($siteReceipt->packing_list)
                    <a href="{{ Storage::url($siteReceipt->packing_list) }}" target="_blank" style="color: #0071e3; text-decoration: none; font-weight: 600;">
                        📥 تحميل / عرض
                    </a>
                @else
                    <span style="color: #999;">غير متوفر</span>
                @endif
            </div>
            
            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">4. شهادات الجودة</h3>
                @if($siteReceipt->quality_certificates && count($siteReceipt->quality_certificates) > 0)
                    @foreach($siteReceipt->quality_certificates as $index => $cert)
                        <div style="margin-bottom: 5px;">
                            <a href="{{ Storage::url($cert) }}" target="_blank" style="color: #0071e3; text-decoration: none; font-weight: 600;">
                                📥 شهادة {{ $index + 1 }}
                            </a>
                        </div>
                    @endforeach
                @else
                    <span style="color: #999;">غير متوفر</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 5: Photos -->
    @if($siteReceipt->photos->count() > 0)
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #ff9500; padding-bottom: 10px;">📸 الصور المرفقة</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            @foreach($siteReceipt->photos as $photo)
            <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <img src="{{ Storage::url($photo->photo_path) }}" style="width: 100%; height: 200px; object-fit: cover;">
                <div style="padding: 10px;">
                    <div style="font-size: 14px; font-weight: 600; margin-bottom: 5px;">{{ $photo->title ?? 'صورة' }}</div>
                    <div style="font-size: 12px; color: #666;">
                        📍 {{ $photo->latitude }}, {{ $photo->longitude }}<br>
                        🕐 {{ $photo->captured_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Section 6: Signatures -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #34c759; padding-bottom: 10px;">✍️ التوقيعات</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Engineer Signature -->
            <div style="border: 2px solid #0071e3; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #0071e3;">1. مهندس الموقع</h3>
                @if($siteReceipt->engineer_signature)
                    <div style="border: 1px solid #ddd; border-radius: 6px; padding: 10px; background: #fafafa; margin-bottom: 10px;">
                        <img src="{{ $siteReceipt->engineer_signature }}" style="max-width: 100%; height: auto;">
                    </div>
                    <div style="font-size: 14px; color: #666;">
                        <strong>{{ $siteReceipt->engineer->name ?? 'مهندس' }}</strong><br>
                        وقع في: {{ $siteReceipt->engineer_signed_at->format('Y-m-d H:i') }}
                    </div>
                    @if($siteReceipt->engineer_notes)
                        <div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; font-size: 14px;">
                            <strong>ملاحظات:</strong> {{ $siteReceipt->engineer_notes }}
                        </div>
                    @endif
                @else
                    <div style="color: #999; text-align: center; padding: 40px 0;">لم يتم التوقيع بعد</div>
                @endif
            </div>

            <!-- Storekeeper Signature -->
            <div style="border: 2px solid #34c759; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #34c759;">2. أمين المخزن</h3>
                @if($siteReceipt->storekeeper_signature)
                    <div style="border: 1px solid #ddd; border-radius: 6px; padding: 10px; background: #fafafa; margin-bottom: 10px;">
                        <img src="{{ $siteReceipt->storekeeper_signature }}" style="max-width: 100%; height: auto;">
                    </div>
                    <div style="font-size: 14px; color: #666;">
                        <strong>{{ $siteReceipt->storekeeper->name ?? 'أمين مخزن' }}</strong><br>
                        وقع في: {{ $siteReceipt->storekeeper_signed_at->format('Y-m-d H:i') }}
                    </div>
                    @if($siteReceipt->storekeeper_notes)
                        <div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; font-size: 14px;">
                            <strong>ملاحظات:</strong> {{ $siteReceipt->storekeeper_notes }}
                        </div>
                    @endif
                @else
                    <div style="color: #999; text-align: center; padding: 40px 0;">لم يتم التوقيع بعد</div>
                @endif
            </div>

            <!-- Driver Signature -->
            <div style="border: 2px solid #ff9500; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #ff9500;">3. السائق/المورد</h3>
                @if($siteReceipt->driver_signature)
                    <div style="border: 1px solid #ddd; border-radius: 6px; padding: 10px; background: #fafafa; margin-bottom: 10px;">
                        <img src="{{ $siteReceipt->driver_signature }}" style="max-width: 100%; height: auto;">
                    </div>
                    <div style="font-size: 14px; color: #666;">
                        <strong>{{ $siteReceipt->driver_signature_name }}</strong><br>
                        وقع في: {{ $siteReceipt->driver_signed_at->format('Y-m-d H:i') }}
                    </div>
                @else
                    <div style="color: #999; text-align: center; padding: 40px 0;">لم يتم التوقيع بعد</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 7: GRN Information -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #007aff; padding-bottom: 10px;">🧾 إشعار استلام البضائع (GRN)</h2>
        
        @if($siteReceipt->grn)
            <div style="background: #d4edda; padding: 20px; border-radius: 8px; border-right: 4px solid #34c759;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <label style="font-weight: 600; color: #155724; display: block; margin-bottom: 5px;">رقم GRN</label>
                        <div style="font-size: 18px; font-weight: 600; color: #155724;">{{ $siteReceipt->grn->grn_number }}</div>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #155724; display: block; margin-bottom: 5px;">تاريخ الإنشاء</label>
                        <div style="font-size: 16px; color: #155724;">{{ $siteReceipt->grn_created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #155724; display: block; margin-bottom: 5px;">الحالة</label>
                        <div style="font-size: 16px; color: #155724;">{{ $siteReceipt->grn->status }}</div>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: #155724; display: block; margin-bottom: 5px;">حالة الدفع</label>
                        <div style="font-size: 16px; color: #155724;">
                            @if($siteReceipt->payment_status === 'ready_for_payment')
                                ✅ جاهز للدفع
                            @elseif($siteReceipt->payment_status === 'paid')
                                ✅ تم الدفع
                            @else
                                ⏳ بانتظار
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($siteReceipt->auto_grn_created)
                    <div style="margin-top: 15px; padding: 10px; background: rgba(255,255,255,0.5); border-radius: 6px; font-size: 14px; color: #155724;">
                        ℹ️ تم إنشاء GRN تلقائياً عند اكتمال التوقيعات الثلاثة
                    </div>
                @endif
                
                @if($siteReceipt->finance_notified)
                    <div style="margin-top: 10px; padding: 10px; background: rgba(255,255,255,0.5); border-radius: 6px; font-size: 14px; color: #155724;">
                        ✅ تم إشعار المالية في: {{ $siteReceipt->finance_notified_at->format('Y-m-d H:i') }}
                    </div>
                @endif
            </div>
        @else
            <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border-right: 4px solid #ff9500;">
                <p style="color: #856404; margin: 0;">⏳ لم يتم إنشاء GRN بعد</p>
                @if($siteReceipt->status === 'verified')
                    <button type="button" onclick="alert('سيتم إنشاء GRN تلقائياً')" style="margin-top: 15px; background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        إنشاء GRN الآن
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if($siteReceipt->general_notes)
    <!-- Section 8: General Notes -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #666; padding-bottom: 10px;">📝 ملاحظات عامة</h2>
        <div style="padding: 15px; background: #f5f5f7; border-radius: 8px; white-space: pre-wrap;">{{ $siteReceipt->general_notes }}</div>
    </div>
    @endif
</div>
@endsection
