@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display:  flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0 0 5px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #666;">{{ $project->project_number ??   $project->project_code }}</p>
        </div>
        <div style="display:  flex; gap: 10px;">
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
            <h2 style="margin: 0; color: #0071e3;">{{ number_format($project->physical_progress ??  0, 1) }}%</h2>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color:  #666; font-size: 14px;">قيمة المشروع</p>
            <h2 style="margin: 0; color: #0071e3;">{{ number_format($project->revised_contract_value ?? $project->contract_value, 0) }}</h2>
            <p style="margin: 0; font-size: 12px; color: #999;">{{ $project->currency ??  $project->currency->code ??  'SAR' }}</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الحالة</p>
            @php $status = $project->status ??   $project->project_status; @endphp
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
                         @if(in_array($status, ['in_progress', 'execution'])) background: #d4edda; color: #155724;
                         @elseif(in_array($status, ['not_started', 'tendering'])) background: #f8f9fa; color: #6c757d;
                         @elseif($status == 'completed') background: #d1ecf1; color: #0c5460;
                         @else background: #fff3cd; color: #856404;
                         @endif">
                @switch($status)
                    @case('not_started') لم يبدأ @break
                    @case('tendering') عطاء @break
                    @case('awarded') مرسى @break
                    @case('mobilization') تجهيز الموقع @break
                    @case('execution')
                    @case('in_progress') قيد التنفيذ @break
                    @case('completed') منتهي @break
                    @case('on_hold') متوقف @break
                    @default {{ $status }}
                @endswitch
            </span>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">الصحة</p>
            @if($project->health)
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
                         @if($project->health == 'on_track') background: #d4edda; color:  #155724;
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
            @else
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; background: #f8f9fa; color: #6c757d;">-</span>
            @endif
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
                        <p style="margin: 0; color: #666; font-size:  14px;">العميل</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">{{ $project->client->name }}</p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size: 14px;">الموقع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            {{ $project->city ??  $project->city? ->name }}, {{ $project->country ??  $project->country?->name }}
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color:  #666; font-size: 14px;">تاريخ البدء</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            {{ ($project->commencement_date ??  $project->contract_start_date)->format('Y-m-d') }}
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size:  14px;">تاريخ الانتهاء المخطط</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            {{ ($project->original_completion_date ?? $project->contract_end_date)->format('Y-m-d') }}
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size:  14px;">النوع</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            @php $type = $project->type ??  $project->project_type; @endphp
                            @switch($type)
                                @case('building') مباني @break
                                @case('infrastructure') بنية تحتية @break
                                @case('industrial') صناعي @break
                                @case('maintenance') صيانة @break
                                @case('fit_out') تشطيبات @break
                                @case('lump_sum') مقطوعية @break
                                @case('unit_price') فئة سعر @break
                                @default {{ $type }}
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #666; font-size:  14px;">التصنيف</p>
                        <p style="margin: 5px 0 0 0; font-weight: 600;">
                            @if($project->category)
                                @switch($project->category)
                                    @case('new_construction') إنشاء جديد @break
                                    @case('renovation') تجديد @break
                                    @case('expansion') توسعة @break
                                    @case('maintenance') صيانة @break
                                    @default {{ $project->category }}
                                @endswitch
                            @else
                                -
                            @endif
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
            <div style="background: white; padding:  25px; border-radius:  12px; box-shadow:  0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0;">الفريق</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap:  15px;">
                    @if($project->projectManager)
                        <div style="padding: 15px; background:  #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size:  13px;">مدير المشروع</p>
                            <p style="margin: 0; font-weight: 600;">{{ $project->projectManager->name }}</p>
                        </div>
                    @endif
                    
                    @if($project->siteEngineer)
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مهندس الموقع</p>
                            <p style="margin: 0; font-weight: 600;">{{ $project->siteEngineer->name }}</p>
                        </div>
                    @endif

                    @if($project->contractManager)
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">مدير العقود</p>
                            <p style="margin: 0; font-weight: 600;">{{ $project->contractManager->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Progress Reports (if available) -->
            @if(method_exists($project, 'progressReports'))
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 20px 0;">آخر تقارير التقدم</h3>
                
                @forelse($project->progressReports->take(5) as $report)
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <p style="margin:  0; font-weight: 600;">تقرير #{{ $report->report_number }}</p>
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
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Details Card (from old code) -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0;">تفاصيل إضافية</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">مدة العقد: </span>
                        <span style="font-weight: 600; margin-right: 10px;">
                            {{ $project->contract_duration_days ??  $project->original_duration_days }} يوم
                        </span>
                    </div>
                    @if($project->site_address)
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">العنوان:</span>
                        <span style="font-weight: 600; margin-right:  10px;">{{ $project->site_address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($project->notes)
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0;">ملاحظات</h3>
                <p style="color: #666; line-height: 1.6; font-size: 14px;">{{ $project->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection