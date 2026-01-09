@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0;">المطالبة: {{ $claim->claim_number }}</h1>
            <p style="color: #6c757d; margin-top: 5px;">{{ $claim->title }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('claims.export', $claim) }}" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="download" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                تصدير PDF
            </a>
            <a href="{{ route('claims.edit', $claim) }}" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="edit" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                تعديل
            </a>
            <a href="{{ route('claims.index') }}" style="padding: 10px 20px; text-decoration: none; color: #666; background: #f1f3f5; border-radius: 8px; display: inline-block;">عودة</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- معلومات أساسية -->
            <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 20px;">معلومات أساسية</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">المشروع</p>
                        <p style="font-weight: 600;">{{ $claim->project->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">العقد</p>
                        <p style="font-weight: 600;">{{ $claim->contract->title ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">النوع</p>
                        <p style="font-weight: 600;">{{ $claim->type_label }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">السبب</p>
                        <p style="font-weight: 600;">{{ $claim->cause_label }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">الحالة</p>
                        <span style="padding: 6px 15px; 
                            @if($claim->status == 'approved' || $claim->status == 'settled') background: #d4edda; color: #155724;
                            @elseif($claim->status == 'rejected') background: #f8d7da; color: #721c24;
                            @elseif($claim->status == 'submitted' || $claim->status == 'under_review') background: #fff3cd; color: #856404;
                            @else background: #e2e3e5; color: #383d41;
                            @endif
                            border-radius: 20px; font-weight: 600; display: inline-block;">
                            {{ $claim->status_label }}
                        </span>
                    </div>
                    <div>
                        <p style="color: #6c757d; margin-bottom: 5px;">الأولوية</p>
                        <span style="padding: 6px 15px; 
                            @if($claim->priority == 'critical') background: #f8d7da; color: #721c24;
                            @elseif($claim->priority == 'high') background: #fff3cd; color: #856404;
                            @elseif($claim->priority == 'medium') background: #d1ecf1; color: #0c5460;
                            @else background: #e2e3e5; color: #383d41;
                            @endif
                            border-radius: 20px; font-weight: 600; display: inline-block;">
                            @if($claim->priority == 'critical') حرجة
                            @elseif($claim->priority == 'high') عالية
                            @elseif($claim->priority == 'medium') متوسطة
                            @else منخفضة
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 15px;">الوصف</h2>
                <p style="line-height: 1.8;">{{ $claim->description }}</p>

                @if($claim->contractual_basis)
                    <h3 style="margin-top: 20px; margin-bottom: 10px;">الأساس التعاقدي</h3>
                    <p style="line-height: 1.8;">{{ $claim->contractual_basis }}</p>
                @endif

                @if($claim->facts)
                    <h3 style="margin-top: 20px; margin-bottom: 10px;">الوقائع</h3>
                    <p style="line-height: 1.8;">{{ $claim->facts }}</p>
                @endif
            </div>

            <!-- القيم المالية -->
            <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 20px;">القيم المالية والزمنية</h2>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div style="text-align: center; padding: 20px; background: #e3f2fd; border-radius: 10px;">
                        <p style="color: #1976d2; margin-bottom: 5px; font-weight: 600;">المطالب</p>
                        <p style="font-size: 1.5rem; font-weight: 700; margin-bottom: 5px;">{{ number_format($claim->claimed_amount, 2) }}</p>
                        <p style="color: #6c757d; font-size: 0.9rem;">{{ $claim->currency }}</p>
                        <p style="margin-top: 10px; font-weight: 600;">{{ $claim->claimed_days }} يوم</p>
                    </div>
                    <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 10px;">
                        <p style="color: #856404; margin-bottom: 5px; font-weight: 600;">المقيم</p>
                        <p style="font-size: 1.5rem; font-weight: 700; margin-bottom: 5px;">{{ number_format($claim->assessed_amount, 2) }}</p>
                        <p style="color: #6c757d; font-size: 0.9rem;">{{ $claim->currency }}</p>
                        <p style="margin-top: 10px; font-weight: 600;">{{ $claim->assessed_days }} يوم</p>
                    </div>
                    <div style="text-align: center; padding: 20px; background: #d4edda; border-radius: 10px;">
                        <p style="color: #155724; margin-bottom: 5px; font-weight: 600;">المعتمد</p>
                        <p style="font-size: 1.5rem; font-weight: 700; margin-bottom: 5px;">{{ number_format($claim->approved_amount, 2) }}</p>
                        <p style="color: #6c757d; font-size: 0.9rem;">{{ $claim->currency }}</p>
                        <p style="margin-top: 10px; font-weight: 600;">{{ $claim->approved_days }} يوم</p>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 20px;">Timeline</h2>
                <div style="position: relative;">
                    @foreach($claim->timeline as $event)
                    <div style="display: flex; gap: 20px; margin-bottom: 25px; position: relative;">
                        <div style="flex-shrink: 0;">
                            <div style="width: 40px; height: 40px; background: #0071e3; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                <i data-lucide="check" style="width: 20px; height: 20px;"></i>
                            </div>
                        </div>
                        <div style="flex: 1; padding-bottom: 20px; border-right: 2px solid #e9ecef;">
                            <div style="padding-left: 20px;">
                                <p style="font-weight: 600; margin-bottom: 5px;">{{ $event->action }}</p>
                                <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 5px;">
                                    {{ $event->performedBy->name }} • {{ $event->created_at->format('Y-m-d H:i') }}
                                </p>
                                @if($event->notes)
                                    <p style="color: #495057; margin-top: 10px;">{{ $event->notes }}</p>
                                @endif
                                @if($event->from_status && $event->to_status)
                                    <p style="color: #6c757d; font-size: 0.85rem; margin-top: 5px;">
                                        من: {{ $event->from_status }} → إلى: {{ $event->to_status }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- التواريخ -->
            <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">التواريخ الهامة</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">بداية الحدث</p>
                        <p style="font-weight: 600;">{{ $claim->event_start_date->format('Y-m-d') }}</p>
                    </div>
                    @if($claim->event_end_date)
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">نهاية الحدث</p>
                        <p style="font-weight: 600;">{{ $claim->event_end_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">تاريخ الإشعار</p>
                        <p style="font-weight: 600;">{{ $claim->notice_date->format('Y-m-d') }}</p>
                    </div>
                    @if($claim->submission_date)
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">تاريخ التقديم</p>
                        <p style="font-weight: 600;">{{ $claim->submission_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                    @if($claim->resolution_date)
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">تاريخ التسوية</p>
                        <p style="font-weight: 600;">{{ $claim->resolution_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- المعدّ والمراجع -->
            <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">الفريق</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">أعده</p>
                        <p style="font-weight: 600;">{{ $claim->preparedBy->name }}</p>
                    </div>
                    @if($claim->reviewedBy)
                    <div>
                        <p style="color: #6c757d; font-size: 0.85rem; margin-bottom: 3px;">راجعه</p>
                        <p style="font-weight: 600;">{{ $claim->reviewedBy->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- إجراءات سريعة -->
            @if($claim->status != 'settled')
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">إجراءات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if($claim->status == 'identified')
                    <button onclick="sendNotice()" style="background: #17a2b8; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        إرسال إشعار
                    </button>
                    @endif
                    @if($claim->status == 'notice_sent' || $claim->status == 'documenting')
                    <button onclick="submitClaim()" style="background: #ffc107; color: #000; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        تقديم المطالبة
                    </button>
                    @endif
                    @if($claim->status == 'submitted' || $claim->status == 'under_review' || $claim->status == 'negotiating')
                    <button onclick="resolveClaim()" style="background: #28a745; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        تسوية المطالبة
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function sendNotice() {
        if (confirm('هل أنت متأكد من إرسال الإشعار؟')) {
            fetch('{{ route('claims.send-notice', $claim) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    notice_date: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            });
        }
    }

    function submitClaim() {
        if (confirm('هل أنت متأكد من تقديم المطالبة؟')) {
            fetch('{{ route('claims.submit', $claim) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    submission_date: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            });
        }
    }

    function resolveClaim() {
        const approved_amount = prompt('أدخل المبلغ المعتمد:');
        const approved_days = prompt('أدخل الأيام المعتمدة:');
        const resolution_notes = prompt('أدخل ملاحظات التسوية:');

        if (approved_amount && approved_days && resolution_notes) {
            fetch('{{ route('claims.resolve', $claim) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    resolution_date: new Date().toISOString().split('T')[0],
                    approved_amount: parseFloat(approved_amount),
                    approved_days: parseInt(approved_days),
                    resolution_notes: resolution_notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            });
        }
    }
</script>
@endpush
@endsection
