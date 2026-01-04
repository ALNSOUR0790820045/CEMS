@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--accent);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: #005bb5;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .breadcrumb {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }
    
    .predecessor-item {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .btn-small {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
</style>

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">الرئيسية</a> / 
    <a href="{{ route('tenders.index') }}">العطاءات</a> / 
    <a href="{{ route('tenders.show', $tender) }}">{{ $tender->name }}</a> / 
    <a href="{{ route('tender-activities.index', $tender) }}">الأنشطة</a> / 
    <span>تعديل نشاط</span>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="page-header">
    <h1 class="page-title">تعديل النشاط: {{ $activity->name }}</h1>
</div>

<div class="card">
    <form method="POST" action="{{ route('tender-activities.update', [$tender, $activity]) }}">
        @csrf
        @method('PUT')
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">رمز النشاط <span style="color: red;">*</span></label>
                <input type="text" name="activity_code" class="form-control" value="{{ old('activity_code', $activity->activity_code) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">عنصر WBS</label>
                <select name="tender_wbs_id" class="form-control">
                    <option value="">-- اختر عنصر WBS --</option>
                    @foreach($wbsItems as $wbs)
                        <option value="{{ $wbs->id }}" {{ old('tender_wbs_id', $activity->tender_wbs_id) == $wbs->id ? 'selected' : '' }}>
                            {{ $wbs->wbs_code }} - {{ $wbs->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">اسم النشاط <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $activity->name) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $activity->name_en) }}">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">الوصف</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $activity->description) }}</textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">المدة (أيام) <span style="color: red;">*</span></label>
                <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $activity->duration_days) }}" min="1" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">الجهد (ساعات)</label>
                <input type="number" name="effort_hours" class="form-control" value="{{ old('effort_hours', $activity->effort_hours) }}" min="0" step="0.01">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">النوع <span style="color: red;">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="task" {{ old('type', $activity->type) == 'task' ? 'selected' : '' }}>مهمة</option>
                    <option value="milestone" {{ old('type', $activity->type) == 'milestone' ? 'selected' : '' }}>معلم</option>
                    <option value="summary" {{ old('type', $activity->type) == 'summary' ? 'selected' : '' }}>ملخص</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">الأولوية <span style="color: red;">*</span></label>
                <select name="priority" class="form-control" required>
                    <option value="low" {{ old('priority', $activity->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ old('priority', $activity->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ old('priority', $activity->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="critical" {{ old('priority', $activity->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">التكلفة المقدرة (ريال)</label>
            <input type="number" name="estimated_cost" class="form-control" value="{{ old('estimated_cost', $activity->estimated_cost) }}" min="0" step="0.01">
        </div>
        
        <div class="form-group">
            <label class="form-label">الأنشطة السابقة (Dependencies)</label>
            <div id="predecessors-container">
                @foreach($activity->predecessors as $predecessor)
                <div class="predecessor-item" id="predecessor-existing-{{ $loop->index }}">
                    <select name="predecessors[{{ $loop->index }}][id]" class="form-control" required style="flex: 2;">
                        <option value="">-- اختر النشاط السابق --</option>
                        @foreach($activities as $act)
                            <option value="{{ $act->id }}" {{ $predecessor->predecessor_id == $act->id ? 'selected' : '' }}>
                                {{ $act->activity_code }} - {{ $act->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="predecessors[{{ $loop->index }}][type]" class="form-control" required style="flex: 1;">
                        <option value="FS" {{ $predecessor->type == 'FS' ? 'selected' : '' }}>Finish-Start (FS)</option>
                        <option value="SS" {{ $predecessor->type == 'SS' ? 'selected' : '' }}>Start-Start (SS)</option>
                        <option value="FF" {{ $predecessor->type == 'FF' ? 'selected' : '' }}>Finish-Finish (FF)</option>
                        <option value="SF" {{ $predecessor->type == 'SF' ? 'selected' : '' }}>Start-Finish (SF)</option>
                    </select>
                    <input type="number" name="predecessors[{{ $loop->index }}][lag_days]" class="form-control" placeholder="Lag (أيام)" value="{{ $predecessor->lag_days }}" style="flex: 1;">
                    <button type="button" class="btn btn-danger btn-small" onclick="removePredecessor('existing-{{ $loop->index }}')">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary btn-small" onclick="addPredecessor()">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                إضافة نشاط سابق
            </button>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                حفظ النشاط
            </button>
            <a href="{{ route('tender-activities.index', $tender) }}" class="btn btn-secondary">
                <i data-lucide="x"></i>
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
    
    let predecessorCount = {{ $activity->predecessors->count() }};
    
    function addPredecessor() {
        predecessorCount++;
        const container = document.getElementById('predecessors-container');
        const div = document.createElement('div');
        div.className = 'predecessor-item';
        div.id = 'predecessor-' + predecessorCount;
        div.innerHTML = `
            <select name="predecessors[${predecessorCount}][id]" class="form-control" required style="flex: 2;">
                <option value="">-- اختر النشاط السابق --</option>
                @foreach($activities as $act)
                    <option value="{{ $act->id }}">{{ $act->activity_code }} - {{ $act->name }}</option>
                @endforeach
            </select>
            <select name="predecessors[${predecessorCount}][type]" class="form-control" required style="flex: 1;">
                <option value="FS">Finish-Start (FS)</option>
                <option value="SS">Start-Start (SS)</option>
                <option value="FF">Finish-Finish (FF)</option>
                <option value="SF">Start-Finish (SF)</option>
            </select>
            <input type="number" name="predecessors[${predecessorCount}][lag_days]" class="form-control" placeholder="Lag (أيام)" value="0" style="flex: 1;">
            <button type="button" class="btn btn-danger btn-small" onclick="removePredecessor(${predecessorCount})">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
            </button>
        `;
        container.appendChild(div);
        lucide.createIcons();
    }
    
    function removePredecessor(id) {
        const element = document.getElementById('predecessor-' + id);
        if (element) {
            element.remove();
        }
    }
</script>
@endsection
