@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>المعالم الرئيسية - {{ $project->name }}</h1>
        <a href="{{ route('projects.show', $project) }}" 
           style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            العودة للمشروع
        </a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">قائمة المعالم</h3>
        
        @forelse($milestones as $milestone)
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h4 style="margin: 0 0 5px 0;">{{ $milestone->name }}</h4>
                    @if($milestone->description)
                        <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">{{ $milestone->description }}</p>
                    @endif
                    <div style="display: flex; gap: 15px; font-size: 13px; color: #666;">
                        <span>التاريخ المستهدف: {{ $milestone->target_date->format('Y-m-d') }}</span>
                        @if($milestone->actual_date)
                            <span>التاريخ الفعلي: {{ $milestone->actual_date->format('Y-m-d') }}</span>
                        @endif
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                 @if($milestone->status == 'achieved') background: #d4edda; color: #155724;
                                 @elseif($milestone->status == 'pending') background: #fff3cd; color: #856404;
                                 @else background: #f8d7da; color: #721c24;
                                 @endif">
                        @switch($milestone->status)
                            @case('pending') قيد الانتظار @break
                            @case('achieved') تم الإنجاز @break
                            @case('delayed') متأخر @break
                            @case('missed') فائت @break
                        @endswitch
                    </span>
                    @if($milestone->is_critical)
                        <span style="padding: 4px 12px; background: #dc3545; color: white; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            حرج
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #666; padding: 40px;">لا توجد معالم مسجلة</p>
        @endforelse
    </div>
</div>
@endsection
