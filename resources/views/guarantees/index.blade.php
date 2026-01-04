@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0 0 10px 0;">خطابات الضمان</h1>
            <p style="color: #666; margin: 0;">إدارة خطابات الضمان البنكية</p>
        </div>
        <a href="{{ route('guarantees.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            خطاب ضمان جديد
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Quick Links -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <a href="{{ route('guarantees.expiring') }}" style="background: white; padding: 20px; border-radius: 10px; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; display: block;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background: #fff3cd; padding: 12px; border-radius: 8px; display: flex;">
                    <i data-lucide="alert-triangle" style="width: 24px; height: 24px; color: #856404;"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1d1d1f;">{{ App\Models\Guarantee::expiring(30)->count() }}</div>
                    <div style="color: #666; font-size: 14px;">قريبة من الانتهاء</div>
                </div>
            </div>
        </a>

        <a href="{{ route('guarantees.statistics') }}" style="background: white; padding: 20px; border-radius: 10px; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; display: block;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background: #d1ecf1; padding: 12px; border-radius: 8px; display: flex;">
                    <i data-lucide="bar-chart-3" style="width: 24px; height: 24px; color: #0c5460;"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1d1d1f;">{{ App\Models\Guarantee::where('status', 'active')->count() }}</div>
                    <div style="color: #666; font-size: 14px;">خطابات نشطة</div>
                </div>
            </div>
        </a>

        <a href="{{ route('guarantees.reports') }}" style="background: white; padding: 20px; border-radius: 10px; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; display: block;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background: #d4edda; padding: 12px; border-radius: 8px; display: flex;">
                    <i data-lucide="file-text" style="width: 24px; height: 24px; color: #155724;"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1d1d1f;">{{ number_format(App\Models\Guarantee::where('status', 'active')->sum('amount'), 0) }}</div>
                    <div style="color: #666; font-size: 14px;">إجمالي المبالغ (ر.س)</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('guarantees.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الخطاب، المستفيد..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">النوع</label>
                <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="bid" {{ request('type') == 'bid' ? 'selected' : '' }}>ضمان ابتدائي</option>
                    <option value="performance" {{ request('type') == 'performance' ? 'selected' : '' }}>ضمان حسن التنفيذ</option>
                    <option value="advance_payment" {{ request('type') == 'advance_payment' ? 'selected' : '' }}>ضمان الدفعة المقدمة</option>
                    <option value="maintenance" {{ request('type') == 'maintenance' ? 'selected' : '' }}>ضمان الصيانة</option>
                    <option value="retention" {{ request('type') == 'retention' ? 'selected' : '' }}>ضمان الاحتجاز</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                    <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>محرر</option>
                    <option value="renewed" {{ request('status') == 'renewed' ? 'selected' : '' }}>مجدد</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">البنك</label>
                <select name="bank_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; flex: 1;">بحث</button>
                <a href="{{ route('guarantees.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; text-decoration: none; text-align: center;">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- Guarantees Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">رقم الخطاب</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">البنك</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">المستفيد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">المبلغ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">تاريخ الانتهاء</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guarantees as $guarantee)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            <a href="{{ route('guarantees.show', $guarantee) }}" style="color: #0071e3; text-decoration: none; font-weight: 600;">{{ $guarantee->guarantee_number }}</a>
                        </td>
                        <td style="padding: 15px;">{{ $guarantee->type_name }}</td>
                        <td style="padding: 15px;">{{ $guarantee->bank->name }}</td>
                        <td style="padding: 15px;">{{ $guarantee->beneficiary }}</td>
                        <td style="padding: 15px; direction: ltr; text-align: right;">{{ number_format($guarantee->amount, 2) }} {{ $guarantee->currency }}</td>
                        <td style="padding: 15px;">
                            {{ $guarantee->expiry_date->format('Y-m-d') }}
                            @if($guarantee->is_expiring)
                                <span style="background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px;">قريب</span>
                            @elseif($guarantee->is_expired)
                                <span style="background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px;">منتهي</span>
                            @endif
                        </td>
                        <td style="padding: 15px;">
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
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('guarantees.show', $guarantee) }}" style="color: #0071e3; text-decoration: none;" title="عرض">
                                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                                </a>
                                <a href="{{ route('guarantees.edit', $guarantee) }}" style="color: #0071e3; text-decoration: none;" title="تعديل">
                                    <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                                </a>
                                <form method="POST" action="{{ route('guarantees.destroy', $guarantee) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;" title="حذف">
                                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                            <p style="margin: 0;">لا توجد خطابات ضمان</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($guarantees->hasPages())
            <div style="padding: 20px; border-top: 1px solid #f0f0f0;">
                {{ $guarantees->links() }}
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
