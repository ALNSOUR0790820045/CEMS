@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--accent);
        text-decoration: none;
        font-size: 0.9rem;
        margin-bottom: 16px;
        font-weight: 600;
    }
    
    .back-link:hover {
        gap: 10px;
    }
    
    .template-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        background: white;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .info-label {
        font-size: 0.75rem;
        color: #86868b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 1rem;
        color: var(--text);
        font-weight: 600;
    }
    
    .tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        border-bottom: 2px solid #f0f0f0;
        background: white;
        padding: 20px 20px 0;
        border-radius: 12px 12px 0 0;
    }
    
    .tab {
        padding: 12px 24px;
        border: none;
        background: none;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        color: #86868b;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    
    .tab.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }
    
    .tab-content {
        display: none;
        background: white;
        padding: 24px;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .tab-content.active {
        display: block;
    }
    
    .clause-item {
        padding: 20px;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .clause-number {
        display: inline-block;
        background: var(--accent);
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .clause-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }
    
    .clause-content {
        color: #666;
        line-height: 1.8;
        margin-top: 12px;
    }
    
    .variable-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .variable-key {
        font-family: 'Courier New', monospace;
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
</style>

<div class="page-header">
    <a href="{{ route('contract-templates.index') }}" class="back-link">
        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        العودة للقوالب
    </a>
    <h1 class="page-title">{{ $contractTemplate->name }}</h1>
    @if($contractTemplate->name_en)
        <p style="color: #86868b; margin-top: 4px;">{{ $contractTemplate->name_en }}</p>
    @endif
</div>

<div class="template-info">
    <div class="info-item">
        <span class="info-label">الكود</span>
        <span class="info-value">{{ $contractTemplate->code }}</span>
    </div>
    <div class="info-item">
        <span class="info-label">النوع</span>
        <span class="info-value">{{ ucfirst(str_replace('_', ' ', $contractTemplate->type)) }}</span>
    </div>
    @if($contractTemplate->version)
    <div class="info-item">
        <span class="info-label">الإصدار</span>
        <span class="info-value">{{ $contractTemplate->version }}</span>
    </div>
    @endif
    @if($contractTemplate->year)
    <div class="info-item">
        <span class="info-label">السنة</span>
        <span class="info-value">{{ $contractTemplate->year }}</span>
    </div>
    @endif
</div>

@if($contractTemplate->description)
<div style="background: white; padding: 24px; border-radius: 12px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">الوصف</h3>
    <p style="color: #666; line-height: 1.8;">{{ $contractTemplate->description }}</p>
</div>
@endif

<div class="tabs">
    <button class="tab active" onclick="switchTab('clauses')">البنود ({{ $contractTemplate->clauses->count() }})</button>
    <button class="tab" onclick="switchTab('special')">الشروط الخاصة ({{ $contractTemplate->specialConditions->count() }})</button>
    <button class="tab" onclick="switchTab('variables')">المتغيرات ({{ $contractTemplate->variables->count() }})</button>
</div>

<div id="clauses-tab" class="tab-content active">
    @forelse($contractTemplate->clauses as $clause)
    <div class="clause-item">
        <span class="clause-number">{{ $clause->clause_number }}</span>
        <h4 class="clause-title">{{ $clause->title }}</h4>
        @if($clause->has_time_bar)
            <span style="background: #ff3b30; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; display: inline-block; margin-top: 8px;">
                Time Bar: {{ $clause->time_bar_days }} يوم
            </span>
        @endif
        <div class="clause-content">{{ $clause->content }}</div>
    </div>
    @empty
    <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد بنود</p>
    @endforelse
</div>

<div id="special-tab" class="tab-content">
    @forelse($contractTemplate->specialConditions as $condition)
    <div class="clause-item">
        <span class="clause-number">{{ $condition->condition_number }}</span>
        <h4 class="clause-title">{{ $condition->title }}</h4>
        @if($condition->modifies_clause)
            <p style="color: var(--accent); font-size: 0.85rem; margin-top: 4px;">يعدل البند: {{ $condition->modifies_clause }}</p>
        @endif
        <div class="clause-content">{{ $condition->content }}</div>
    </div>
    @empty
    <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد شروط خاصة</p>
    @endforelse
</div>

<div id="variables-tab" class="tab-content">
    @forelse($contractTemplate->variables as $variable)
    <div class="variable-item">
        <div>
            <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">{{ $variable->variable_label }}</div>
            <span class="variable-key">{{ $variable->variable_key }}</span>
            <span style="margin-right: 8px; font-size: 0.85rem; color: #86868b;">{{ $variable->data_type }}</span>
            @if($variable->is_required)
                <span style="color: #ff3b30; margin-right: 8px;">*</span>
            @endif
        </div>
    </div>
    @empty
    <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد متغيرات</p>
    @endforelse
</div>

<div style="margin-top: 24px; text-align: center;">
    <a href="{{ route('contract-templates.generate', $contractTemplate->id) }}" class="btn btn-primary">
        <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
        إنشاء عقد جديد من هذا القالب
    </a>
</div>

@push('scripts')
<script>
    lucide.createIcons();
    
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.add('active');
        event.target.classList.add('active');
    }
</script>
@endpush
@endsection
