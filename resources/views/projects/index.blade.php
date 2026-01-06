@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>المشاريع</h1>
        <a href="{{ route('projects.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة مشروع جديد
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
        @forelse($projects as $project)
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;" 
                 onmouseover="this.style.transform='translateY(-4px)'" 
                 onmouseout="this.style.transform='translateY(0)'">
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0 0 5px 0; font-size: 18px;">{{ $project->name }}</h3>
                        <p style="margin: 0; color: #666; font-size: 14px;">{{ $project->project_number }}</p>
                    </div>
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                 @if($project->status == 'in_progress') background: #d4edda; color: #155724;
                                 @elseif($project->status == 'not_started') background: #f8f9fa; color: #6c757d;
                                 @elseif($project->status == 'completed') background: #d1ecf1; color: #0c5460;
                                 @else background: #fff3cd; color: #856404;
                                 @endif">
                        @switch($project->status)
                            @case('not_started') لم يبدأ @break
                            @case('mobilization') تجهيز الموقع @break
                            @case('in_progress') قيد التنفيذ @break
                            @case('on_hold') متوقف @break
                            @case('suspended') معلق @break
                            @case('completed') منتهي @break
                            @case('handed_over') تم التسليم @break
                            @case('closed') مغلق @break
                            @default {{ $project->status }}
                        @endswitch
                    </span>
                </div>

                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin: 0 0 5px 0; font-size: 14px; color: #666;">العميل</p>
                    <p style="margin: 0; font-weight: 600;">{{ $project->client->name }}</p>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 14px; color: #666;">نسبة الإنجاز</span>
                        <span style="font-weight: 600;">{{ number_format($project->physical_progress, 1) }}%</span>
                    </div>
                    <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: #0071e3; height: 100%; width: {{ $project->physical_progress }}%; transition: width 0.3s;"></div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 12px; color: #666;">القيمة</p>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">{{ number_format($project->revised_contract_value, 0) }} {{ $project->currency }}</p>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-size: 12px; color: #666;">المدينة</p>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">{{ $project->city }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px;
                                 @if($project->health == 'on_track') background: #d4edda; color: #155724;
                                 @elseif($project->health == 'at_risk') background: #fff3cd; color: #856404;
                                 @elseif($project->health == 'delayed') background: #f8d7da; color: #721c24;
                                 @else background: #dc3545; color: white;
                                 @endif">
                        @switch($project->health)
                            @case('on_track') في المسار @break
                            @case('at_risk') في خطر @break
                            @case('delayed') متأخر @break
                            @case('critical') حرج @break
                        @endswitch
                    </span>
                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; background: #e9ecef; color: #495057;">
                        @switch($project->priority)
                            @case('low') منخفضة @break
                            @case('medium') متوسطة @break
                            @case('high') عالية @break
                            @case('critical') حرجة @break
                        @endswitch
                    </span>
                </div>

                <div style="display: flex; gap: 10px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                    <a href="{{ route('projects.show', $project) }}" 
                       style="flex: 1; text-align: center; background: #0071e3; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                        عرض
                    </a>
                    <a href="{{ route('projects.edit', $project) }}" 
                       style="flex: 1; text-align: center; background: #6c757d; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                        تعديل
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: white; border-radius: 12px;">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">لا توجد مشاريع حالياً</p>
                <a href="{{ route('projects.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    إضافة مشروع جديد
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
