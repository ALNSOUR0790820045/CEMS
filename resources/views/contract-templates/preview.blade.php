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
    
    .contract-preview {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        max-width: 900px;
        margin: 0 auto;
    }
    
    .contract-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--accent);
    }
    
    .contract-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 12px;
    }
    
    .contract-subtitle {
        color: #86868b;
        font-size: 1.1rem;
    }
    
    .contract-section {
        margin-bottom: 32px;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .contract-content {
        line-height: 2;
        color: #333;
        text-align: justify;
    }
    
    .parties-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .party-card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        border-right: 4px solid var(--accent);
    }
    
    .party-label {
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 8px;
    }
    
    .export-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f0f0f0;
    }
    
    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #34c759;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #30b350;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-right: 12px;
    }
    
    .status-draft {
        background: rgba(255, 149, 0, 0.1);
        color: #ff9500;
    }
    
    .status-review {
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
    }
    
    .status-approved {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
    }
    
    .status-signed {
        background: rgba(88, 86, 214, 0.1);
        color: #5856d6;
    }
</style>

<div class="page-header">
    <a href="{{ route('contract-templates.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--accent); text-decoration: none; font-size: 0.9rem; margin-bottom: 16px; font-weight: 600;">
        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        العودة للقوالب
    </a>
    <h1 class="page-title">
        معاينة العقد
        <span class="status-badge status-{{ $contract->status }}">
            {{ ucfirst($contract->status) }}
        </span>
    </h1>
</div>

<div class="contract-preview">
    <div class="contract-header">
        <h1 class="contract-title">{{ $contract->contract_title }}</h1>
        <p class="contract-subtitle">عقد {{ $contract->template->name }}</p>
    </div>
    
    <div class="parties-info">
        <div class="party-card">
            <div class="party-label">الطرف الأول (صاحب العمل)</div>
            <div style="font-weight: 600; font-size: 1.1rem; color: var(--text);">
                {{ $contract->parties['employer_name'] ?? 'غير محدد' }}
            </div>
            @if(isset($contract->parties['employer_address']))
                <div style="color: #666; margin-top: 4px;">{{ $contract->parties['employer_address'] }}</div>
            @endif
        </div>
        
        <div class="party-card">
            <div class="party-label">الطرف الثاني (المقاول)</div>
            <div style="font-weight: 600; font-size: 1.1rem; color: var(--text);">
                {{ $contract->parties['contractor_name'] ?? 'غير محدد' }}
            </div>
            @if(isset($contract->parties['contractor_address']))
                <div style="color: #666; margin-top: 4px;">{{ $contract->parties['contractor_address'] }}</div>
            @endif
        </div>
    </div>
    
    @if(count($contract->filled_data) > 0)
    <div class="contract-section">
        <h3 class="section-title">بيانات العقد</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
            @foreach($contract->filled_data as $key => $value)
                <div style="padding: 12px; background: #f9f9f9; border-radius: 6px;">
                    <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 4px;">{{ $key }}</div>
                    <div style="font-weight: 600; color: var(--text);">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="contract-section">
        <h3 class="section-title">بنود العقد</h3>
        @foreach($contract->template->clauses->where('parent_id', null) as $clause)
            <div style="margin-bottom: 24px;">
                <h4 style="font-weight: 700; color: var(--text); margin-bottom: 8px;">
                    <span style="color: var(--accent);">{{ $clause->clause_number }}.</span> {{ $clause->title }}
                </h4>
                <div class="contract-content">
                    {{ $clause->content }}
                </div>
            </div>
        @endforeach
    </div>
    
    @if($contract->template->specialConditions->count() > 0)
    <div class="contract-section">
        <h3 class="section-title">الشروط الخاصة</h3>
        @foreach($contract->template->specialConditions as $condition)
            <div style="margin-bottom: 20px;">
                <h4 style="font-weight: 700; color: var(--text); margin-bottom: 8px;">
                    <span style="color: var(--accent);">{{ $condition->condition_number }}.</span> {{ $condition->title }}
                </h4>
                <div class="contract-content">
                    {{ $condition->content }}
                </div>
            </div>
        @endforeach
    </div>
    @endif
    
    <div class="export-actions">
        <a href="{{ route('contracts.export-word', $contract->id) }}" class="btn btn-primary">
            <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
            تصدير Word
        </a>
        <a href="{{ route('contracts.export-pdf', $contract->id) }}" class="btn btn-secondary">
            <i data-lucide="file-down" style="width: 18px; height: 18px;"></i>
            تصدير PDF
        </a>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
