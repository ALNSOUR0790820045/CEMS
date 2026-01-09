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
    
    .clause-list {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .clause-item {
        padding: 20px;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    
    .clause-number {
        display: inline-block;
        background: var(--accent);
        color: white;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .clause-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 12px;
    }
    
    .clause-content {
        color: #666;
        line-height: 1.8;
        margin-top: 12px;
    }
    
    .clause-meta {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    
    .meta-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-category {
        background: rgba(0, 113, 227, 0.1);
        color: var(--accent);
    }
    
    .badge-timebar {
        background: rgba(255, 59, 48, 0.1);
        color: #ff3b30;
    }
    
    .badge-mandatory {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
    }
</style>

<div class="page-header">
    <a href="{{ route('contract-templates.show', $contractTemplate->id) }}" class="back-link">
        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        العودة
    </a>
    <h1 class="page-title">بنود {{ $contractTemplate->name }}</h1>
</div>

<div class="clause-list">
    @forelse($contractTemplate->clauses as $clause)
    <div class="clause-item">
        <span class="clause-number">البند {{ $clause->clause_number }}</span>
        
        <h3 class="clause-title">{{ $clause->title }}</h3>
        
        @if($clause->title_en)
            <p style="color: #86868b; font-size: 0.95rem; margin-bottom: 8px;">{{ $clause->title_en }}</p>
        @endif
        
        <div class="clause-content">
            {{ $clause->content }}
        </div>
        
        @if($clause->content_en)
            <div class="clause-content" style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed #e0e0e0;">
                <strong>English:</strong> {{ $clause->content_en }}
            </div>
        @endif
        
        <div class="clause-meta">
            <span class="meta-badge badge-category">
                <i data-lucide="tag" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                {{ $clause->category }}
            </span>
            
            @if($clause->has_time_bar)
                <span class="meta-badge badge-timebar">
                    <i data-lucide="clock" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                    Time Bar: {{ $clause->time_bar_days }} يوم
                </span>
            @endif
            
            @if($clause->is_mandatory)
                <span class="meta-badge badge-mandatory">
                    <i data-lucide="check-circle" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                    إلزامي
                </span>
            @endif
            
            @if($clause->is_modifiable)
                <span class="meta-badge" style="background: rgba(255, 149, 0, 0.1); color: #ff9500;">
                    <i data-lucide="edit" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i>
                    قابل للتعديل
                </span>
            @endif
        </div>
        
        @if($clause->time_bar_description)
            <div style="margin-top: 12px; padding: 12px; background: rgba(255, 59, 48, 0.05); border-right: 3px solid #ff3b30; border-radius: 6px;">
                <strong style="color: #ff3b30;">ملاحظة Time Bar:</strong>
                <p style="color: #666; margin-top: 4px;">{{ $clause->time_bar_description }}</p>
            </div>
        @endif
    </div>
    @empty
    <p style="text-align: center; color: #86868b; padding: 60px;">لا توجد بنود متاحة</p>
    @endforelse
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
