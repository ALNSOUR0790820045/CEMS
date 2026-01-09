@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>فريق المشروع - {{ $project->name }}</h1>
        <a href="{{ route('projects.show', $project) }}" 
           style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            العودة للمشروع
        </a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0;">أعضاء الفريق</h3>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            @forelse($team as $member)
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h4 style="margin: 0 0 5px 0;">{{ $member->user->name }}</h4>
                    <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">{{ $member->role }}</p>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #666;">
                        <span>من: {{ $member->start_date->format('Y-m-d') }}</span>
                        @if($member->end_date)
                            <span>إلى: {{ $member->end_date->format('Y-m-d') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="color: #666;">لا يوجد أعضاء في الفريق حتى الآن</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
