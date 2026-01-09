@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">شروط الدفع</h1>
            <p style="color: #86868b;">عرض وإدارة جميع شروط الدفع في النظام</p>
        </div>
        <a href="{{ route('payment-terms.create') }}" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة شرط دفع جديد
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Search -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('payment-terms.index') }}" style="display: flex; gap: 10px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث في شروط الدفع..." 
                style="flex: 1; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-family: 'Cairo', sans-serif; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                بحث
            </button>
            @if(request('search'))
            <a href="{{ route('payment-terms.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">
                إعادة تعيين
            </a>
            @endif
        </form>
    </div>

    <!-- Payment Terms Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($paymentTerms->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">#</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">عدد الأيام</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الوصف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentTerms as $paymentTerm)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $loop->iteration }}</td>
                    <td style="padding: 15px; font-weight: 600;">
                        {{ $paymentTerm->name }}
                        @if($paymentTerm->name_en)
                        <span style="color: #86868b; font-weight: 400; font-size: 0.9rem;">({{ $paymentTerm->name_en }})</span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <span style="background: #f5f5f7; color: #0071e3; padding: 4px 12px; border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                            {{ $paymentTerm->days }} يوم
                        </span>
                    </td>
                    <td style="padding: 15px; color: #86868b;">{{ $paymentTerm->description ?? '-' }}</td>
                    <td style="padding: 15px;">
                        @if($paymentTerm->is_active)
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">نشط</span>
                        @else
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="{{ route('payment-terms.edit', $paymentTerm) }}" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            <form method="POST" action="{{ route('payment-terms.destroy', $paymentTerm) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف شرط الدفع هذا؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="calendar-clock" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد شروط دفع</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة شرط دفع جديد</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
