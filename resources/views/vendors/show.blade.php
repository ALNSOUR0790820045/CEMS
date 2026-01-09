@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700;">{{ $vendor->name }}</h1>
            <p style="color: #86868b; margin-top: 5px;">{{ $vendor->vendor_code }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if(!$vendor->is_approved)
            <form method="POST" action="{{ route('vendors.approve', $vendor) }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: #34c759; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    اعتماد المورد
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('vendors.reject', $vendor) }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: #ff9500; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    إلغاء الاعتماد
                </button>
            </form>
            @endif
            <a href="{{ route('vendors.edit', $vendor) }}" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                تعديل
            </a>
            <a href="{{ route('vendors.index') }}" style="padding: 10px 20px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; font-weight: 600;">
                رجوع
            </a>
        </div>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Vendor Details -->
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">معلومات المورد</h2>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">نوع المورد</div>
                <div style="font-weight: 600;">
                    @php
                        $typeLabels = [
                            'materials_supplier' => 'مورد مواد',
                            'equipment_supplier' => 'مورد معدات',
                            'services_provider' => 'مزود خدمات',
                            'subcontractor' => 'مقاول باطن',
                            'consultant' => 'استشاري',
                        ];
                    @endphp
                    {{ $typeLabels[$vendor->vendor_type] ?? $vendor->vendor_type }}
                </div>
            </div>

            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">التصنيف</div>
                <div style="font-weight: 600;">
                    @php
                        $categoryLabels = [
                            'strategic' => 'استراتيجي',
                            'preferred' => 'مفضل',
                            'regular' => 'عادي',
                            'blacklisted' => 'محظور',
                        ];
                    @endphp
                    {{ $categoryLabels[$vendor->vendor_category] ?? $vendor->vendor_category }}
                </div>
            </div>

            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">الحالة</div>
                <div>
                    @if($vendor->is_approved)
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; background: #d4edda; color: #155724;">معتمد</span>
                    @else
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; background: #fff3cd; color: #856404;">قيد الانتظار</span>
                    @endif
                    @if($vendor->is_active)
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; background: #d4edda; color: #155724; margin-right: 5px;">نشط</span>
                    @else
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; background: #f8d7da; color: #721c24; margin-right: 5px;">غير نشط</span>
                    @endif
                </div>
            </div>

            @if($vendor->commercial_registration)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">السجل التجاري</div>
                <div style="font-weight: 600;">{{ $vendor->commercial_registration }}</div>
            </div>
            @endif

            @if($vendor->tax_number)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">الرقم الضريبي</div>
                <div style="font-weight: 600;">{{ $vendor->tax_number }}</div>
            </div>
            @endif

            @if($vendor->license_number)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">رقم الترخيص</div>
                <div style="font-weight: 600;">{{ $vendor->license_number }}</div>
            </div>
            @endif

            @if($vendor->phone)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">الهاتف</div>
                <div style="font-weight: 600;">{{ $vendor->phone }}</div>
            </div>
            @endif

            @if($vendor->mobile)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">الجوال</div>
                <div style="font-weight: 600;">{{ $vendor->mobile }}</div>
            </div>
            @endif

            @if($vendor->email)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">البريد الإلكتروني</div>
                <div style="font-weight: 600;">{{ $vendor->email }}</div>
            </div>
            @endif

            @if($vendor->address)
            <div style="grid-column: span 3;">
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">العنوان</div>
                <div style="font-weight: 600;">{{ $vendor->address }}</div>
            </div>
            @endif

            @if($vendor->notes)
            <div style="grid-column: span 3;">
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">ملاحظات</div>
                <div>{{ $vendor->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Financial Information -->
    @if($vendor->payment_terms || $vendor->credit_limit)
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">المعلومات المالية</h2>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            @if($vendor->payment_terms)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">شروط الدفع</div>
                <div style="font-weight: 600;">
                    @php
                        $paymentTermsLabels = [
                            'cod' => 'نقداً عند التسليم',
                            '7_days' => '7 أيام',
                            '15_days' => '15 يوم',
                            '30_days' => '30 يوم',
                            '45_days' => '45 يوم',
                            '60_days' => '60 يوم',
                            '90_days' => '90 يوم',
                            'custom' => 'مخصص',
                        ];
                    @endphp
                    {{ $paymentTermsLabels[$vendor->payment_terms] ?? $vendor->payment_terms }}
                </div>
            </div>
            @endif

            @if($vendor->credit_limit)
            <div>
                <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">حد الائتمان</div>
                <div style="font-weight: 600;">{{ number_format($vendor->credit_limit, 2) }} ريال</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Contacts, Bank Accounts, Documents, etc. can be added here in future tabs -->
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">بيانات إضافية</h2>
        <p style="color: #86868b;">يمكن إضافة جهات الاتصال، الحسابات البنكية، المستندات، المواد، والتقييمات من خلال وحدات إضافية.</p>
    </div>
</div>
@endsection
