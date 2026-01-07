@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0 0 5px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #666;">{{ $project->project_number }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('projects.edit', $project) }}" 
               style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                تعديل
            </a>
            <a href="{{ route('projects.index') }}" 
               style="background: #f8f9fa; color: #666; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                العودة للقائمة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">نسبة الإنجاز</p>
            <h2 style="margin: 0; color: #0071e3;">{{ number_format($project->physical_progress, 1) }}%</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">قيمة المشروع</p>
            <h2 style="margin: 0; color: #0071e3;">{{ number_format($project->revised_contract_value, 0) }}</h2>
            <p style="margin: 0; font-size: 12px; color: #999;">{{ $project->currency }}</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الحالة</p>
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
                         @if($project->status == 'in_progress') background: #d4edda; color: #155724;
                         @elseif($project->status == 'not_started') background: #f8f9fa; color: #6c757d;
                         @elseif($project->status == 'completed') background: #d1ecf1; color: #0c5460;
                         @else background: #fff3cd; color: #856404;
                         @endif">
                @switch($project->status)
                    @case('not_started') لم يبدأ @break
                    @case('mobilization') تجهيز الموقع @break
                    @case('in_progress') قيد التنفيذ @break
                    @case('completed') منتهي @break
                    @default {{ $project->status }}
                @endswitch
            </span>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الصحة</p>
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
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
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- Project Info -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; padding-bottom: 15px; border-bottom: 2px solid #0071e3;">معلومات المشروع</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">العميل</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">{{ $project->client->name }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">الموقع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">{{ $project->city }}, {{ $project->country }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">تاريخ البدء</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">{{ $project->commencement_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">تاريخ الانتهاء المخطط</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">{{ $project->original_completion_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">النوع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            @switch($project->type)
                                @case('building') مباني @break
                                @case('infrastructure') بنية تحتية @break
                                @case('industrial') صناعي @break
                                @case('maintenance') صيانة @break
                                @case('fit_out') تشطيبات @break
                                @default {{ $project->type }}
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">التصنيف</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            @switch($project->category)
                                @case('new_construction') إنشاء جديد @break
                                @case('renovation') تجديد @break
                                @case('expansion') توسعة @break
                                @case('maintenance') صيانة @break
                                @default {{ $project->category }}
                            @endswitch
                        </p>
                    </div>
                </div>

                @if($project->description)
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                        <p style="margin: 0; color: #666; font-size: 14px;">الوصف</p>
                        <p style="margin: 10px 0 0 0;">{{ $project->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Team -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">الفريق</h3>
                    <a href="{{ route('projects.team', $project) }}" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        عرض الكل →
                    </a>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    @if($project->projectManager)
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مدير المشروع</p>
                            <p style="margin: 0; font-weight: 600;">{{ $project->projectManager->name }}</p>
                        </div>
                    @endif
                    
                    @if($project->siteEngineer)
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مهندس الموقع</p>
                            <p style="margin: 0; font-weight: 600;">{{ $project->siteEngineer->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Progress Reports -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">آخر تقارير التقدم</h3>
                    <a href="{{ route('projects.progress', $project) }}" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        عرض الكل →
                    </a>
                </div>
                
                @forelse($project->progressReports as $report)
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <p style="margin: 0; font-weight: 600;">تقرير #{{ $report->report_number }}</p>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">{{ $report->report_date->format('Y-m-d') }}</p>
                            </div>
                            <span style="padding: 4px 12px; background: white; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                {{ number_format($report->physical_progress, 1) }}%
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: #666; padding: 20px;">لا توجد تقارير تقدم حتى الآن</p>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0;">إجراءات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('projects.progress', $project) }}" 
                       style="padding: 12px; background: #0071e3; color: white; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        إضافة تقرير تقدم
                    </a>
                    <a href="{{ route('projects.milestones', $project) }}" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        المعالم الرئيسية
                    </a>
                    <a href="{{ route('projects.issues', $project) }}" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        المشاكل
                    </a>
                    <a href="{{ route('projects.team', $project) }}" 
                       style="padding: 12px; background: #f8f9fa; color: #333; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                        الفريق
                    </a>
                </div>
            </div>

            <!-- Milestones -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">المعالم القادمة</h3>
                    <a href="{{ route('projects.milestones', $project) }}" style="color: #0071e3; text-decoration: none; font-size: 14px;">
                        الكل →
                    </a>
                </div>
                
                @forelse($project->milestones->where('status', 'pending')->take(3) as $milestone)
                    <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">{{ $milestone->name }}</p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">{{ $milestone->target_date->format('Y-m-d') }}</p>
                    </div>
                @empty
                    <p style="text-align: center; color: #666; font-size: 14px; padding: 15px;">لا توجد معالم</p>
                @endforelse
            </div>

            <!-- Open Issues -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">المشاكل المفتوحة</h3>
                    <span style="padding: 4px 10px; background: #f8d7da; color: #721c24; border-radius: 12px; font-size: 13px; font-weight: 600;">
                        {{ $project->issues->whereIn('status', ['open', 'in_progress'])->count() }}
                    </span>
                </div>
                
                @forelse($project->issues->take(3) as $issue)
                    <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px;">
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">{{ $issue->title }}</p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">{{ $issue->identified_date->format('Y-m-d') }}</p>
                    </div>
                @empty
                    <p style="text-align: center; color: #666; font-size: 14px; padding: 15px;">لا توجد مشاكل</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
