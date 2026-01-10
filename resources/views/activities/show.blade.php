@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">
                {{ $activity->activity_code }} - {{ $activity->name }}
            </h1>
            <p style="color: #86868b;">{{ $activity->wbs ? $activity->wbs->getFullPath() : '' }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('activities.progress-update', $activity) }}" style="background: #34c759; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="trending-up" style="width: 18px; height: 18px;"></i>
                تحديث التقدم
            </a>
            <a href="{{ route('activities.edit', $activity) }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">تعديل</a>
            <a href="{{ route('activities.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">رجوع</a>
        </div>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Main Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- Left Column -->
        <div>
            <!-- Basic Info -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">معلومات أساسية</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">الكود</div>
                        <div style="font-weight: 600; font-family: monospace;">{{ $activity->activity_code }}</div>
                    </div>

                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">النوع</div>
                        <div style="font-weight: 600;">
                            @if($activity->type == 'task') مهمة
                            @elseif($activity->type == 'milestone') معلم
                            @else ملخص
                            @endif
                        </div>
                    </div>

                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">الحالة</div>
                        <span style="background: {{ $activity->status_color }}; color: white; padding: 6px 14px; border-radius: 12px; font-size: 0.9rem; font-weight: 500; display: inline-block;">
                            @if($activity->status == 'not_started') لم يبدأ
                            @elseif($activity->status == 'in_progress') قيد التنفيذ
                            @elseif($activity->status == 'completed') مكتمل
                            @elseif($activity->status == 'on_hold') معلق
                            @else ملغي
                            @endif
                        </span>
                    </div>

                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">الأولوية</div>
                        <span style="background: {{ $activity->priority_color }}; color: white; padding: 6px 14px; border-radius: 12px; font-size: 0.9rem; font-weight: 500; display: inline-block;">
                            @if($activity->priority == 'low') منخفضة
                            @elseif($activity->priority == 'medium') متوسطة
                            @elseif($activity->priority == 'high') عالية
                            @else حرجة
                            @endif
                        </span>
                    </div>

                    @if($activity->responsible)
                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">المسؤول</div>
                        <div style="font-weight: 600;">{{ $activity->responsible->name }}</div>
                    </div>
                    @endif

                    <div>
                        <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">حرج؟</div>
                        @if($activity->is_critical)
                        <span style="color: #ff3b30; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                            <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                            نعم - نشاط حرج
                        </span>
                        @else
                        <span style="color: #34c759; font-weight: 600;">لا</span>
                        @endif
                    </div>
                </div>

                @if($activity->description)
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                    <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 5px;">الوصف</div>
                    <div style="line-height: 1.6;">{{ $activity->description }}</div>
                </div>
                @endif
            </div>

            <!-- Timeline -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">الجدول الزمني</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <!-- Planned -->
                    <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                        <div style="font-weight: 600; margin-bottom: 15px; color: #0071e3;">المخطط</div>
                        
                        <div style="margin-bottom: 12px;">
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">البداية</div>
                            <div style="font-weight: 600;">{{ $activity->planned_start_date->format('Y-m-d') }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">النهاية</div>
                            <div style="font-weight: 600;">{{ $activity->planned_end_date->format('Y-m-d') }}</div>
                        </div>

                        <div>
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">المدة</div>
                            <div style="font-weight: 600;">{{ $activity->planned_duration_days }} يوم</div>
                        </div>
                    </div>

                    <!-- Actual -->
                    <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                        <div style="font-weight: 600; margin-bottom: 15px; color: #34c759;">الفعلي</div>
                        
                        <div style="margin-bottom: 12px;">
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">البداية</div>
                            <div style="font-weight: 600;">{{ $activity->actual_start_date ? $activity->actual_start_date->format('Y-m-d') : '-' }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">النهاية</div>
                            <div style="font-weight: 600;">{{ $activity->actual_end_date ? $activity->actual_end_date->format('Y-m-d') : '-' }}</div>
                        </div>

                        <div>
                            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">المدة</div>
                            <div style="font-weight: 600;">{{ $activity->actual_duration_days ?? '-' }} {{ $activity->actual_duration_days ? 'يوم' : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dependencies -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">التبعيات</h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="font-weight: 600; margin-bottom: 10px; color: #0071e3;">الأنشطة السابقة ({{ $activity->predecessors->count() }})</div>
                    @if($activity->predecessors->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($activity->predecessors as $predecessor)
                        <div style="background: #f5f5f7; padding: 12px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-family: monospace; font-weight: 600; color: #0071e3;">{{ $predecessor->activity_code }}</span>
                                - {{ $predecessor->name }}
                            </div>
                            <span style="background: white; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                {{ $predecessor->pivot->type }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="color: #86868b; font-size: 0.9rem;">لا توجد أنشطة سابقة</p>
                    @endif
                </div>

                <div>
                    <div style="font-weight: 600; margin-bottom: 10px; color: #34c759;">الأنشطة اللاحقة ({{ $activity->successors->count() }})</div>
                    @if($activity->successors->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($activity->successors as $successor)
                        <div style="background: #f5f5f7; padding: 12px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-family: monospace; font-weight: 600; color: #34c759;">{{ $successor->activity_code }}</span>
                                - {{ $successor->name }}
                            </div>
                            <span style="background: white; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                {{ $successor->pivot->type }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="color: #86868b; font-size: 0.9rem;">لا توجد أنشطة لاحقة</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Progress -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px;">نسبة الإنجاز</h3>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 3rem; font-weight: 700; background: linear-gradient(135deg, #0071e3, #34c759); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        {{ number_format($activity->progress_percent, 0) }}%
                    </div>
                </div>

                <div style="height: 12px; background: #f0f0f0; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
                    <div style="height: 100%; background: linear-gradient(90deg, #34c759, #0071e3); width: {{ $activity->progress_percent }}%; transition: width 0.3s;"></div>
                </div>

                <div style="color: #86868b; font-size: 0.85rem; text-align: center;">
                    طريقة الحساب: 
                    @if($activity->progress_method == 'manual') يدوي
                    @elseif($activity->progress_method == 'duration') بناء على المدة
                    @elseif($activity->progress_method == 'effort') بناء على الجهد
                    @else بناء على الوحدات
                    @endif
                </div>
            </div>

            <!-- Effort -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px;">الجهد (ساعات)</h3>
                
                <div style="margin-bottom: 15px;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المخطط</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0071e3;">{{ number_format($activity->planned_effort_hours, 2) }}</div>
                </div>

                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الفعلي</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #34c759;">{{ number_format($activity->actual_effort_hours, 2) }}</div>
                </div>
            </div>

            <!-- Cost -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px;">التكلفة</h3>
                
                <div style="margin-bottom: 15px;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المخططة</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0071e3;">{{ number_format($activity->budgeted_cost, 2) }}</div>
                </div>

                <div>
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الفعلية</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #34c759;">{{ number_format($activity->actual_cost, 2) }}</div>
                </div>

                @if($activity->budgeted_cost > 0)
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الانحراف</div>
                    @php
                        $variance = $activity->actual_cost - $activity->budgeted_cost;
                        $variancePercent = ($variance / $activity->budgeted_cost) * 100;
                    @endphp
                    <div style="font-size: 1.2rem; font-weight: 700; color: {{ $variance > 0 ? '#ff3b30' : '#34c759' }};">
                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                        <span style="font-size: 0.9rem;">({{ number_format($variancePercent, 1) }}%)</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Milestones -->
            @if($activity->milestones->count() > 0)
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">المعالم المرتبطة</h3>
                
                @foreach($activity->milestones as $milestone)
                <div style="background: #f5f5f7; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                    <div style="font-weight: 600; margin-bottom: 5px;">{{ $milestone->name }}</div>
                    <div style="color: #86868b; font-size: 0.85rem;">
                        التاريخ المستهدف: {{ $milestone->target_date->format('Y-m-d') }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
