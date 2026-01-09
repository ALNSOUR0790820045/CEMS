@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0 0 10px 0;">تفاصيل خطاب الضمان</h1>
            <p style="color: #666; margin: 0;">{{ $guarantee->guarantee_number }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($guarantee->status == 'draft')
                <form method="POST" action="{{ route('guarantees.approve', $guarantee) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">اعتماد الخطاب</button>
                </form>
            @endif
            @if(in_array($guarantee->status, ['active', 'renewed']))
                <a href="{{ route('guarantees.renew', $guarantee) }}" style="background: #ffc107; color: #000; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">تجديد</a>
                <a href="{{ route('guarantees.release', $guarantee) }}" style="background: #17a2b8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">تحرير</a>
            @endif
            <a href="{{ route('guarantees.edit', $guarantee) }}" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">تعديل</a>
            <a href="{{ route('guarantees.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">رجوع</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Info -->
        <div>
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <h2 style="margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">معلومات أساسية</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">رقم الخطاب</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->guarantee_number }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">النوع</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->type_name }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">البنك</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->bank->name }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">الحالة</label>
                        <span style="background: 
                            @if($guarantee->status == 'active') #d4edda
                            @elseif($guarantee->status == 'draft') #d1ecf1
                            @elseif($guarantee->status == 'expired') #f8d7da
                            @elseif($guarantee->status == 'released') #d1ecf1
                            @else #e2e3e5
                            @endif; 
                            color: 
                            @if($guarantee->status == 'active') #155724
                            @elseif($guarantee->status == 'draft') #0c5460
                            @elseif($guarantee->status == 'expired') #721c24
                            @elseif($guarantee->status == 'released') #0c5460
                            @else #383d41
                            @endif; 
                            padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                            {{ $guarantee->status_name }}
                        </span>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">الجهة المستفيدة</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->beneficiary }}</p>
                        @if($guarantee->beneficiary_address)
                            <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">{{ $guarantee->beneficiary_address }}</p>
                        @endif
                    </div>

                    @if($guarantee->project)
                        <div>
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">المشروع</label>
                            <p style="margin: 0; font-size: 16px;">{{ $guarantee->project->name }}</p>
                        </div>
                    @endif

                    @if($guarantee->tender)
                        <div>
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">المناقصة</label>
                            <p style="margin: 0; font-size: 16px;">{{ $guarantee->tender->name }}</p>
                        </div>
                    @endif

                    @if($guarantee->contract)
                        <div>
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">العقد</label>
                            <p style="margin: 0; font-size: 16px;">{{ $guarantee->contract->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Info -->
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <h2 style="margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">المعلومات المالية</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">المبلغ</label>
                        <p style="margin: 0; font-size: 20px; font-weight: 700; color: #0071e3;">{{ number_format($guarantee->amount, 2) }} {{ $guarantee->currency }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">رسوم البنك</label>
                        <p style="margin: 0; font-size: 16px;">{{ number_format($guarantee->bank_charges, 2) }} {{ $guarantee->currency }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">نسبة العمولة السنوية</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->bank_commission_rate }}%</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">الهامش النقدي</label>
                        <p style="margin: 0; font-size: 16px;">{{ number_format($guarantee->cash_margin, 2) }} {{ $guarantee->currency }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">نسبة الهامش</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->margin_percentage }}%</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">رقم المرجع البنكي</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->bank_reference_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <h2 style="margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">التواريخ</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">تاريخ الإصدار</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->issue_date->format('Y-m-d') }}</p>
                    </div>

                    <div>
                        <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">تاريخ الانتهاء</label>
                        <p style="margin: 0; font-size: 16px;">{{ $guarantee->expiry_date->format('Y-m-d') }}</p>
                        @if($guarantee->is_expiring)
                            <span style="background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-top: 5px; display: inline-block;">ينتهي خلال {{ abs($guarantee->days_until_expiry) }} يوم</span>
                        @elseif($guarantee->is_expired)
                            <span style="background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-top: 5px; display: inline-block;">منتهي</span>
                        @endif
                    </div>

                    @if($guarantee->expected_release_date)
                        <div>
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">تاريخ التحرير المتوقع</label>
                            <p style="margin: 0; font-size: 16px;">{{ $guarantee->expected_release_date->format('Y-m-d') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($guarantee->purpose || $guarantee->notes)
                <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h2 style="margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">تفاصيل إضافية</h2>
                    
                    @if($guarantee->purpose)
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">الغرض</label>
                            <p style="margin: 0; font-size: 16px; line-height: 1.6;">{{ $guarantee->purpose }}</p>
                        </div>
                    @endif

                    @if($guarantee->notes)
                        <div>
                            <label style="font-weight: 600; color: #666; font-size: 14px; display: block; margin-bottom: 5px;">ملاحظات</label>
                            <p style="margin: 0; font-size: 16px; line-height: 1.6;">{{ $guarantee->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; font-size: 16px;">معلومات النظام</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #666; font-size: 13px; display: block; margin-bottom: 5px;">أنشئ بواسطة</label>
                    <p style="margin: 0; font-size: 14px;">{{ $guarantee->creator->name }}</p>
                    <p style="margin: 0; font-size: 12px; color: #999;">{{ $guarantee->created_at->diffForHumans() }}</p>
                </div>

                @if($guarantee->approved_by)
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 600; color: #666; font-size: 13px; display: block; margin-bottom: 5px;">اعتمد بواسطة</label>
                        <p style="margin: 0; font-size: 14px;">{{ $guarantee->approver->name }}</p>
                        <p style="margin: 0; font-size: 12px; color: #999;">{{ $guarantee->approved_at->diffForHumans() }}</p>
                    </div>
                @endif

                <div>
                    <label style="font-weight: 600; color: #666; font-size: 13px; display: block; margin-bottom: 5px;">آخر تحديث</label>
                    <p style="margin: 0; font-size: 14px;">{{ $guarantee->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Renewals -->
            @if($guarantee->renewals->count() > 0)
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px;">سجل التجديدات ({{ $guarantee->renewals->count() }})</h3>
                    
                    @foreach($guarantee->renewals as $renewal)
                        <div style="border-bottom: 1px solid #f0f0f0; padding: 10px 0; margin-bottom: 10px;">
                            <p style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600;">{{ $renewal->renewal_date->format('Y-m-d') }}</p>
                            <p style="margin: 0; font-size: 13px; color: #666;">من {{ $renewal->old_expiry_date->format('Y-m-d') }} إلى {{ $renewal->new_expiry_date->format('Y-m-d') }}</p>
                            @if($renewal->renewal_charges > 0)
                                <p style="margin: 5px 0 0 0; font-size: 12px; color: #0071e3;">رسوم: {{ number_format($renewal->renewal_charges, 2) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Releases -->
            @if($guarantee->releases->count() > 0)
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px;">سجل التحرير ({{ $guarantee->releases->count() }})</h3>
                    
                    @foreach($guarantee->releases as $release)
                        <div style="border-bottom: 1px solid #f0f0f0; padding: 10px 0; margin-bottom: 10px;">
                            <p style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600;">{{ $release->release_date->format('Y-m-d') }}</p>
                            <p style="margin: 0; font-size: 13px; color: #666;">{{ $release->release_type == 'full' ? 'تحرير كلي' : 'تحرير جزئي' }}</p>
                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #0071e3;">المبلغ: {{ number_format($release->released_amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Claims -->
            @if($guarantee->claims->count() > 0)
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px;">المطالبات ({{ $guarantee->claims->count() }})</h3>
                    
                    @foreach($guarantee->claims as $claim)
                        <div style="border-bottom: 1px solid #f0f0f0; padding: 10px 0; margin-bottom: 10px;">
                            <p style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600;">{{ $claim->claim_date->format('Y-m-d') }}</p>
                            <p style="margin: 0; font-size: 13px; color: #666;">{{ $claim->claim_reason }}</p>
                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #dc3545;">المبلغ: {{ number_format($claim->claimed_amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
