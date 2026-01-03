@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">{{ $project->name }}</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('projects.edit', $project) }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">تعديل</a>
            <a href="{{ route('projects.index') }}" style="padding: 12px 30px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-weight: 600;">رجوع</a>
        </div>
    </div>

    <!-- Project Overview Card -->
    <div style="background: white; border-radius: 10px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div>
                <div style="font-size: 0.85rem; color: #999; margin-bottom: 5px;">كود المشروع</div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #0071e3;">{{ $project->project_code }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #999; margin-bottom: 5px;">العميل</div>
                <div style="font-size: 1rem; font-weight: 600;">{{ $project->client->name }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #999; margin-bottom: 5px;">قيمة العقد</div>
                <div style="font-size: 1.2rem; font-weight: 700;">{{ number_format($project->contract_value, 2) }} {{ $project->currency->code }}</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #999; margin-bottom: 5px;">الحالة</div>
                <div>
                    @if($project->project_status == 'execution')
                        <span style="background: #e0f2f1; color: #00796b; padding: 6px 16px; border-radius: 12px; font-size: 0.9rem;">تنفيذ</span>
                    @elseif($project->project_status == 'completed')
                        <span style="background: #d1f4e0; color: #0c6b3f; padding: 6px 16px; border-radius: 12px; font-size: 0.9rem;">مكتمل</span>
                    @else
                        <span style="background: #f5f5f7; color: #666; padding: 6px 16px; border-radius: 12px; font-size: 0.9rem;">{{ $project->project_status }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details -->
    <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #0071e3;">تفاصيل المشروع</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: #1d1d1f;">معلومات العقد</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">تاريخ البدء:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->contract_start_date->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">تاريخ الانتهاء:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->contract_end_date->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">مدة العقد:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->contract_duration_days }} يوم</span>
                    </div>
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">نوع المشروع:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->project_type }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: #1d1d1f;">الموقع</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @if($project->country)
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">الدولة:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->country->name }}</span>
                    </div>
                    @endif
                    @if($project->city)
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">المدينة:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->city->name }}</span>
                    </div>
                    @endif
                    @if($project->site_address)
                    <div>
                        <span style="color: #999; font-size: 0.9rem;">العنوان:</span>
                        <span style="font-weight: 600; margin-right: 10px;">{{ $project->site_address }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #f5f5f7;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: #1d1d1f;">فريق العمل</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <span style="color: #999; font-size: 0.9rem;">مدير المشروع:</span>
                    <span style="font-weight: 600; margin-right: 10px;">{{ $project->projectManager->name }}</span>
                </div>
                @if($project->siteEngineer)
                <div>
                    <span style="color: #999; font-size: 0.9rem;">مهندس الموقع:</span>
                    <span style="font-weight: 600; margin-right: 10px;">{{ $project->siteEngineer->name }}</span>
                </div>
                @endif
                @if($project->contractManager)
                <div>
                    <span style="color: #999; font-size: 0.9rem;">مدير العقود:</span>
                    <span style="font-weight: 600; margin-right: 10px;">{{ $project->contractManager->name }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($project->description)
        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #f5f5f7;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 10px; color: #1d1d1f;">وصف المشروع</h3>
            <p style="color: #666; line-height: 1.6;">{{ $project->description }}</p>
        </div>
        @endif

        @if($project->notes)
        <div style="margin-top: 20px;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 10px; color: #1d1d1f;">ملاحظات</h3>
            <p style="color: #666; line-height: 1.6;">{{ $project->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
