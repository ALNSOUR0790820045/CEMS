# Activities & Tasks Management Module

## نظام إدارة الأنشطة والمهام

تم تطوير نظام شامل لإدارة أنشطة المشاريع يتضمن:
- إدارة 245+ نشاط
- إدارة 312+ علاقة تبعية
- تتبع المعالم الرئيسية
- تحديث التقدم التفاعلي
- تصميم Apple-style مع دعم RTL كامل

## المميزات الرئيسية

### 1. إدارة الأنشطة (Activities Management)
- **CRUD كامل**: إنشاء، عرض، تعديل، وحذف الأنشطة
- **فلاتر متقدمة**: البحث حسب WBS، الحالة، المسؤول، الأنشطة الحرجة
- **معلومات شاملة**:
  - كود النشاط الفريد (ACT-001)
  - ربط بـ WBS
  - تواريخ مخططة وفعلية
  - المدة وساعات العمل
  - نسبة الإنجاز (مع 4 طرق حساب)
  - النوع (مهمة/معلم/ملخص)
  - المسار الحرج (Critical Path)
  - التكلفة المخططة والفعلية
  - الأولوية (منخفضة/متوسطة/عالية/حرجة)

### 2. إدارة التبعيات (Dependencies Management)
- **أنواع العلاقات**:
  - FS (Finish-to-Start): الأكثر شيوعاً
  - SS (Start-to-Start): البداية متزامنة
  - FF (Finish-to-Finish): النهاية متزامنة
  - SF (Start-to-Finish): نادرة الاستخدام
- **Lag Days**: التأخير أو التقديم بين الأنشطة
- **Circular Dependency Detection**: منع العلاقات الدائرية
- **Network Diagram**: عرض بصري للعلاقات

### 3. إدارة المعالم (Milestones Management)
- **أنواع المعالم**:
  - Project: معالم المشروع
  - Contractual: معالم تعاقدية
  - Payment: معالم الدفع
  - Technical: معالم تقنية
- **تتبع الحالة**:
  - Pending: قيد الانتظار
  - Achieved: تم التحقيق
  - Missed: فات الموعد
- **Timeline View**: عرض المعالم القريبة مع التنبيهات

### 4. تحديث التقدم (Progress Update)
- **واجهة تفاعلية**:
  - Slider مرئي لنسبة الإنجاز
  - تحديث التواريخ الفعلية
  - تسجيل ساعات العمل
  - تسجيل التكلفة الفعلية
  - ملاحظات التقدم
- **حساب تلقائي**:
  - تحديث الحالة بناءً على نسبة الإنجاز
  - حساب المدة من التواريخ
  - حساب التقدم بناءً على الطريقة المحددة

## البنية التقنية

### Database Schema

#### Tables Created:
1. **projects**: معلومات المشاريع الأساسية
2. **project_wbs**: هيكل تقسيم العمل الهرمي
3. **project_activities**: الأنشطة (245+ نشاط)
4. **activity_dependencies**: التبعيات (312+ علاقة)
5. **project_milestones**: المعالم الرئيسية

### Models
- **Project**: العلاقات مع الأنشطة والمعالم
- **ProjectWbs**: البنية الهرمية مع parent/children
- **ProjectActivity**: Calculations للمدة والتقدم
- **ActivityDependency**: Validation للعلاقات الدائرية
- **ProjectMilestone**: Accessors للحالة والنوع

### Controllers
- **ProjectActivityController**: CRUD + تحديث التقدم
- **ActivityDependencyController**: إدارة التبعيات
- **ProjectMilestoneController**: إدارة المعالم

### Views (Blade Templates)
- `activities/index.blade.php`: قائمة الأنشطة مع فلاتر
- `activities/create.blade.php`: نموذج إنشاء نشاط
- `activities/edit.blade.php`: نموذج تعديل نشاط
- `activities/show.blade.php`: تفاصيل النشاط الكاملة
- `activities/dependencies.blade.php`: إدارة التبعيات
- `activities/milestones.blade.php`: إدارة المعالم
- `activities/progress-update.blade.php`: تحديث التقدم

## التثبيت والإعداد

### 1. تشغيل Migrations
```bash
php artisan migrate
```

### 2. تشغيل Seeder (بيانات تجريبية)
```bash
php artisan db:seed --class=ProjectsSeeder
```

سيقوم الـ Seeder بإنشاء:
- 1 مشروع تجريبي
- 4 مستويات WBS
- 5 أنشطة مع حالات مختلفة
- 4 علاقات تبعية
- 3 معالم رئيسية

### 3. الوصول للنظام
- **الأنشطة**: `/activities`
- **التبعيات**: `/dependencies`
- **المعالم**: `/milestones`

## الاستخدام

### إضافة نشاط جديد
1. انتقل إلى `/activities`
2. اضغط "إضافة نشاط جديد"
3. املأ البيانات المطلوبة:
   - معلومات أساسية (كود، اسم، WBS)
   - جدول زمني (تواريخ مخططة)
   - نوع النشاط وطريقة حساب التقدم
   - المسؤول والحالة والأولوية
   - التكلفة المخططة
4. احفظ النشاط

### تحديث تقدم النشاط
1. افتح تفاصيل النشاط
2. اضغط "تحديث التقدم"
3. استخدم الـ Slider أو أدخل النسبة مباشرة
4. حدّث التواريخ الفعلية وساعات العمل
5. أضف ملاحظات إن وجدت
6. احفظ التحديث

### إضافة تبعية
1. انتقل إلى `/dependencies`
2. اختر النشاط السابق والنشاط اللاحق
3. حدد نوع العلاقة (FS, SS, FF, SF)
4. أضف Lag Days إن وجد
5. احفظ العلاقة

النظام سيمنع تلقائياً:
- العلاقات الذاتية (النشاط يعتمد على نفسه)
- العلاقات الدائرية (A → B → C → A)

### إضافة معلم
1. انتقل إلى `/milestones`
2. اختر المشروع واكتب اسم المعلم
3. حدد التاريخ المستهدف والنوع
4. احفظ المعلم

## الحسابات التلقائية

### Duration Calculation
```php
$activity->calculatePlannedDuration();
// يحسب المدة من التواريخ المخططة

$activity->calculateActualDuration();
// يحسب المدة من التواريخ الفعلية
```

### Progress Calculation
```php
$activity->calculateProgress();
// يحسب التقدم بناءً على progress_method:
// - manual: لا تغيير
// - duration: نسبة المدة الفعلية للمخططة
// - effort: نسبة ساعات العمل الفعلية للمخططة
// - units: يدوي (لاحقاً)
```

### Status Auto-Update
عند تحديث التقدم:
- `0%` → `not_started`
- `1-99%` → `in_progress`
- `100%` → `completed`

## التصميم والواجهة

### Apple-Style Design
- ألوان ناعمة وحديثة
- تدرجات لونية (Gradients)
- Backdrop blur للقوائم
- Border radius دائري
- Shadows ناعمة
- Transitions سلسة

### RTL Support
- دعم كامل للغة العربية
- `dir="rtl"` في HTML
- خط Cairo من Google Fonts
- محاذاة النصوص من اليمين
- أيقونات Lucide

### Color Coding

#### الحالات (Status):
- لم يبدأ: `#86868b` (رمادي)
- قيد التنفيذ: `#0071e3` (أزرق)
- مكتمل: `#34c759` (أخضر)
- معلق: `#ff9500` (برتقالي)
- ملغي: `#ff3b30` (أحمر)

#### الأولويات (Priority):
- منخفضة: `#34c759` (أخضر)
- متوسطة: `#ff9500` (برتقالي)
- عالية: `#ff6b1a` (برتقالي داكن)
- حرجة: `#ff3b30` (أحمر)

#### الأنشطة الحرجة:
- خلفية: `#fff5f5` (أحمر فاتح)
- أيقونة: `#ff3b30` (أحمر)

## الأمان والتحقق

### Validation Rules
- التحقق من صحة التواريخ
- التحقق من وجود الـ WBS
- التحقق من صحة نسبة الإنجاز (0-100)
- منع التبعيات الدائرية

### Authorization
جميع الصفحات محمية بـ `auth` middleware

## المستقبل والتطوير

### قيد التطوير:
- [ ] Critical Path Method (CPM) calculation
- [ ] Earned Value Management (EVM)
- [ ] Gantt Chart visualization
- [ ] Resource allocation
- [ ] Progress photos upload
- [ ] Documents attachment
- [ ] Update history log
- [ ] Export to Excel/PDF
- [ ] Dashboard statistics

## الدعم

للأسئلة والدعم، يرجى التواصل مع فريق التطوير.

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-02  
**Laravel Version**: 12.x  
**Database**: PostgreSQL
