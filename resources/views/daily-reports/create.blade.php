@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">{{ isset($dailyReport) ? 'تعديل التقرير اليومي' : 'إنشاء تقرير يومي جديد' }}</h1>
    
    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ isset($dailyReport) ? route('daily-reports.update', $dailyReport) : route('daily-reports.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($dailyReport))
            @method('PUT')
        @endif

        <!-- Tabs -->
        <div style="background: white; border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
            <div style="display: flex; border-bottom: 1px solid #ddd; overflow-x: auto;">
                <button type="button" class="tab-btn active" data-tab="general" 
                        style="padding: 15px 25px; border: none; background: #f5f5f7; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid #0071e3;">
                    معلومات عامة
                </button>
                <button type="button" class="tab-btn" data-tab="labor" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    العمالة
                </button>
                <button type="button" class="tab-btn" data-tab="equipment" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    المعدات
                </button>
                <button type="button" class="tab-btn" data-tab="work" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    الأعمال المنفذة
                </button>
                <button type="button" class="tab-btn" data-tab="materials" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    المواد
                </button>
                <button type="button" class="tab-btn" data-tab="problems" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    المشاكل
                </button>
                <button type="button" class="tab-btn" data-tab="visitors" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    الزوار
                </button>
                <button type="button" class="tab-btn" data-tab="photos" 
                        style="padding: 15px 25px; border: none; background: white; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 500; border-bottom: 3px solid transparent;">
                    الصور
                </button>
            </div>

            <!-- Tab Content -->
            <div style="padding: 30px;">
                <!-- General Tab -->
                <div class="tab-content" id="general-tab">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                            <select name="project_id" required 
                                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                            {{ (old('project_id', $dailyReport->project_id ?? '') == $project->id) ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم التقرير</label>
                            <input type="text" value="{{ $dailyReport->report_number ?? $reportNumber }}" readonly 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f5f5f7; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التقرير *</label>
                            <input type="date" name="report_date" value="{{ old('report_date', $dailyReport->report_date ?? date('Y-m-d')) }}" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة الجوية</label>
                            <select name="weather_condition" 
                                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                                <option value="">اختر الحالة</option>
                                <option value="صافي" {{ old('weather_condition', $dailyReport->weather_condition ?? '') == 'صافي' ? 'selected' : '' }}>صافي</option>
                                <option value="غائم" {{ old('weather_condition', $dailyReport->weather_condition ?? '') == 'غائم' ? 'selected' : '' }}>غائم</option>
                                <option value="ممطر" {{ old('weather_condition', $dailyReport->weather_condition ?? '') == 'ممطر' ? 'selected' : '' }}>ممطر</option>
                                <option value="عاصف" {{ old('weather_condition', $dailyReport->weather_condition ?? '') == 'عاصف' ? 'selected' : '' }}>عاصف</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">درجة الحرارة (°C)</label>
                            <input type="number" step="0.01" name="temperature" value="{{ old('temperature', $dailyReport->temperature ?? '') }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرطوبة (%)</label>
                            <input type="number" step="0.01" name="humidity" value="{{ old('humidity', $dailyReport->humidity ?? '') }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">وقت بدء العمل</label>
                            <input type="time" name="work_start_time" value="{{ old('work_start_time', $dailyReport->work_start_time ?? '') }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">وقت انتهاء العمل</label>
                            <input type="time" name="work_end_time" value="{{ old('work_end_time', $dailyReport->work_end_time ?? '') }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div style="grid-column: span 2;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ساعات العمل الكلية</label>
                            <input type="number" step="0.01" name="total_work_hours" value="{{ old('total_work_hours', $dailyReport->total_work_hours ?? 8) }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div style="grid-column: span 2;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ظروف الموقع</label>
                            <textarea name="site_conditions" rows="3" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('site_conditions', $dailyReport->site_conditions ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Labor Tab -->
                <div class="tab-content" id="labor-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">عدد العمال الكلي</label>
                            <input type="number" name="workers_count" value="{{ old('workers_count', $dailyReport->workers_count ?? 0) }}" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تفصيل العمالة (JSON)</label>
                            <textarea name="workers_breakdown" rows="4" placeholder='{"مهندسين": 2, "فنيين": 5, "عمال": 20}' 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('workers_breakdown', isset($dailyReport) && $dailyReport->workers_breakdown ? json_encode($dailyReport->workers_breakdown) : '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات الحضور</label>
                            <textarea name="attendance_notes" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('attendance_notes', $dailyReport->attendance_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Equipment Tab -->
                <div class="tab-content" id="equipment-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ساعات المعدات (JSON)</label>
                            <textarea name="equipment_hours" rows="4" placeholder='[{"equipment_id": 1, "hours": 8}, {"equipment_id": 2, "hours": 6}]' 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('equipment_hours', isset($dailyReport) && $dailyReport->equipment_hours ? json_encode($dailyReport->equipment_hours) : '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات المعدات</label>
                            <textarea name="equipment_notes" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('equipment_notes', $dailyReport->equipment_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Work Tab -->
                <div class="tab-content" id="work-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأعمال المنفذة</label>
                            <textarea name="work_executed" rows="6" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('work_executed', $dailyReport->work_executed ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تقدم الأنشطة (JSON)</label>
                            <textarea name="activities_progress" rows="4" placeholder='[{"activity_id": 1, "progress_today": 10}]' 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('activities_progress', isset($dailyReport) && $dailyReport->activities_progress ? json_encode($dailyReport->activities_progress) : '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات الجودة</label>
                            <textarea name="quality_notes" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('quality_notes', $dailyReport->quality_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Materials Tab -->
                <div class="tab-content" id="materials-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المواد المستلمة (JSON)</label>
                            <textarea name="materials_received" rows="4" placeholder='[{"material": "أسمنت", "quantity": 100, "unit": "كيس"}]' 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('materials_received', isset($dailyReport) && $dailyReport->materials_received ? json_encode($dailyReport->materials_received) : '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات المواد</label>
                            <textarea name="materials_notes" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('materials_notes', $dailyReport->materials_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Problems Tab -->
                <div class="tab-content" id="problems-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشاكل</label>
                            <textarea name="problems" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('problems', $dailyReport->problems ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">التأخيرات</label>
                            <textarea name="delays" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('delays', $dailyReport->delays ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">حوادث السلامة</label>
                            <textarea name="safety_incidents" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('safety_incidents', $dailyReport->safety_incidents ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Visitors Tab -->
                <div class="tab-content" id="visitors-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الزوار (JSON)</label>
                            <textarea name="visitors" rows="4" placeholder='[{"name": "أحمد", "company": "شركة ABC", "purpose": "معاينة", "time": "10:00"}]' 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('visitors', isset($dailyReport) && $dailyReport->visitors ? json_encode($dailyReport->visitors) : '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاجتماعات</label>
                            <textarea name="meetings" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('meetings', $dailyReport->meetings ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">التعليمات المستلمة</label>
                            <textarea name="instructions_received" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('instructions_received', $dailyReport->instructions_received ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات عامة</label>
                            <textarea name="general_notes" rows="4" 
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('general_notes', $dailyReport->general_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Photos Tab -->
                <div class="tab-content" id="photos-tab" style="display: none;">
                    <div style="display: grid; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رفع الصور (حتى 24 صورة)</label>
                            <input type="file" name="photos[]" multiple accept="image/*" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                            <p style="color: #666; font-size: 0.85rem; margin-top: 5px;">
                                يتم استخراج GPS والتاريخ تلقائياً من بيانات الصورة (EXIF)
                            </p>
                        </div>

                        @if(isset($dailyReport) && $dailyReport->photos->count() > 0)
                            <div>
                                <h4 style="margin-bottom: 15px;">الصور المرفوعة ({{ $dailyReport->photos->count() }})</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                                    @foreach($dailyReport->photos as $photo)
                                        <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                                            <img src="{{ Storage::url($photo->photo_path) }}" alt="{{ $photo->photo_title }}" 
                                                 style="width: 100%; height: 150px; object-fit: cover;">
                                            <div style="padding: 8px; font-size: 0.8rem;">
                                                <div>{{ $photo->photo_title ?? 'صورة' }}</div>
                                                @if($photo->latitude && $photo->longitude)
                                                    <div style="color: #28a745;">📍 GPS</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="background: white; padding: 20px; border-radius: 10px; display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('daily-reports.index') }}" 
               style="padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; display: inline-block;">
                إلغاء
            </a>
            <button type="submit" name="submit_action" value="draft" 
                    style="background: #f5f5f7; color: #333; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ كمسودة
            </button>
            <button type="submit" name="submit_action" value="submit" 
                    style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ وإرسال
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.background = 'white';
                b.style.borderBottom = '3px solid transparent';
                b.classList.remove('active');
            });

            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            // Activate clicked tab
            this.style.background = '#f5f5f7';
            this.style.borderBottom = '3px solid #0071e3';
            this.classList.add('active');

            // Show corresponding content
            const tabId = this.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).style.display = 'block';
        });
    });
</script>
@endpush
@endsection
