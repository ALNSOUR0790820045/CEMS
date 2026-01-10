@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0;">{{ $dailyReport->report_number }}</h1>
            <p style="color: #666; margin-top: 5px;">{{ $dailyReport->project->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if($dailyReport->status === 'draft')
                <a href="{{ route('daily-reports.edit', $dailyReport) }}" 
                   style="background: #ffc107; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                    تعديل
                </a>
            @endif
            @if($dailyReport->canBeSigned())
                <a href="{{ route('daily-reports.sign', $dailyReport) }}" 
                   style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="pen-tool" style="width: 18px; height: 18px;"></i>
                    توقيع
                </a>
            @endif
            <a href="{{ route('daily-reports.index') }}" 
               style="background: #f5f5f7; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                العودة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status and Signatures -->
    <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">الحالة</h4>
                @php
                    $statusColors = [
                        'draft' => ['bg' => '#f5f5f7', 'color' => '#666'],
                        'submitted' => ['bg' => '#fff3cd', 'color' => '#856404'],
                        'approved' => ['bg' => '#d4edda', 'color' => '#155724'],
                        'rejected' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                    ];
                    $statusLabels = [
                        'draft' => 'مسودة',
                        'submitted' => 'مرسل',
                        'approved' => 'معتمد',
                        'rejected' => 'مرفوض',
                    ];
                @endphp
                <span style="background: {{ $statusColors[$dailyReport->status]['bg'] }}; color: {{ $statusColors[$dailyReport->status]['color'] }}; padding: 6px 16px; border-radius: 16px; font-weight: 600; display: inline-block;">
                    {{ $statusLabels[$dailyReport->status] }}
                </span>
            </div>

            @if($dailyReport->preparedBy)
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">مُعد بواسطة</h4>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                        <div>
                            <div style="font-weight: 600;">{{ $dailyReport->preparedBy->name }}</div>
                            @if($dailyReport->prepared_at)
                                <div style="color: #666; font-size: 0.85rem;">{{ $dailyReport->prepared_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($dailyReport->reviewedBy)
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">مدير المشروع</h4>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                        <div>
                            <div style="font-weight: 600;">{{ $dailyReport->reviewedBy->name }}</div>
                            @if($dailyReport->reviewed_at)
                                <div style="color: #666; font-size: 0.85rem;">{{ $dailyReport->reviewed_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">مدير المشروع</h4>
                    <div style="color: #999;">بانتظار التوقيع</div>
                </div>
            @endif

            @if($dailyReport->consultantApprovedBy)
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">الاستشاري</h4>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                        <div>
                            <div style="font-weight: 600;">{{ $dailyReport->consultantApprovedBy->name }}</div>
                            @if($dailyReport->consultant_approved_at)
                                <div style="color: #666; font-size: 0.85rem;">{{ $dailyReport->consultant_approved_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">الاستشاري</h4>
                    <div style="color: #999;">بانتظار التوقيع</div>
                </div>
            @endif

            @if($dailyReport->clientApprovedBy)
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">العميل</h4>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #28a745; font-size: 1.2rem;">✓</span>
                        <div>
                            <div style="font-weight: 600;">{{ $dailyReport->clientApprovedBy->name }}</div>
                            @if($dailyReport->client_approved_at)
                                <div style="color: #666; font-size: 0.85rem;">{{ $dailyReport->client_approved_at->format('Y-m-d H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div>
                    <h4 style="margin-bottom: 10px; color: #666; font-size: 0.9rem;">العميل</h4>
                    <div style="color: #999;">غير موقع</div>
                </div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- General Information -->
            <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">معلومات عامة</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <div>
                        <div style="color: #666; font-size: 0.9rem;">التاريخ</div>
                        <div style="font-weight: 600;">{{ $dailyReport->report_date->format('Y-m-d') }}</div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 0.9rem;">ساعات العمل</div>
                        <div style="font-weight: 600;">{{ $dailyReport->total_work_hours }} ساعة</div>
                    </div>
                    @if($dailyReport->weather_condition)
                        <div>
                            <div style="color: #666; font-size: 0.9rem;">الحالة الجوية</div>
                            <div style="font-weight: 600;">{{ $dailyReport->weather_condition }}</div>
                        </div>
                    @endif
                    @if($dailyReport->temperature)
                        <div>
                            <div style="color: #666; font-size: 0.9rem;">درجة الحرارة</div>
                            <div style="font-weight: 600;">{{ $dailyReport->temperature }}°C</div>
                        </div>
                    @endif
                    @if($dailyReport->humidity)
                        <div>
                            <div style="color: #666; font-size: 0.9rem;">الرطوبة</div>
                            <div style="font-weight: 600;">{{ $dailyReport->humidity }}%</div>
                        </div>
                    @endif
                    @if($dailyReport->work_start_time && $dailyReport->work_end_time)
                        <div>
                            <div style="color: #666; font-size: 0.9rem;">وقت العمل</div>
                            <div style="font-weight: 600;">{{ $dailyReport->work_start_time }} - {{ $dailyReport->work_end_time }}</div>
                        </div>
                    @endif
                </div>
                @if($dailyReport->site_conditions)
                    <div style="margin-top: 15px;">
                        <div style="color: #666; font-size: 0.9rem;">ظروف الموقع</div>
                        <div style="margin-top: 5px;">{{ $dailyReport->site_conditions }}</div>
                    </div>
                @endif
            </div>

            <!-- Labor -->
            @if($dailyReport->workers_count > 0)
                <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">العمالة</h3>
                    <div style="margin-bottom: 15px;">
                        <div style="color: #666; font-size: 0.9rem;">عدد العمال</div>
                        <div style="font-weight: 600; font-size: 1.5rem; color: #0071e3;">{{ $dailyReport->workers_count }}</div>
                    </div>
                    @if($dailyReport->workers_breakdown)
                        <div style="margin-bottom: 15px;">
                            <div style="color: #666; font-size: 0.9rem; margin-bottom: 8px;">التفصيل</div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                @foreach($dailyReport->workers_breakdown as $type => $count)
                                    <div style="background: #f5f5f7; padding: 10px; border-radius: 6px;">
                                        <div style="color: #666; font-size: 0.85rem;">{{ $type }}</div>
                                        <div style="font-weight: 600;">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($dailyReport->attendance_notes)
                        <div>
                            <div style="color: #666; font-size: 0.9rem;">ملاحظات</div>
                            <div style="margin-top: 5px;">{{ $dailyReport->attendance_notes }}</div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Work Executed -->
            @if($dailyReport->work_executed)
                <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">الأعمال المنفذة</h3>
                    <div style="line-height: 1.8; white-space: pre-wrap;">{{ $dailyReport->work_executed }}</div>
                </div>
            @endif

            <!-- Problems -->
            @if($dailyReport->problems || $dailyReport->delays || $dailyReport->safety_incidents)
                <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #dc3545;">المشاكل والتأخيرات</h3>
                    @if($dailyReport->problems)
                        <div style="margin-bottom: 15px;">
                            <div style="color: #666; font-size: 0.9rem; font-weight: 600;">المشاكل</div>
                            <div style="margin-top: 5px; line-height: 1.8;">{{ $dailyReport->problems }}</div>
                        </div>
                    @endif
                    @if($dailyReport->delays)
                        <div style="margin-bottom: 15px;">
                            <div style="color: #666; font-size: 0.9rem; font-weight: 600;">التأخيرات</div>
                            <div style="margin-top: 5px; line-height: 1.8;">{{ $dailyReport->delays }}</div>
                        </div>
                    @endif
                    @if($dailyReport->safety_incidents)
                        <div>
                            <div style="color: #dc3545; font-size: 0.9rem; font-weight: 600;">⚠️ حوادث السلامة</div>
                            <div style="margin-top: 5px; line-height: 1.8;">{{ $dailyReport->safety_incidents }}</div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Photos Gallery -->
            @if($dailyReport->photos->count() > 0)
                <div style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0071e3;">
                        الصور ({{ $dailyReport->photos->count() }})
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        @foreach($dailyReport->photos as $photo)
                            <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; cursor: pointer;" onclick="openLightbox('{{ Storage::url($photo->photo_path) }}', '{{ $photo->photo_title }}')">
                                <img src="{{ Storage::url($photo->photo_path) }}" alt="{{ $photo->photo_title }}" 
                                     style="width: 100%; height: 200px; object-fit: cover;">
                                <div style="padding: 10px;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">{{ $photo->photo_title ?? 'صورة' }}</div>
                                    @if($photo->description)
                                        <div style="color: #666; font-size: 0.85rem; margin-bottom: 5px;">{{ Str::limit($photo->description, 50) }}</div>
                                    @endif
                                    <div style="display: flex; gap: 10px; margin-top: 8px; font-size: 0.85rem;">
                                        @if($photo->latitude && $photo->longitude)
                                            <span style="color: #28a745;" title="GPS: {{ $photo->latitude }}, {{ $photo->longitude }}">📍 GPS</span>
                                        @endif
                                        @if($photo->verified)
                                            <span style="color: #0071e3;" title="Hash Verified">✓ Verified</span>
                                        @endif
                                    </div>
                                    <div style="color: #999; font-size: 0.75rem; margin-top: 5px;">
                                        {{ $photo->captured_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Stats -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px;">إحصائيات سريعة</h4>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="border-right: 3px solid #0071e3; padding-right: 12px;">
                        <div style="color: #666; font-size: 0.85rem;">عدد العمال</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #0071e3;">{{ $dailyReport->workers_count }}</div>
                    </div>
                    <div style="border-right: 3px solid #28a745; padding-right: 12px;">
                        <div style="color: #666; font-size: 0.85rem;">عدد الصور</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #28a745;">{{ $dailyReport->photos->count() }}</div>
                    </div>
                    <div style="border-right: 3px solid #ffc107; padding-right: 12px;">
                        <div style="color: #666; font-size: 0.85rem;">ساعات العمل</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #ffc107;">{{ $dailyReport->total_work_hours }}</div>
                    </div>
                </div>
            </div>

            @if($dailyReport->general_notes)
                <div style="background: #fffbf0; padding: 20px; border-radius: 10px; border-right: 4px solid #ffc107; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: #856404;">ملاحظات عامة</h4>
                    <div style="color: #856404; line-height: 1.6;">{{ $dailyReport->general_notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 9999; padding: 40px;" onclick="closeLightbox()">
    <div style="position: relative; height: 100%; display: flex; align-items: center; justify-content: center;">
        <button onclick="closeLightbox()" style="position: absolute; top: 20px; left: 20px; background: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.5rem;">×</button>
        <img id="lightbox-img" src="" alt="" style="max-width: 90%; max-height: 90%; object-fit: contain;">
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function openLightbox(src, title) {
        document.getElementById('lightbox').style.display = 'block';
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-img').alt = title;
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
@endsection
