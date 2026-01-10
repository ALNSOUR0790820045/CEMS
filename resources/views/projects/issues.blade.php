@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>المشاكل - {{ $project->name }}</h1>
        <a href="{{ route('projects.show', $project) }}" 
           style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            العودة للمشروع
        </a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">قائمة المشاكل</h3>
        
        @forelse($issues as $issue)
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0;">{{ $issue->title }}</h4>
                        <p style="margin: 0; color: #666; font-size: 13px;">{{ $issue->issue_number }}</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                     @if($issue->severity == 'low') background: #d1ecf1; color: #0c5460;
                                     @elseif($issue->severity == 'medium') background: #fff3cd; color: #856404;
                                     @elseif($issue->severity == 'high') background: #f8d7da; color: #721c24;
                                     @else background: #dc3545; color: white;
                                     @endif">
                            @switch($issue->severity)
                                @case('low') منخفضة @break
                                @case('medium') متوسطة @break
                                @case('high') عالية @break
                                @case('critical') حرجة @break
                            @endswitch
                        </span>
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                                     @if($issue->status == 'open') background: #fff3cd; color: #856404;
                                     @elseif($issue->status == 'in_progress') background: #d1ecf1; color: #0c5460;
                                     @else background: #d4edda; color: #155724;
                                     @endif">
                            @switch($issue->status)
                                @case('open') مفتوحة @break
                                @case('in_progress') قيد المعالجة @break
                                @case('resolved') محلولة @break
                                @case('closed') مغلقة @break
                            @endswitch
                        </span>
                    </div>
                </div>
                
                <p style="margin: 0 0 10px 0;">{{ $issue->description }}</p>
                
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #666; border-top: 1px solid #dee2e6; padding-top: 10px;">
                    <span>تم التحديد: {{ $issue->identified_date->format('Y-m-d') }}</span>
                    @if($issue->assignedTo)
                        <span>مسند إلى: {{ $issue->assignedTo->name }}</span>
                    @endif
                    <span>بواسطة: {{ $issue->reportedBy->name }}</span>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #666; padding: 40px;">لا توجد مشاكل مسجلة</p>
        @endforelse

        @if($issues->hasPages())
            <div style="margin-top: 20px;">
                {{ $issues->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
