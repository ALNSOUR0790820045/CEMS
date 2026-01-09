@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <h1 style="margin-bottom: 30px;">محفظة المشاريع</h1>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">إجمالي المشاريع</p>
            <h2 style="margin: 0; color: #0071e3;">{{ $stats['total_projects'] }}</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">المشاريع النشطة</p>
            <h2 style="margin: 0; color: #28a745;">{{ $stats['active_projects'] }}</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">المشاريع المكتملة</p>
            <h2 style="margin: 0; color: #6c757d;">{{ $stats['completed_projects'] }}</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">القيمة الإجمالية</p>
            <h2 style="margin: 0; color: #0071e3; font-size: 20px;">{{ number_format($stats['total_value'], 0) }}</h2>
            <p style="margin: 0; font-size: 12px; color: #999;">SAR</p>
        </div>
    </div>

    <!-- Active Projects -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">المشاريع النشطة</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            @forelse($projects as $project)
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="margin-bottom: 15px;">
                        <h4 style="margin: 0 0 5px 0;">{{ $project->name }}</h4>
                        <p style="margin: 0; color: #666; font-size: 13px;">{{ $project->project_number }}</p>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px;">
                            <span>التقدم</span>
                            <span style="font-weight: 600;">{{ number_format($project->physical_progress, 1) }}%</span>
                        </div>
                        <div style="background: #e9ecef; height: 6px; border-radius: 3px; overflow: hidden;">
                            <div style="background: #0071e3; height: 100%; width: {{ $project->physical_progress }}%;"></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #666; margin-bottom: 10px;">
                        <span>{{ $project->client->name }}</span>
                        <span>{{ $project->city }}</span>
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <span style="padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
                                     @if($project->health == 'on_track') background: #d4edda; color: #155724;
                                     @elseif($project->health == 'at_risk') background: #fff3cd; color: #856404;
                                     @else background: #f8d7da; color: #721c24;
                                     @endif">
                            @switch($project->health)
                                @case('on_track') في المسار @break
                                @case('at_risk') في خطر @break
                                @case('delayed') متأخر @break
                                @case('critical') حرج @break
                            @endswitch
                        </span>
                        <a href="{{ route('projects.show', $project) }}" 
                           style="padding: 4px 10px; background: #0071e3; color: white; border-radius: 12px; font-size: 11px; font-weight: 600; text-decoration: none;">
                            عرض
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="color: #666;">لا توجد مشاريع نشطة</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
