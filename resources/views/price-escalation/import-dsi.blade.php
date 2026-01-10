@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 10px;
    }
    
    .page-subtitle {
        font-size: 1rem;
        color: #86868b;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 20px;
    }
    
    .upload-area {
        border: 2px dashed #d1d1d6;
        border-radius: 12px;
        padding: 60px 40px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .upload-area:hover {
        border-color: #0071e3;
        background: rgba(0, 113, 227, 0.05);
    }
    
    .upload-area.dragover {
        border-color: #0071e3;
        background: rgba(0, 113, 227, 0.1);
    }
    
    .upload-icon {
        width: 64px;
        height: 64px;
        color: #0071e3;
        margin: 0 auto 20px;
    }
    
    .upload-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .upload-subtitle {
        color: #86868b;
        margin-bottom: 20px;
    }
    
    .file-input {
        display: none;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary:hover {
        background: #0077ed;
    }
    
    .template-section {
        margin-top: 30px;
        padding: 20px;
        background: #f5f5f7;
        border-radius: 8px;
    }
    
    .template-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    
    th, td {
        padding: 12px;
        text-align: center;
        border: 1px solid #e5e5ea;
    }
    
    th {
        background: #f5f5f7;
        font-weight: 600;
    }
    
    .download-btn {
        margin-top: 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: #0071e3;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        border: 2px solid #0071e3;
        transition: all 0.2s;
    }
    
    .download-btn:hover {
        background: #0071e3;
        color: white;
    }
    
    .logs-section {
        margin-top: 40px;
    }
    
    .log-item {
        padding: 15px;
        background: #f5f5f7;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .log-info {
        flex: 1;
    }
    
    .log-date {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .log-details {
        font-size: 0.9rem;
        color: #86868b;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">استيراد مؤشرات DSI</h1>
        <p class="page-subtitle">استيراد مؤشرات دائرة الإحصاءات العامة من ملف CSV</p>
    </div>
    
    @if($errors->any())
        <div style="background: #fee; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>خطأ:</strong> {{ $errors->first() }}
        </div>
    @endif
    
    @if(session('success'))
        <div style="background: #d1f4dd; color: #047857; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card">
        <form method="POST" action="{{ route('price-escalation.import-dsi.post') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <i data-lucide="upload-cloud" class="upload-icon"></i>
                <h3 class="upload-title">اسحب الملف هنا أو انقر للاختيار</h3>
                <p class="upload-subtitle">CSV - حجم أقصى 10MB</p>
                <input type="file" name="file" id="fileInput" class="file-input" accept=".csv" required>
                <button type="button" class="btn-primary" onclick="event.stopPropagation(); document.getElementById('fileInput').click()">
                    اختيار ملف
                </button>
            </div>
            
            <div id="fileInfo" style="margin-top: 20px; display: none;">
                <p style="font-weight: 600;">الملف المختار: <span id="fileName"></span></p>
            </div>
            
            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" class="btn-primary" style="padding: 14px 40px;">
                    استيراد المؤشرات
                </button>
            </div>
        </form>
        
        <div class="template-section">
            <h4 class="template-title">تنسيق الملف المطلوب:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Materials</th>
                        <th>Labor</th>
                        <th>General (اختياري)</th>
                        <th>Source (اختياري)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2026</td>
                        <td>1</td>
                        <td>156.8</td>
                        <td>142.3</td>
                        <td>149.5</td>
                        <td>DOS Jordan</td>
                    </tr>
                    <tr>
                        <td>2026</td>
                        <td>2</td>
                        <td>158.2</td>
                        <td>143.1</td>
                        <td>150.1</td>
                        <td>DOS Jordan</td>
                    </tr>
                </tbody>
            </table>
            <a href="#" class="download-btn" onclick="downloadTemplate(); return false;">
                <i data-lucide="download" style="width: 18px; height: 18px;"></i>
                تحميل قالب CSV
            </a>
        </div>
    </div>
    
    @if(!empty($importLogs))
        <div class="logs-section">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px;">سجل الاستيرادات السابقة</h3>
            @foreach($importLogs as $log)
                <div class="log-item">
                    <div class="log-info">
                        <div class="log-date">{{ $log->import_date->format('Y-m-d') }}</div>
                        <div class="log-details">
                            {{ $log->records_imported }} سجل - بواسطة {{ $log->importedBy->name }}
                        </div>
                    </div>
                    <i data-lucide="check-circle" style="width: 24px; height: 24px; color: #34c759;"></i>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    lucide.createIcons();
    
    // File input handling
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileInfo.style.display = 'block';
        }
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileName.textContent = files[0].name;
            fileInfo.style.display = 'block';
        }
    });
    
    // Download template
    function downloadTemplate() {
        const csv = 'Year,Month,Materials,Labor,General,Source\n2026,1,156.8,142.3,149.5,DOS Jordan\n2026,2,158.2,143.1,150.1,DOS Jordan';
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'dsi_template.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }
</script>
@endsection
