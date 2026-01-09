# Financial Regret Index Module - مؤشر الندم المالي

## الوصف
وحدة لحساب مؤشر الندم المالي - أداة تفاوضية قوية تُظهر للعميل تكلفة إنهاء العقد مقابل الاستمرار مع المقاول الحالي.

## المفهوم
```
مؤشر الندم المالي = (تكلفة الإنهاء + تكلفة مقاول جديد + التأخير) - (تكلفة الاستمرار مع الحالي)
```

- **إذا كان الناتج موجباً** = الأفضل للعميل الاستمرار مع المقاول الحالي
- **إذا كان الناتج سالباً** = قد يكون الإنهاء أفضل اقتصادياً

## الميزات الرئيسية

✅ **حساب تلقائي لمؤشر الندم**
- حسابات دقيقة لجميع التكاليف
- توصيات تلقائية بناءً على النتائج

✅ **سيناريوهات متعددة**
- سيناريو متفائل
- سيناريو واقعي
- سيناريو متشائم

✅ **تقرير تفاوضي احترافي**
- تقرير PDF بالعربية مع دعم RTL
- تفصيل كامل للتكاليف
- نقاط التفاوض الرئيسية

✅ **عرض تقديمي للعميل**
- بيانات منسقة للعرض
- رسوم بيانية جاهزة
- ملخص تنفيذي

✅ **دعم كامل للعربية (RTL)**

## التثبيت

### 1. تشغيل الـ Migrations
```bash
php artisan migrate
```

سيتم إنشاء الجداول التالية:
- `projects` - المشاريع
- `contracts` - العقود
- `financial_regret_analyses` - تحليلات مؤشر الندم
- `regret_index_scenarios` - السيناريوهات

### 2. إنشاء بيانات تجريبية (اختياري)
يمكنك إنشاء مشاريع وعقود تجريبية للاختبار.

## API Endpoints

جميع الـ endpoints تتطلب authentication عبر Laravel Sanctum.

### القائمة الأساسية:
```
GET    /api/regret-index              - قائمة التحليلات
POST   /api/regret-index/calculate    - حساب تحليل جديد
GET    /api/regret-index/{id}         - عرض تحليل محدد
POST   /api/regret-index/{id}/scenarios - إضافة سيناريو
GET    /api/regret-index/{id}/export  - تصدير PDF
GET    /api/regret-index/{id}/presentation - بيانات العرض
```

للتفاصيل الكاملة، راجع [API Documentation](./FINANCIAL_REGRET_INDEX_API.md)

## مثال الاستخدام

### 1. إنشاء تحليل جديد
```bash
POST /api/regret-index/calculate
```

```json
{
  "project_id": 1,
  "contract_id": 1,
  "analysis_date": "2026-01-04",
  "work_completed_value": 500000,
  "work_completed_percentage": 50,
  "elapsed_days": 180,
  "continuation_remaining_cost": 500000,
  "termination_payment_due": 550000,
  "new_contractor_mobilization": 80000,
  "new_contractor_learning_curve": 40000,
  "new_contractor_premium": 50000,
  "new_contractor_remaining_work": 520000,
  "estimated_delay_days": 60,
  "delay_cost_per_day": 1000
}
```

### 2. عرض النتائج
```bash
GET /api/regret-index/1
```

النتيجة ستتضمن:
- مؤشر الندم المالي
- نسبة الندم
- التوصية (استمرار / تفاوض / مراجعة)
- تفصيل كامل للتكاليف

### 3. إضافة سيناريوهات
```bash
POST /api/regret-index/1/scenarios
```

```json
{
  "scenario_name": "السيناريو المتشائم",
  "scenario_type": "pessimistic",
  "assumptions": {
    "continuation_claims_multiplier": 1.5,
    "termination_claims_multiplier": 2.0,
    "delay_days_multiplier": 1.5
  }
}
```

### 4. تصدير التقرير
```bash
GET /api/regret-index/1/export
```

يتم تحميل ملف PDF باللغة العربية يحتوي على:
- معلومات المشروع والعقد
- تفصيل جميع التكاليف
- النتائج والتوصيات
- السيناريوهات المختلفة
- نقاط التفاوض

## مكونات التكلفة

### 1. تكاليف الاستمرار
- تكلفة الأعمال المتبقية
- المطالبات المتوقعة
- التعديلات والإضافات

### 2. تكاليف الإنهاء
- المستحقات للمقاول
- تكاليف الإخلاء
- المطالبات المتوقعة
- التكاليف القانونية

### 3. تكاليف المقاول الجديد
- تكاليف التعبئة والتجهيز
- تكلفة منحنى التعلم
- علاوة الدخول لمشروع قائم
- تكلفة الأعمال المتبقية

### 4. تكاليف التأخير
- الأيام المتوقعة للتأخير
- التكلفة اليومية للتأخير

## التوصيات التلقائية

يقوم النظام بإصدار توصية تلقائية:

| التوصية | المعنى | الشرط |
|---------|--------|-------|
| استمرار | يُوصى بالاستمرار مع المقاول الحالي | المؤشر > 20% وموجب |
| تفاوض | يُوصى بإعادة التفاوض | المؤشر بين 10-20% |
| مراجعة | يتطلب مراجعة دقيقة | المؤشر < 10% أو سالب |

## الملفات الرئيسية

### Models
- `app/Models/FinancialRegretAnalysis.php` - النموذج الرئيسي
- `app/Models/RegretIndexScenario.php` - نموذج السيناريوهات
- `app/Models/Project.php` - نموذج المشروع
- `app/Models/Contract.php` - نموذج العقد

### Controllers
- `app/Http/Controllers/Api/RegretIndexController.php` - API Controller

### Migrations
- `2026_01_04_000001_create_projects_table.php`
- `2026_01_04_000002_create_contracts_table.php`
- `2026_01_04_000003_create_financial_regret_analyses_table.php`
- `2026_01_04_000004_create_regret_index_scenarios_table.php`

### Views
- `resources/views/pdf/regret-index-report.blade.php` - قالب تقرير PDF

### Routes
- `routes/api.php` - API Routes

## متطلبات النظام
- PHP >= 8.2
- Laravel 12
- Laravel Sanctum (للـ authentication)
- DomPDF (لتصدير PDF)

## الدعم الفني

للمزيد من المعلومات، راجع [التوثيق الكامل للـ API](./FINANCIAL_REGRET_INDEX_API.md)

---

## License
هذا المشروع جزء من نظام CEMS (Construction & Engineering Management System)
