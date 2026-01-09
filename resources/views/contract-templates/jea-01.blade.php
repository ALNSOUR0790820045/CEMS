@extends('layouts.app')

@section('content')
<style>
    .jea-header {
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        padding: 40px;
        border-radius: 12px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .jea-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .jea-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .info-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .info-label {
        font-size: 0.75rem;
        color: #86868b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .info-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
    }
    
    .features-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .feature-item {
        display: flex;
        align-items: start;
        gap: 12px;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        background: rgba(0, 113, 227, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .btn-generate {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--accent);
        color: white;
        padding: 16px 32px;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .btn-generate:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 113, 227, 0.4);
    }
</style>

<div class="jea-header">
    <h1 class="jea-title">{{ $template->name }}</h1>
    <p class="jea-subtitle">{{ $template->name_en ?? 'JEA-01 Contract Template' }}</p>
</div>

<div class="info-grid">
    <div class="info-card">
        <div class="info-label">الكود</div>
        <div class="info-value">{{ $template->code }}</div>
    </div>
    
    <div class="info-card">
        <div class="info-label">النوع</div>
        <div class="info-value">عقد نقابة المقاولين</div>
    </div>
    
    @if($template->version)
    <div class="info-card">
        <div class="info-label">الإصدار</div>
        <div class="info-value">{{ $template->version }}</div>
    </div>
    @endif
    
    @if($template->year)
    <div class="info-card">
        <div class="info-label">السنة</div>
        <div class="info-value">{{ $template->year }}</div>
    </div>
    @endif
</div>

<div class="features-section">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">مميزات العقد</h2>
    <p style="color: #86868b; margin-bottom: 24px;">عقد موحد معتمد من نقابة المقاولين الأردنيين</p>
    
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: var(--accent);"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">معتمد رسمياً</div>
                <div style="font-size: 0.85rem; color: #666;">من نقابة المقاولين الأردنيين</div>
            </div>
        </div>
        
        <div class="feature-item">
            <div class="feature-icon">
                <i data-lucide="shield-check" style="width: 20px; height: 20px; color: var(--accent);"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">حماية قانونية</div>
                <div style="font-size: 0.85rem; color: #666;">يوفر حماية لجميع الأطراف</div>
            </div>
        </div>
        
        <div class="feature-item">
            <div class="feature-icon">
                <i data-lucide="file-text" style="width: 20px; height: 20px; color: var(--accent);"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">{{ $template->clauses->count() }} بند</div>
                <div style="font-size: 0.85rem; color: #666;">بنود شاملة ومفصلة</div>
            </div>
        </div>
        
        <div class="feature-item">
            <div class="feature-icon">
                <i data-lucide="edit" style="width: 20px; height: 20px; color: var(--accent);"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: var(--text); margin-bottom: 4px;">قابل للتخصيص</div>
                <div style="font-size: 0.85rem; color: #666;">شروط خاصة قابلة للإضافة</div>
            </div>
        </div>
    </div>
</div>

@if($template->description)
<div class="features-section">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 16px;">عن العقد</h2>
    <p style="color: #666; line-height: 1.8; text-align: justify;">{{ $template->description }}</p>
</div>
@endif

<div class="features-section">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 16px;">الاستخدام</h2>
    <p style="color: #666; line-height: 1.8; margin-bottom: 20px;">
        يستخدم هذا العقد في مشاريع البناء والإنشاءات في الأردن. يتضمن جميع البنود القانونية والفنية اللازمة لتنظيم العلاقة بين المقاول وصاحب العمل.
    </p>
    
    <div style="text-align: center; margin-top: 32px;">
        <a href="{{ route('contract-templates.generate', $template->id) }}" class="btn-generate">
            <i data-lucide="plus-circle" style="width: 24px; height: 24px;"></i>
            إنشاء عقد جديد
        </a>
    </div>
</div>

<div class="features-section">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 16px;">الروابط المفيدة</h2>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('contract-templates.show', $template->id) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(0, 113, 227, 0.1); color: var(--accent); border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s;">
            <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
            عرض تفاصيل العقد
        </a>
        <a href="{{ route('contract-templates.clauses', $template->id) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(0, 113, 227, 0.1); color: var(--accent); border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s;">
            <i data-lucide="list" style="width: 18px; height: 18px;"></i>
            عرض جميع البنود
        </a>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
