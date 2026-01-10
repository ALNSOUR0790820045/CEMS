@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">تفاصيل أمر التغيير: {{ $variationOrder->vo_number }}</h1>
        <div style="display: flex; gap: 10px;">
            @if(in_array($variationOrder->status, ['draft', 'identified']))
                <a href="{{ route('variation-orders.edit', $variationOrder) }}" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    تعديل
                </a>
            @endif
            @if($variationOrder->status === 'draft')
                <form method="POST" action="{{ route('variation-orders.submit', $variationOrder) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        تقديم للمراجعة
                    </button>
                </form>
            @endif
            <a href="{{ route('variation-orders.export', $variationOrder) }}" target="_blank" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                طباعة/تصدير PDF
            </a>
            <a href="{{ route('variation-orders.index') }}" style="padding: 10px 20px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; background: white;">
                رجوع
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Info -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <div>
                <h3 style="margin: 0 0 15px 0; font-size: 1.1rem; color: #666;">معلومات أساسية</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">المشروع:</span>
                        <div style="font-weight: 600;">{{ $variationOrder->project->name }}</div>
                    </div>
                    @if($variationOrder->contract)
                        <div>
                            <span style="color: #999; font-size: 0.9rem;">العقد:</span>
                            <div style="font-weight: 600;">{{ $variationOrder->contract->title }}</div>
                        </div>
                    @endif
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">العنوان:</span>
                        <div style="font-weight: 600;">{{ $variationOrder->title }}</div>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">الحالة:</span>
                        <div>
                            @php
                                $statusColors = [
                                    'identified' => '#6c757d',
                                    'draft' => '#17a2b8',
                                    'submitted' => '#ffc107',
                                    'under_review' => '#fd7e14',
                                    'approved' => '#28a745',
                                    'rejected' => '#dc3545',
                                ];
                                $statusLabels = [
                                    'identified' => 'تم تحديده',
                                    'draft' => 'مسودة',
                                    'submitted' => 'مقدم',
                                    'under_review' => 'قيد المراجعة',
                                    'approved' => 'معتمد',
                                    'rejected' => 'مرفوض',
                                ];
                            @endphp
                            <span style="background: {{ $statusColors[$variationOrder->status] ?? '#6c757d' }}; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">
                                {{ $statusLabels[$variationOrder->status] ?? $variationOrder->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin: 0 0 15px 0; font-size: 1.1rem; color: #666;">التصنيف</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">النوع:</span>
                        <div style="font-weight: 600;">
                            @switch($variationOrder->type)
                                @case('addition') إضافة أعمال @break
                                @case('omission') حذف أعمال @break
                                @case('modification') تعديل أعمال @break
                                @case('substitution') استبدال @break
                            @endswitch
                        </div>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">المصدر:</span>
                        <div style="font-weight: 600;">
                            @switch($variationOrder->source)
                                @case('client') طلب العميل @break
                                @case('consultant') طلب الاستشاري @break
                                @case('contractor') طلب المقاول @break
                                @case('design_change') تغيير التصميم @break
                                @case('site_condition') ظروف الموقع @break
                            @endswitch
                        </div>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">الأولوية:</span>
                        <div style="font-weight: 600;">
                            @php
                                $priorityLabels = [
                                    'low' => 'منخفضة',
                                    'medium' => 'متوسطة',
                                    'high' => 'عالية',
                                    'critical' => 'حرجة',
                                ];
                            @endphp
                            {{ $priorityLabels[$variationOrder->priority] }}
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin: 0 0 15px 0; font-size: 1.1rem; color: #666;">القيم المالية</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">القيمة المقدرة:</span>
                        <div style="font-weight: 600; color: #0071e3; font-size: 1.2rem;">
                            {{ number_format($variationOrder->estimated_value, 2) }} {{ $variationOrder->currency }}
                        </div>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">القيمة المعتمدة:</span>
                        <div style="font-weight: 600; color: #28a745; font-size: 1.2rem;">
                            {{ number_format($variationOrder->approved_value, 2) }} {{ $variationOrder->currency }}
                        </div>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">التأثير على المدة:</span>
                        <div style="font-weight: 600;">{{ $variationOrder->time_impact_days }} يوم</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.1rem; color: #666;">الوصف</h3>
            <p style="margin: 0; line-height: 1.6;">{{ $variationOrder->description }}</p>
        </div>

        @if($variationOrder->justification)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.1rem; color: #666;">المبررات</h3>
                <p style="margin: 0; line-height: 1.6;">{{ $variationOrder->justification }}</p>
            </div>
        @endif

        @if($variationOrder->rejection_reason)
            <div style="margin-top: 20px; padding: 15px; background: #f8d7da; border-radius: 8px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.1rem; color: #721c24;">سبب الرفض</h3>
                <p style="margin: 0; color: #721c24;">{{ $variationOrder->rejection_reason }}</p>
            </div>
        @endif
    </div>

    <!-- Workflow Actions -->
    @if(in_array($variationOrder->status, ['submitted', 'under_review', 'negotiating']))
        <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
            <h2 style="margin: 0 0 20px 0;">إجراءات الموافقة</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Approve Form -->
                <form method="POST" action="{{ route('variation-orders.approve', $variationOrder) }}" style="border: 1px solid #28a745; padding: 20px; border-radius: 8px;">
                    @csrf
                    <h3 style="margin: 0 0 15px 0; color: #28a745;">اعتماد أمر التغيير</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">القيمة المعتمدة *</label>
                        <input type="number" name="approved_value" step="0.01" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">أيام التمديد المعتمدة</label>
                        <input type="number" name="approved_extension_days" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
                    </div>
                    <button type="submit" style="background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; width: 100%;">
                        اعتماد
                    </button>
                </form>

                <!-- Reject Form -->
                <form method="POST" action="{{ route('variation-orders.reject', $variationOrder) }}" style="border: 1px solid #dc3545; padding: 20px; border-radius: 8px;">
                    @csrf
                    <h3 style="margin: 0 0 15px 0; color: #dc3545;">رفض أمر التغيير</h3>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">سبب الرفض *</label>
                        <textarea name="rejection_reason" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
                    </div>
                    <button type="submit" style="background: #dc3545; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; width: 100%;">
                        رفض
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Timeline -->
    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <h2 style="margin: 0 0 20px 0;">السجل الزمني</h2>
        @forelse($variationOrder->timeline as $entry)
            <div style="padding: 15px; border-right: 3px solid #0071e3; margin-bottom: 15px; background: #f5f5f7; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <strong>{{ $entry->action }}</strong>
                    <span style="color: #999; font-size: 0.9rem;">{{ $entry->created_at->format('Y-m-d H:i') }}</span>
                </div>
                @if($entry->from_status || $entry->to_status)
                    <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">
                        @if($entry->from_status) من: {{ $entry->from_status }} @endif
                        @if($entry->to_status) إلى: {{ $entry->to_status }} @endif
                    </div>
                @endif
                @if($entry->notes)
                    <div style="color: #666;">{{ $entry->notes }}</div>
                @endif
                <div style="color: #999; font-size: 0.85rem; margin-top: 5px;">
                    بواسطة: {{ $entry->performedBy->name }}
                </div>
            </div>
        @empty
            <p style="color: #999; text-align: center; padding: 20px;">لا توجد أحداث مسجلة</p>
        @endforelse
    </div>
</div>
@endsection
