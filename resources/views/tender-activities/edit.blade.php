@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تعديل النشاط - {{ $activity->activity_code }}</h2>
        <a href="{{ route('tender-activities.index', $activity->tender_id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tender-activities.update', $activity->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="tender_id" value="{{ $activity->tender_id }}">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">كود النشاط <span class="text-danger">*</span></label>
                        <input type="text" name="activity_code" class="form-control @error('activity_code') is-invalid @enderror" 
                               value="{{ old('activity_code', $activity->activity_code) }}" required>
                        @error('activity_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">WBS</label>
                        <select name="tender_wbs_id" class="form-select">
                            <option value="">-- اختر WBS --</option>
                            @foreach($wbsItems as $wbs)
                                <option value="{{ $wbs->id }}" {{ old('tender_wbs_id', $activity->tender_wbs_id) == $wbs->id ? 'selected' : '' }}>
                                    {{ $wbs->code ??  $wbs->wbs_code }} - {{ $wbs->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $activity->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $activity->name_en) }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $activity->description) }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">المدة (أيا��) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" 
                               value="{{ old('duration_days', $activity->duration_days) }}" required min="1">
                        @error('duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">الجهد (ساعات)</label>
                        <input type="number" name="effort_hours" class="form-control" 
                               value="{{ old('effort_hours', $activity->effort_hours) }}" step="0.01" min="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">التكلفة المقدرة</label>
                        <input type="number" name="estimated_cost" class="form-control" 
                               value="{{ old('estimated_cost', $activity->estimated_cost) }}" step="0.01" min="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select">
                            <option value="task" {{ old('type', $activity->type) == 'task' ? 'selected' :  '' }}>مهمة</option>
                            <option value="milestone" {{ old('type', $activity->type) == 'milestone' ? 'selected' :  '' }}>معلم</option>
                            <option value="summary" {{ old('type', $activity->type) == 'summary' ? 'selected' : '' }}>ملخص</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select">
                            <option value="low" {{ old('priority', $activity->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ old('priority', $activity->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ old('priority', $activity->priority) == 'high' ? 'selected' :  '' }}>عالية</option>
                            <option value="critical" {{ old('priority', $activity->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">ترتيب العرض</label>
                        <input type="number" name="sort_order" class="form-control" 
                               value="{{ old('sort_order', $activity->sort_order) }}" min="0">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">الأنشطة السابقة (Predecessors)</label>
                        <div id="predecessors-container">
                            @foreach($activity->predecessors as $predecessor)
                            <div class="predecessor-item mb-2 d-flex gap-2" id="predecessor-existing-{{ $loop->index }}">
                                <select name="predecessors[{{ $loop->index }}][id]" class="form-select" required style="flex: 2;">
                                    <option value="">-- اختر النشاط السابق --</option>
                                    @foreach($allActivities ??  [] as $act)
                                        @if($act->id != $activity->id)
                                            <option value="{{ $act->id }}" {{ $predecessor->predecessor_id == $act->id ?  'selected' : '' }}>
                                                {{ $act->activity_code }} - {{ $act->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <select name="predecessors[{{ $loop->index }}][type]" class="form-select" required style="flex: 1;">
                                    <option value="FS" {{ $predecessor->type == 'FS' ? 'selected' : '' }}>Finish-Start (FS)</option>
                                    <option value="SS" {{ $predecessor->type == 'SS' ? 'selected' :  '' }}>Start-Start (SS)</option>
                                    <option value="FF" {{ $predecessor->type == 'FF' ?  'selected' : '' }}>Finish-Finish (FF)</option>
                                    <option value="SF" {{ $predecessor->type == 'SF' ? 'selected' : '' }}>Start-Finish (SF)</option>
                                </select>
                                <input type="number" name="predecessors[{{ $loop->index }}][lag_days]" class="form-control" placeholder="Lag (أيام)" value="{{ $predecessor->lag_days }}" style="flex: 1;">
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addPredecessor()">
                            <i class="fas fa-plus me-1"></i>إضافة نشاط سابق
                        </button>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   value="1" {{ old('is_active', $activity->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ التعديلات
                    </button>
                    <a href="{{ route('tender-activities.index', $activity->tender_id) }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let predecessorCount = {{ $activity->predecessors->count() }};
    
    function addPredecessor() {
        predecessorCount++;
        const container = document. getElementById('predecessors-container');
        const div = document.createElement('div');
        div.className = 'predecessor-item mb-2 d-flex gap-2';
        div.id = 'predecessor-' + predecessorCount;
        div.innerHTML = `
            <select name="predecessors[${predecessorCount}][id]" class="form-select" required style="flex: 2;">
                <option value="">-- اختر النشاط السابق --</option>
                @foreach($allActivities ?? [] as $act)
                    @if($act->id != $activity->id)
                        <option value="{{ $act->id }}">{{ $act->activity_code }} - {{ $act->name }}</option>
                    @endif
                @endforeach
            </select>
            <select name="predecessors[${predecessorCount}][type]" class="form-select" required style="flex: 1;">
                <option value="FS">Finish-Start (FS)</option>
                <option value="SS">Start-Start (SS)</option>
                <option value="FF">Finish-Finish (FF)</option>
                <option value="SF">Start-Finish (SF)</option>
            </select>
            <input type="number" name="predecessors[${predecessorCount}][lag_days]" class="form-control" placeholder="Lag (أيام)" value="0" style="flex: 1;">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement. remove()">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }
</script>
@endsection