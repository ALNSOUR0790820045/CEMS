@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">المعالم الرئيسية</h1>
            <p style="color: #86868b;">متابعة المعالم المهمة في المشاريع ({{ $milestones->total() }} معلم)</p>
        </div>
        <a href="{{ route('activities.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            رجوع للأنشطة
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('milestones.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->project_code }} - {{ $project->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="achieved" {{ request('status') == 'achieved' ? 'selected' : '' }}>تم التحقيق</option>
                    <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>فات الموعد</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">النوع</label>
                <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأنواع</option>
                    <option value="project" {{ request('type') == 'project' ? 'selected' : '' }}>مشروع</option>
                    <option value="contractual" {{ request('type') == 'contractual' ? 'selected' : '' }}>تعاقدي</option>
                    <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>دفع</option>
                    <option value="technical" {{ request('type') == 'technical' ? 'selected' : '' }}>تقني</option>
                </select>
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">بحث</button>
                <a href="{{ route('milestones.index') }}" style="flex: 1; background: #f5f5f7; color: #1d1d1f; padding: 10px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600;">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <!-- Add Milestone Form -->
    <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px;">إضافة معلم جديد</h3>
        
        <form method="POST" action="{{ route('milestones.store') }}">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                    <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_code }} - {{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المعلم *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التاريخ المستهدف *</label>
                    <input type="date" name="target_date" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع *</label>
                    <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="project">مشروع</option>
                        <option value="contractual">تعاقدي</option>
                        <option value="payment">دفع</option>
                        <option value="technical">تقني</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة *</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <option value="pending">قيد الانتظار</option>
                        <option value="achieved">تم التحقيق</option>
                        <option value="missed">فات الموعد</option>
                    </select>
                </div>

                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إضافة</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Milestones Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($milestones->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المشروع</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">النوع</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">التاريخ المستهدف</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">التاريخ الفعلي</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($milestones as $milestone)
                <tr style="border-bottom: 1px solid #f0f0f0; {{ $milestone->is_critical ? 'background: #fff5f5;' : '' }}">
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($milestone->is_critical)
                            <i data-lucide="alert-circle" style="width: 16px; height: 16px; color: #ff3b30;"></i>
                            @endif
                            <div>
                                <div style="font-weight: 600; margin-bottom: 3px;">{{ $milestone->name }}</div>
                                @if($milestone->description)
                                <div style="color: #86868b; font-size: 0.85rem;">{{ Str::limit($milestone->description, 60) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #86868b;">
                        {{ $milestone->project->project_code }}
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #f5f5f7; color: #1d1d1f; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                            {{ $milestone->type_label }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center; font-weight: 600;">
                        {{ $milestone->target_date->format('Y-m-d') }}
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        {{ $milestone->actual_date ? $milestone->actual_date->format('Y-m-d') : '-' }}
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: {{ $milestone->status_color }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                            {{ $milestone->status_label }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <form method="POST" action="{{ route('milestones.destroy', $milestone) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المعلم؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="padding: 20px; border-top: 1px solid #f0f0f0;">
            {{ $milestones->links() }}
        </div>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="flag" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد معالم</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة معلم جديد</p>
        </div>
        @endif
    </div>

    <!-- Timeline View (upcoming milestones) -->
    @php
        $upcomingMilestones = $milestones->where('status', 'pending')->where('target_date', '>=', now())->take(5);
    @endphp
    @if($upcomingMilestones->count() > 0)
    <div style="background: white; border-radius: 12px; padding: 25px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="calendar" style="width: 24px; height: 24px; color: #ff9500;"></i>
            المعالم القريبة
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach($upcomingMilestones as $milestone)
            @php
                $daysUntil = now()->diffInDays($milestone->target_date, false);
            @endphp
            <div style="background: {{ $daysUntil <= 7 ? '#fff5f5' : '#f5f5f7' }}; padding: 15px; border-radius: 8px; border-right: 4px solid {{ $daysUntil <= 7 ? '#ff3b30' : '#0071e3' }};">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 5px;">{{ $milestone->name }}</div>
                        <div style="color: #86868b; font-size: 0.85rem;">{{ $milestone->project->name }}</div>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-weight: 600; color: {{ $daysUntil <= 7 ? '#ff3b30' : '#0071e3' }};">
                            {{ $milestone->target_date->format('Y-m-d') }}
                        </div>
                        <div style="color: #86868b; font-size: 0.85rem;">
                            @if($daysUntil == 0) اليوم
                            @elseif($daysUntil == 1) غداً
                            @elseif($daysUntil < 0) متأخر {{ abs($daysUntil) }} يوم
                            @else بعد {{ $daysUntil }} يوم
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
