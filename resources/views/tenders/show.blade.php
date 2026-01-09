@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
    .header-content h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 10px 0; }
    .tender-number { color: #666; font-size: 0.9rem; }
    .actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; font-size: 0.9rem; }
    .btn-primary { background: #0071e3; color: white; }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .btn-success { background: #34c759; color: white; }
    .btn-danger { background: #ff3b30; color: white; }
    .btn-warning { background: #ff9500; color: white; }
    .grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    .card { background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; }
    .card h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .info-item { display: flex; flex-direction: column; gap: 5px; }
    .info-label { font-size: 0.85rem; color: #666; font-weight: 600; }
    .info-value { font-size: 0.95rem; color: #1d1d1f; }
    .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; }
    .status-identified { background: #e3f2fd; color: #1976d2; }
    .status-studying { background: #fff3e0; color: #f57c00; }
    .status-go { background: #e8f5e9; color: #388e3c; }
    .status-no_go { background: #ffebee; color: #d32f2f; }
    .status-pricing { background: #f3e5f5; color: #7b1fa2; }
    .status-submitted { background: #e1f5fe; color: #0288d1; }
    .status-won { background: #c8e6c9; color: #2e7d32; }
    .status-lost { background: #ffcdd2; color: #c62828; }
    .timeline { display: flex; flex-direction: column; gap: 15px; }
    .timeline-item { display: flex; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-right: 3px solid #0071e3; }
    .timeline-icon { width: 40px; height: 40px; background: #0071e3; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .timeline-content { flex: 1; }
    .timeline-title { font-weight: 600; margin-bottom: 5px; }
    .timeline-desc { font-size: 0.9rem; color: #666; }
    .timeline-date { font-size: 0.8rem; color: #999; margin-top: 5px; }
    .empty-state { text-align: center; padding: 40px; color: #999; }
    .sidebar .card { position: sticky; top: 80px; }
</style>

<div class="container">
    <div class="header">
        <div class="header-content">
            <h1>{{ $tender->name }}</h1>
            <div class="tender-number">{{ $tender->tender_number }} @if($tender->reference_number)| {{ $tender->reference_number }}@endif</div>
        </div>
        <div class="actions">
            <a href="{{ route('tenders.edit', $tender) }}" class="btn btn-secondary">
                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                تعديل
            </a>
            @if($tender->status === 'studying' || $tender->status === 'identified')
            <button onclick="showGoDecisionModal()" class="btn btn-primary">
                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                قرار GO/NO-GO
            </button>
            @endif
            @if($tender->status === 'pricing' || $tender->status === 'documents_purchased')
            <button onclick="showSubmitModal()" class="btn btn-success">
                <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                تسجيل التقديم
            </button>
            @endif
            @if($tender->status === 'submitted' || $tender->status === 'opened')
            <button onclick="showResultModal()" class="btn btn-warning">
                <i data-lucide="trophy" style="width: 16px; height: 16px;"></i>
                تسجيل النتيجة
            </button>
            @endif
            @if($tender->canConvertToProject())
            <button onclick="showConvertModal()" class="btn btn-success">
                <i data-lucide="folder-plus" style="width: 16px; height: 16px;"></i>
                تحويل لمشروع
            </button>
            @endif
        </div>
    </div>

    <div class="grid">
        <div class="main-content">
            <!-- Basic Information -->
            <div class="card">
                <h3>المعلومات الأساسية</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">الحالة</div>
                        <div class="info-value">
                            @php
                                $statusLabels = [
                                    'identified' => 'تم اكتشافها',
                                    'studying' => 'قيد الدراسة',
                                    'go' => 'قرار المشاركة',
                                    'no_go' => 'قرار عدم المشاركة',
                                    'documents_purchased' => 'تم شراء الكراسة',
                                    'pricing' => 'قيد التسعير',
                                    'submitted' => 'تم التقديم',
                                    'opened' => 'تم فتح المظاريف',
                                    'negotiating' => 'قيد التفاوض',
                                    'won' => 'فوز',
                                    'lost' => 'خسارة',
                                    'cancelled' => 'ملغاة',
                                    'converted' => 'تم التحويل',
                                ];
                            @endphp
                            <span class="status-badge status-{{ $tender->status }}">
                                {{ $statusLabels[$tender->status] ?? $tender->status }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الأولوية</div>
                        <div class="info-value">
                            @php
                                $priorityLabels = ['critical' => 'حرجة', 'high' => 'عالية', 'medium' => 'متوسطة', 'low' => 'منخفضة'];
                            @endphp
                            {{ $priorityLabels[$tender->priority] ?? $tender->priority }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">نوع المناقصة</div>
                        <div class="info-value">
                            @php
                                $typeLabels = ['public' => 'عامة', 'private' => 'خاصة', 'limited' => 'محدودة', 'direct_order' => 'أمر مباشر'];
                            @endphp
                            {{ $typeLabels[$tender->type] ?? $tender->type }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">الفئة</div>
                        <div class="info-value">
                            @php
                                $categoryLabels = ['building' => 'مباني', 'infrastructure' => 'بنية تحتية', 'industrial' => 'صناعي', 'maintenance' => 'صيانة', 'supply' => 'توريدات', 'other' => 'أخرى'];
                            @endphp
                            {{ $categoryLabels[$tender->category] ?? $tender->category }}
                        </div>
                    </div>
                    @if($tender->description)
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-label">الوصف</div>
                        <div class="info-value">{{ $tender->description }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Client Information -->
            <div class="card">
                <h3>معلومات الجهة المالكة</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">اسم الجهة</div>
                        <div class="info-value">{{ $tender->client?->name ?? $tender->client_name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">القطاع</div>
                        <div class="info-value">{{ $tender->sector ?? '-' }}</div>
                    </div>
                    @if($tender->client_contact)
                    <div class="info-item">
                        <div class="info-label">جهة الاتصال</div>
                        <div class="info-value">{{ $tender->client_contact }}</div>
                    </div>
                    @endif
                    @if($tender->client_phone)
                    <div class="info-item">
                        <div class="info-label">الهاتف</div>
                        <div class="info-value">{{ $tender->client_phone }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dates -->
            <div class="card">
                <h3>التواريخ المهمة</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">آخر موعد للتقديم</div>
                        <div class="info-value">{{ $tender->submission_deadline?->format('Y-m-d') ?? '-' }}</div>
                    </div>
                    @if($tender->submission_time)
                    <div class="info-item">
                        <div class="info-label">وقت التقديم</div>
                        <div class="info-value">{{ $tender->submission_time }}</div>
                    </div>
                    @endif
                    @if($tender->opening_date)
                    <div class="info-item">
                        <div class="info-label">تاريخ فتح المظاريف</div>
                        <div class="info-value">{{ $tender->opening_date->format('Y-m-d') }}</div>
                    </div>
                    @endif
                    @if($tender->expected_award_date)
                    <div class="info-item">
                        <div class="info-label">تاريخ الترسية المتوقع</div>
                        <div class="info-value">{{ $tender->expected_award_date->format('Y-m-d') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Financial Information -->
            <div class="card">
                <h3>القيم المالية</h3>
                <div class="info-grid">
                    @if($tender->estimated_value)
                    <div class="info-item">
                        <div class="info-label">القيمة المقدرة</div>
                        <div class="info-value">{{ number_format($tender->estimated_value, 2) }} {{ $tender->currency }}</div>
                    </div>
                    @endif
                    @if($tender->our_offer_value)
                    <div class="info-item">
                        <div class="info-label">قيمة عرضنا</div>
                        <div class="info-value" style="color: #0071e3; font-weight: 700;">{{ number_format($tender->our_offer_value, 2) }} {{ $tender->currency }}</div>
                    </div>
                    @endif
                    @if($tender->winning_value)
                    <div class="info-item">
                        <div class="info-label">قيمة الفوز</div>
                        <div class="info-value">{{ number_format($tender->winning_value, 2) }} {{ $tender->currency }}</div>
                    </div>
                    @endif
                    @if($tender->bid_bond_amount)
                    <div class="info-item">
                        <div class="info-label">قيمة الضمان الابتدائي</div>
                        <div class="info-value">{{ number_format($tender->bid_bond_amount, 2) }} {{ $tender->currency }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <h3>التسلسل الزمني</h3>
                @if($tender->timeline->count() > 0)
                <div class="timeline">
                    @foreach($tender->timeline->sortByDesc('created_at') as $event)
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i data-lucide="activity" style="width: 20px; height: 20px; color: white;"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $event->description }}</div>
                            <div class="timeline-desc">بواسطة: {{ $event->performedBy->name }}</div>
                            <div class="timeline-date">{{ $event->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i data-lucide="clock" style="width: 40px; height: 40px;"></i>
                    <p>لا توجد أحداث بعد</p>
                </div>
                @endif
            </div>
        </div>

        <div class="sidebar">
            <!-- Quick Info -->
            <div class="card">
                <h3>معلومات سريعة</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="info-item">
                        <div class="info-label">المكلف بالمتابعة</div>
                        <div class="info-value">{{ $tender->assignedTo?->name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">مسؤول التسعير</div>
                        <div class="info-value">{{ $tender->estimator?->name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">أنشئت بواسطة</div>
                        <div class="info-value">{{ $tender->createdBy->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء</div>
                        <div class="info-value">{{ $tender->created_at->format('Y-m-d') }}</div>
                    </div>
                    @if($tender->go_decision !== null)
                    <div class="info-item">
                        <div class="info-label">قرار GO/NO-GO</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $tender->go_decision ? 'go' : 'no_go' }}">
                                {{ $tender->go_decision ? 'GO - المشاركة' : 'NO-GO - عدم المشاركة' }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card">
                <h3>المستندات ({{ $tender->documents->count() }})</h3>
                @if($tender->documents->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($tender->documents as $doc)
                    <div style="padding: 10px; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-weight: 600; font-size: 0.9rem;">{{ $doc->name }}</div>
                        <div style="font-size: 0.8rem; color: #666;">{{ $doc->type }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state" style="padding: 20px;">
                    <i data-lucide="file" style="width: 30px; height: 30px;"></i>
                    <p>لا توجد مستندات</p>
                </div>
                @endif
            </div>

            <!-- Competitors -->
            <div class="card">
                <h3>المنافسون ({{ $tender->competitors->count() }})</h3>
                @if($tender->competitors->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($tender->competitors as $comp)
                    <div style="padding: 10px; background: #f8f9fa; border-radius: 6px;">
                        <div style="font-weight: 600; font-size: 0.9rem;">{{ $comp->company_name }}</div>
                        @if($comp->offer_value)
                        <div style="font-size: 0.85rem; color: #666;">{{ number_format($comp->offer_value, 2) }} {{ $tender->currency }}</div>
                        @endif
                        @if($comp->is_winner)
                        <span style="display: inline-block; margin-top: 5px; padding: 3px 8px; background: #c8e6c9; color: #2e7d32; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">فائز</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state" style="padding: 20px;">
                    <i data-lucide="users" style="width: 30px; height: 30px;"></i>
                    <p>لا توجد بيانات منافسين</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals would go here - simplified for now -->
<div id="goDecisionModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 20px 0;">قرار GO/NO-GO</h3>
        <form method="POST" action="{{ route('tenders.go-decision', $tender) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">القرار</label>
                <select name="go_decision" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="1">GO - المشاركة</option>
                    <option value="0">NO-GO - عدم المشاركة</option>
                </select>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">ملاحظات</label>
                <textarea name="go_decision_notes" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif; min-height: 100px;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideGoDecisionModal()" class="btn btn-secondary">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ القرار</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
    
    function showGoDecisionModal() {
        document.getElementById('goDecisionModal').style.display = 'flex';
    }
    
    function hideGoDecisionModal() {
        document.getElementById('goDecisionModal').style.display = 'none';
    }
    
    function showSubmitModal() {
        // Create a safer modal instead of prompt
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = '<div style="background:white;padding:30px;border-radius:12px;max-width:500px;width:90%;"><h3 style="margin:0 0 20px 0;">تسجيل قيمة العرض</h3><form method="POST" action="{{ route("tenders.submit", $tender) }}">@csrf<div style="margin-bottom:20px;"><label style="display:block;margin-bottom:10px;font-weight:600;">قيمة عرضنا</label><input type="number" step="0.01" name="our_offer_value" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:\'Cairo\',sans-serif;"></div><div style="display:flex;gap:10px;justify-content:flex-end;"><button type="button" onclick="this.closest(\'div\').parentElement.remove()" class="btn btn-secondary">إلغاء</button><button type="submit" class="btn btn-primary">حفظ</button></div></form></div>';
        document.body.appendChild(modal);
    }
    
    function showResultModal() {
        // Create a safer modal instead of confirm
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = '<div style="background:white;padding:30px;border-radius:12px;max-width:500px;width:90%;"><h3 style="margin:0 0 20px 0;">تسجيل نتيجة المناقصة</h3><form method="POST" action="{{ route("tenders.result", $tender) }}">@csrf<div style="margin-bottom:20px;"><label style="display:block;margin-bottom:10px;font-weight:600;">النتيجة</label><select name="status" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:\'Cairo\',sans-serif;"><option value="won">فوز</option><option value="lost">خسارة</option></select></div><div style="margin-bottom:20px;"><label style="display:block;margin-bottom:10px;font-weight:600;">اسم الفائز (إذا خسرنا)</label><input type="text" name="winner_name" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:\'Cairo\',sans-serif;"></div><div style="display:flex;gap:10px;justify-content:flex-end;"><button type="button" onclick="this.closest(\'div\').parentElement.remove()" class="btn btn-secondary">إلغاء</button><button type="submit" class="btn btn-primary">حفظ</button></div></form></div>';
        document.body.appendChild(modal);
    }
    
    function showConvertModal() {
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = '<div style="background:white;padding:30px;border-radius:12px;max-width:500px;width:90%;"><h3 style="margin:0 0 20px 0;">تحويل إلى مشروع</h3><form method="POST" action="{{ route("tenders.convert", $tender) }}">@csrf<div style="margin-bottom:20px;"><p>هل تريد تحويل هذه المناقصة إلى مشروع؟</p></div><div style="margin-bottom:20px;"><label style="display:block;margin-bottom:10px;font-weight:600;">اسم المشروع (اختياري)</label><input type="text" name="project_name" value="{{ $tender->name }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:\'Cairo\',sans-serif;"></div><div style="margin-bottom:20px;"><label style="display:block;margin-bottom:10px;font-weight:600;">تاريخ البدء</label><input type="date" name="start_date" value="{{ date(\'Y-m-d\') }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-family:\'Cairo\',sans-serif;"></div><div style="display:flex;gap:10px;justify-content:flex-end;"><button type="button" onclick="this.closest(\'div\').parentElement.remove()" class="btn btn-secondary">إلغاء</button><button type="submit" class="btn btn-primary">تحويل</button></div></form></div>';
        document.body.appendChild(modal);
    }
</script>
@endpush
@endsection
