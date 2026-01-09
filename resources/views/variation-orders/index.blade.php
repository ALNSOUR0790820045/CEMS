@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">أوامر التغيير (Variation Orders)</h1>
        <a href="{{ route('variation-orders.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            إضافة أمر تغيير جديد
        </a>
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

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('variation-orders.index') }}" style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>تم تحديده</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدم</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع</label>
                <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأنواع</option>
                    <option value="addition" {{ request('type') == 'addition' ? 'selected' : '' }}>إضافة أعمال</option>
                    <option value="omission" {{ request('type') == 'omission' ? 'selected' : '' }}>حذف أعمال</option>
                    <option value="modification" {{ request('type') == 'modification' ? 'selected' : '' }}>تعديل أعمال</option>
                    <option value="substitution" {{ request('type') == 'substitution' ? 'selected' : '' }}>استبدال</option>
                </select>
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                تصفية
            </button>
            <a href="{{ route('variation-orders.index') }}" style="padding: 12px 30px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; background: white;">
                إعادة تعيين
            </a>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">رقم VO</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">العنوان</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">القيمة المقدرة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الحالة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الأولوية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #ddd;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($variationOrders as $vo)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">{{ $vo->vo_number }}</td>
                        <td style="padding: 15px;">{{ $vo->title }}</td>
                        <td style="padding: 15px;">{{ $vo->project->name }}</td>
                        <td style="padding: 15px;">
                            @switch($vo->type)
                                @case('addition') إضافة @break
                                @case('omission') حذف @break
                                @case('modification') تعديل @break
                                @case('substitution') استبدال @break
                            @endswitch
                        </td>
                        <td style="padding: 15px;">{{ number_format($vo->estimated_value, 2) }} {{ $vo->currency }}</td>
                        <td style="padding: 15px;">
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
                            <span style="background: {{ $statusColors[$vo->status] ?? '#6c757d' }}; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem;">
                                {{ $statusLabels[$vo->status] ?? $vo->status }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            @php
                                $priorityColors = [
                                    'low' => '#28a745',
                                    'medium' => '#ffc107',
                                    'high' => '#fd7e14',
                                    'critical' => '#dc3545',
                                ];
                                $priorityLabels = [
                                    'low' => 'منخفضة',
                                    'medium' => 'متوسطة',
                                    'high' => 'عالية',
                                    'critical' => 'حرجة',
                                ];
                            @endphp
                            <span style="color: {{ $priorityColors[$vo->priority] }}; font-weight: 600;">
                                {{ $priorityLabels[$vo->priority] }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('variation-orders.show', $vo) }}" style="color: #0071e3; text-decoration: none;">عرض</a>
                                @if(in_array($vo->status, ['draft', 'identified']))
                                    <a href="{{ route('variation-orders.edit', $vo) }}" style="color: #28a745; text-decoration: none;">تعديل</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                            لا توجد أوامر تغيير
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $variationOrders->links() }}
    </div>
</div>
@endsection
