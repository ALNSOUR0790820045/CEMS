@extends('layouts.app')

@section('content')
<style>
    /* Same styles as create.blade.php */
    .risk-form {
        padding: 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .tender-info {
        background: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .tender-code {
        font-weight: 600;
        color: #0071e3;
        font-size: 1.1rem;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 10px 15px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Cairo', sans-serif;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #0071e3;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-help {
        font-size: 0.8rem;
        color: #6e6e73;
    }

    .probability-scale,
    .impact-scale {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .scale-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px 10px;
        border: 2px solid #d2d2d7;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .scale-option:hover {
        border-color: #0071e3;
        background: #f5f5f7;
    }

    .scale-option input[type="radio"] {
        display: none;
    }

    .scale-option.selected {
        border-color: #0071e3;
        background: #f0f7ff;
    }

    .scale-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .scale-label {
        font-size: 0.75rem;
        text-align: center;
        padding: 4px 8px;
        border-radius: 4px;
        background: #f5f5f7;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .btn-secondary:hover {
        background: #e8e8ed;
    }

    .calculated-score {
        background: #f5f5f7;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
    }

    .score-value {
        font-size: 2rem;
        color: #0071e3;
        margin: 10px 0;
    }

    .score-level {
        font-size: 1rem;
        padding: 5px 15px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 10px;
    }

    .level-low { background: #34c759; color: white; }
    .level-medium { background: #ff9500; color: white; }
    .level-high { background: #ff3b30; color: white; }
    .level-critical { background: #000; color: white; }
</style>

<div class="risk-form">
    <div class="page-header">
        <h1 class="page-title">تعديل المخاطرة</h1>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <form method="POST" action="{{ route('tender-risks.update', [$tender->id, $risk->id]) }}" id="riskForm">
        @csrf
        @method('PUT')

        <!-- 1. التعريف -->
        <div class="form-card">
            <h2 class="form-section-title">1. التعريف</h2>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">الكود</label>
                    <input type="text" name="risk_code" class="form-input" value="{{ $risk->risk_code }}" required readonly>
                </div>

                <div class="form-group">
                    <label class="form-label required">الفئة</label>
                    <select name="risk_category" class="form-select" required>
                        <option value="">اختر الفئة</option>
                        <option value="technical" {{ $risk->risk_category == 'technical' ? 'selected' : '' }}>فنية</option>
                        <option value="financial" {{ $risk->risk_category == 'financial' ? 'selected' : '' }}>مالية</option>
                        <option value="contractual" {{ $risk->risk_category == 'contractual' ? 'selected' : '' }}>تعاقدية</option>
                        <option value="schedule" {{ $risk->risk_category == 'schedule' ? 'selected' : '' }}>جدولة</option>
                        <option value="resources" {{ $risk->risk_category == 'resources' ? 'selected' : '' }}>موارد</option>
                        <option value="external" {{ $risk->risk_category == 'external' ? 'selected' : '' }}>خارجية</option>
                        <option value="safety" {{ $risk->risk_category == 'safety' ? 'selected' : '' }}>سلامة</option>
                        <option value="quality" {{ $risk->risk_category == 'quality' ? 'selected' : '' }}>جودة</option>
                        <option value="political" {{ $risk->risk_category == 'political' ? 'selected' : '' }}>سياسية</option>
                        <option value="environmental" {{ $risk->risk_category == 'environmental' ? 'selected' : '' }}>بيئية</option>
                        <option value="other" {{ $risk->risk_category == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label required">الحالة</label>
                    <select name="status" class="form-select" required>
                        <option value="identified" {{ $risk->status == 'identified' ? 'selected' : '' }}>محددة</option>
                        <option value="assessed" {{ $risk->status == 'assessed' ? 'selected' : '' }}>مقيّمة</option>
                        <option value="planned" {{ $risk->status == 'planned' ? 'selected' : '' }}>مخطط لها</option>
                        <option value="monitored" {{ $risk->status == 'monitored' ? 'selected' : '' }}>مراقبة</option>
                        <option value="closed" {{ $risk->status == 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">العنوان</label>
                    <input type="text" name="risk_title" class="form-input" value="{{ $risk->risk_title }}" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">الوصف التفصيلي</label>
                    <textarea name="risk_description" class="form-textarea" required>{{ $risk->risk_description }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. التقييم -->
        <div class="form-card">
            <h2 class="form-section-title">2. التقييم</h2>
            
            <div class="form-group">
                <label class="form-label required">الاحتمالية</label>
                <p class="form-help">ما مدى احتمالية حدوث هذه المخاطرة؟</p>
                <div class="probability-scale">
                    <label class="scale-option {{ $risk->probability == 'very_low' ? 'selected' : '' }}">
                        <input type="radio" name="probability" value="very_low" data-score="1" {{ $risk->probability == 'very_low' ? 'checked' : '' }} required>
                        <div class="scale-number">1</div>
                        <div class="scale-label">نادر جداً<br>&lt; 10%</div>
                    </label>
                    <label class="scale-option {{ $risk->probability == 'low' ? 'selected' : '' }}">
                        <input type="radio" name="probability" value="low" data-score="2" {{ $risk->probability == 'low' ? 'checked' : '' }}>
                        <div class="scale-number">2</div>
                        <div class="scale-label">نادر<br>10-30%</div>
                    </label>
                    <label class="scale-option {{ $risk->probability == 'medium' ? 'selected' : '' }}">
                        <input type="radio" name="probability" value="medium" data-score="3" {{ $risk->probability == 'medium' ? 'checked' : '' }}>
                        <div class="scale-number">3</div>
                        <div class="scale-label">محتمل<br>30-50%</div>
                    </label>
                    <label class="scale-option {{ $risk->probability == 'high' ? 'selected' : '' }}">
                        <input type="radio" name="probability" value="high" data-score="4" {{ $risk->probability == 'high' ? 'checked' : '' }}>
                        <div class="scale-number">4</div>
                        <div class="scale-label">مرجح<br>50-70%</div>
                    </label>
                    <label class="scale-option {{ $risk->probability == 'very_high' ? 'selected' : '' }}">
                        <input type="radio" name="probability" value="very_high" data-score="5" {{ $risk->probability == 'very_high' ? 'checked' : '' }}>
                        <div class="scale-number">5</div>
                        <div class="scale-label">شبه مؤكد<br>&gt; 70%</div>
                    </label>
                </div>
                <input type="hidden" name="probability_score" id="probability_score" value="{{ $risk->probability_score }}">
            </div>

            <div class="form-group" style="margin-top: 30px;">
                <label class="form-label required">التأثير</label>
                <p class="form-help">ما مدى تأثير هذه المخاطرة إذا حدثت؟</p>
                <div class="impact-scale">
                    <label class="scale-option {{ $risk->impact == 'very_low' ? 'selected' : '' }}">
                        <input type="radio" name="impact" value="very_low" data-score="1" {{ $risk->impact == 'very_low' ? 'checked' : '' }} required>
                        <div class="scale-number">1</div>
                        <div class="scale-label">ضئيل جداً</div>
                    </label>
                    <label class="scale-option {{ $risk->impact == 'low' ? 'selected' : '' }}">
                        <input type="radio" name="impact" value="low" data-score="2" {{ $risk->impact == 'low' ? 'checked' : '' }}>
                        <div class="scale-number">2</div>
                        <div class="scale-label">طفيف</div>
                    </label>
                    <label class="scale-option {{ $risk->impact == 'medium' ? 'selected' : '' }}">
                        <input type="radio" name="impact" value="medium" data-score="3" {{ $risk->impact == 'medium' ? 'checked' : '' }}>
                        <div class="scale-number">3</div>
                        <div class="scale-label">متوسط</div>
                    </label>
                    <label class="scale-option {{ $risk->impact == 'high' ? 'selected' : '' }}">
                        <input type="radio" name="impact" value="high" data-score="4" {{ $risk->impact == 'high' ? 'checked' : '' }}>
                        <div class="scale-number">4</div>
                        <div class="scale-label">كبير</div>
                    </label>
                    <label class="scale-option {{ $risk->impact == 'very_high' ? 'selected' : '' }}">
                        <input type="radio" name="impact" value="very_high" data-score="5" {{ $risk->impact == 'very_high' ? 'checked' : '' }}>
                        <div class="scale-number">5</div>
                        <div class="scale-label">كارثي</div>
                    </label>
                </div>
                <input type="hidden" name="impact_score" id="impact_score" value="{{ $risk->impact_score }}">
            </div>

            <div class="calculated-score" id="calculatedScore">
                <div>النتيجة المحسوبة</div>
                <div class="score-value" id="scoreValue">{{ $risk->risk_score }}</div>
                <div class="score-level" id="scoreLevel"></div>
            </div>
        </div>

        <!-- 3. التأثير -->
        <div class="form-card">
            <h2 class="form-section-title">3. التأثير</h2>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">التأثير المالي - الحد الأدنى (د.أ)</label>
                    <input type="number" step="0.01" name="cost_impact_min" class="form-input" value="{{ $risk->cost_impact_min }}">
                </div>

                <div class="form-group">
                    <label class="form-label">التأثير المالي - الحد الأقصى (د.أ)</label>
                    <input type="number" step="0.01" name="cost_impact_max" class="form-input" value="{{ $risk->cost_impact_max }}">
                </div>

                <div class="form-group">
                    <label class="form-label">التأثير المالي - المتوقع (د.أ)</label>
                    <input type="number" step="0.01" name="cost_impact_expected" class="form-input" value="{{ $risk->cost_impact_expected }}">
                </div>

                <div class="form-group">
                    <label class="form-label">التأثير الزمني (أيام)</label>
                    <input type="number" name="schedule_impact_days" class="form-input" value="{{ $risk->schedule_impact_days }}">
                </div>
            </div>
        </div>

        <!-- 4. الاستجابة -->
        <div class="form-card">
            <h2 class="form-section-title">4. الاستجابة</h2>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">استراتيجية الاستجابة</label>
                    <select name="response_strategy" class="form-select">
                        <option value="">اختر الاستراتيجية</option>
                        <option value="avoid" {{ $risk->response_strategy == 'avoid' ? 'selected' : '' }}>تجنب</option>
                        <option value="mitigate" {{ $risk->response_strategy == 'mitigate' ? 'selected' : '' }}>تخفيف</option>
                        <option value="transfer" {{ $risk->response_strategy == 'transfer' ? 'selected' : '' }}>نقل</option>
                        <option value="accept" {{ $risk->response_strategy == 'accept' ? 'selected' : '' }}>قبول</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تكلفة الاستجابة (د.أ)</label>
                    <input type="number" step="0.01" name="response_cost" class="form-input" value="{{ $risk->response_cost }}">
                </div>

                <div class="form-group">
                    <label class="form-label">المسؤول</label>
                    <select name="owner_id" class="form-select">
                        <option value="">اختر المسؤول</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $risk->owner_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">خطة الاستجابة</label>
                    <textarea name="response_plan" class="form-textarea" placeholder="اذكر الإجراءات والخطوات المخططة للتعامل مع هذه المخاطرة">{{ $risk->response_plan }}</textarea>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <a href="{{ route('tender-risks.index', $tender->id) }}" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary">تحديث المخاطرة</button>
        </div>
    </form>
</div>

<script>
    // Calculate risk score dynamically
    function calculateScore() {
        const probabilityScore = parseInt(document.querySelector('input[name="probability"]:checked')?.dataset.score || 0);
        const impactScore = parseInt(document.querySelector('input[name="impact"]:checked')?.dataset.score || 0);
        
        document.getElementById('probability_score').value = probabilityScore;
        document.getElementById('impact_score').value = impactScore;
        
        if (probabilityScore && impactScore) {
            const score = probabilityScore * impactScore;
            document.getElementById('scoreValue').textContent = score;
            
            const levelElement = document.getElementById('scoreLevel');
            if (score >= 21) {
                levelElement.textContent = '⚫ حرج';
                levelElement.className = 'score-level level-critical';
            } else if (score >= 13) {
                levelElement.textContent = '🔴 عالي';
                levelElement.className = 'score-level level-high';
            } else if (score >= 7) {
                levelElement.textContent = '🟡 متوسط';
                levelElement.className = 'score-level level-medium';
            } else {
                levelElement.textContent = '🟢 منخفض';
                levelElement.className = 'score-level level-low';
            }
        }
    }

    // Initialize on page load
    calculateScore();

    // Handle scale option selection
    document.querySelectorAll('.scale-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all options in the same group
            this.closest('.probability-scale, .impact-scale').querySelectorAll('.scale-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            // Add selected class to the chosen option
            this.closest('.scale-option').classList.add('selected');
            
            calculateScore();
        });
    });
</script>
@endsection
