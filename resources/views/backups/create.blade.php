@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%);
        color: white;
        padding: 20px 24px;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .card-body {
        padding: 30px;
    }
    
    .backup-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    
    .backup-option {
        background: white;
        border: 2px solid #e5e5e7;
        border-radius: 12px;
        padding: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        text-align: center;
    }
    
    .backup-option:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border-color: #0071e3;
    }
    
    .backup-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .backup-option input[type="radio"]:checked + .option-content {
        color: #0071e3;
    }
    
    .backup-option input[type="radio"]:checked ~ .check-mark {
        opacity: 1;
        transform: scale(1);
    }
    
    .backup-option.selected {
        border-color: #0071e3;
        background: #f0f8ff;
    }
    
    .option-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px;
        color: #0071e3;
    }
    
    .option-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 12px;
    }
    
    .option-description {
        color: #86868b;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .check-mark {
        position: absolute;
        top: 15px;
        left: 15px;
        width: 28px;
        height: 28px;
        background: #0071e3;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e5e7;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    
    .alert-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .btn {
        padding: 12px 28px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,113,227,0.3);
    }
    
    .btn-primary:disabled {
        background: #86868b;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-secondary {
        background: #86868b;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #6e6e73;
    }
    
    .button-group {
        display: flex;
        gap: 12px;
        justify-content: flex-start;
        margin-top: 30px;
    }
</style>

<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1d1d1f; margin: 0;">
            <i data-lucide="plus-circle" style="width: 32px; height: 32px; vertical-align: middle;"></i>
            إنشاء نسخة احتياطية
        </h1>
    </div>

    @if(session('error'))
        <div class="alert" style="background: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <i data-lucide="alert-circle" class="alert-icon"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i data-lucide="settings" style="width: 20px; height: 20px;"></i>
            إعدادات النسخ الاحتياطي
        </div>
        
        <div class="card-body">
            <div class="alert">
                <i data-lucide="info" class="alert-icon"></i>
                <div>
                    <strong>ملاحظات هامة:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        <li>نسخ قاعدة البيانات: يتم الاحتفاظ بآخر 30 نسخة احتياطية</li>
                        <li>نسخ الملفات: يتم الاحتفاظ بآخر 10 نسخ احتياطية</li>
                        <li>النسخ الكامل: ينشئ نسخة لقاعدة البيانات والملفات معاً</li>
                        <li>قد تستغرق العملية عدة دقائق حسب حجم البيانات</li>
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('backups.store') }}" id="backupForm">
                @csrf
                
                <!-- Backup Type Selection -->
                <div class="form-group">
                    <label class="form-label">نوع النسخ الاحتياطي *</label>
                    <div class="backup-options">
                        <!-- Database Option -->
                        <label class="backup-option" onclick="selectOption(this, 'database')">
                            <input type="radio" name="type" value="database" required>
                            <div class="option-content">
                                <i data-lucide="database" class="option-icon"></i>
                                <div class="option-title">قاعدة البيانات</div>
                                <div class="option-description">
                                    نسخ احتياطي كامل لقاعدة البيانات بجميع الجداول والبيانات
                                </div>
                            </div>
                            <div class="check-mark">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            </div>
                        </label>

                        <!-- Files Option -->
                        <label class="backup-option" onclick="selectOption(this, 'files')">
                            <input type="radio" name="type" value="files" required>
                            <div class="option-content">
                                <i data-lucide="folder" class="option-icon"></i>
                                <div class="option-title">الملفات</div>
                                <div class="option-description">
                                    نسخ احتياطي للملفات المرفوعة والمخزنة في النظام
                                </div>
                            </div>
                            <div class="check-mark">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            </div>
                        </label>

                        <!-- Full Option -->
                        <label class="backup-option" onclick="selectOption(this, 'full')">
                            <input type="radio" name="type" value="full" required>
                            <div class="option-content">
                                <i data-lucide="layers" class="option-icon"></i>
                                <div class="option-title">نسخة كاملة</div>
                                <div class="option-description">
                                    نسخ احتياطي شامل لقاعدة البيانات والملفات معاً
                                </div>
                            </div>
                            <div class="check-mark">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Backup Name -->
                <div class="form-group">
                    <label class="form-label" for="name">
                        اسم النسخة الاحتياطية (اختياري)
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name"
                        placeholder="مثال: نسخة_قبل_التحديث"
                        value="{{ old('name') }}"
                    >
                    <small style="color: #86868b; display: block; margin-top: 6px;">
                        إذا تركت هذا الحقل فارغاً، سيتم إنشاء اسم تلقائي بناءً على التاريخ والوقت
                    </small>
                    @error('name')
                        <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                        بدء النسخ الاحتياطي
                    </button>
                    <a href="{{ route('backups.index') }}" class="btn btn-secondary">
                        <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function selectOption(element, type) {
        // Remove selected class from all options
        document.querySelectorAll('.backup-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selected class to clicked option
        element.classList.add('selected');
        
        // Update icons
        lucide.createIcons();
    }

    // Form submission handling
    document.getElementById('backupForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" style="width: 18px; height: 18px; animation: spin 1s linear infinite;"></i> جاري المعالجة...';
        
        // Re-create icons for the loader
        lucide.createIcons();
    });

    // Add spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
@endsection
