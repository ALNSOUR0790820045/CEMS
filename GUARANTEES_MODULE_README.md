# وحدة خطابات الضمان (Letter of Guarantee Module)

## نظرة عامة
وحدة متكاملة لإدارة خطابات الضمان البنكية لشركات المقاولات في نظام CEMS ERP.

## الميزات الرئيسية

### أنواع خطابات الضمان المدعومة
1. **ضمان ابتدائي (Bid Bond)** - للمشاركة في المناقصات (1-5%)
2. **ضمان حسن التنفيذ (Performance Bond)** - ضمان تنفيذ العقد (5-10%)
3. **ضمان الدفعة المقدمة (Advance Payment Guarantee)** - ضمان الدفعة المقدمة
4. **ضمان الصيانة (Maintenance/Warranty Bond)** - فترة الضمان بعد التسليم
5. **ضمان الاحتجاز (Retention Guarantee)** - بديل عن خصم الاحتجاز

### الوظائف
- ✅ إدارة كاملة (CRUD) لخطابات الضمان
- ✅ ترقيم تلقائي (LG-YYYY-NNNN)
- ✅ إدارة البنوك
- ✅ ربط الخطابات بالمشاريع والمناقصات والعقود
- ✅ تجديد خطابات الضمان مع سجل التجديدات
- ✅ تحرير الخطابات (كلي أو جزئي)
- ✅ تتبع المطالبات
- ✅ حساب العمولات البنكية
- ✅ تنبيهات انتهاء الصلاحية (30/60/90 يوم)
- ✅ إحصائيات وتقارير
- ✅ واجهة مستخدم عربية متجاوبة (RTL)

## البنية التحتية

### قاعدة البيانات

#### الجداول الرئيسية
- `banks` - معلومات البنوك
- `projects` - المشاريع (مبسط)
- `tenders` - المناقصات (مبسط)
- `contracts` - العقود (مبسط)
- `guarantees` - خطابات الضمان
- `guarantee_renewals` - سجل التجديدات
- `guarantee_releases` - سجل التحرير
- `guarantee_claims` - المطالبات

### النماذج (Models)
- `Bank` - إدارة البنوك
- `Guarantee` - خطاب الضمان الرئيسي
- `GuaranteeRenewal` - تجديد الخطابات
- `GuaranteeRelease` - تحرير الخطابات
- `GuaranteeClaim` - مطالبات الضمان
- `Project`, `Tender`, `Contract` - نماذج مساعدة

### المتحكمات (Controllers)
- `GuaranteeController` - إدارة خطابات الضمان
- `BankController` - إدارة البنوك

## الاستخدام

### تشغيل Migrations
```bash
php artisan migrate
```

### تعبئة بيانات البنوك الافتراضية
```bash
php artisan db:seed --class=BankSeeder
```

### المسارات (Routes)

#### خطابات الضمان
```
GET    /guarantees                    - قائمة الخطابات
POST   /guarantees                    - إنشاء خطاب جديد
GET    /guarantees/create             - نموذج إنشاء خطاب
GET    /guarantees/{id}               - تفاصيل خطاب
GET    /guarantees/{id}/edit          - نموذج تعديل خطاب
PUT    /guarantees/{id}               - تحديث خطاب
DELETE /guarantees/{id}               - حذف خطاب
POST   /guarantees/{id}/approve       - اعتماد خطاب
GET    /guarantees/{id}/renew         - نموذج تجديد خطاب
POST   /guarantees/{id}/renew         - تجديد خطاب
GET    /guarantees/{id}/release       - نموذج تحرير خطاب
POST   /guarantees/{id}/release       - تحرير خطاب
GET    /guarantees-expiring           - خطابات قريبة من الانتهاء
GET    /guarantees-statistics         - إحصائيات
GET    /guarantees-reports            - تقارير
```

#### البنوك
```
GET    /banks                         - قائمة البنوك
POST   /banks                         - إضافة بنك
GET    /banks/create                  - نموذج إضافة بنك
GET    /banks/{id}/edit               - نموذج تعديل بنك
PUT    /banks/{id}                    - تحديث بنك
DELETE /banks/{id}                    - حذف بنك
```

## العلاقات

### Guarantee Model
```php
- belongsTo: bank, project, tender, contract, creator (user), approver (user)
- hasMany: renewals, releases, claims
```

### الأساليب المفيدة
```php
// توليد رقم تلقائي
Guarantee::generateGuaranteeNumber()

// الخطابات النشطة
Guarantee::active()->get()

// الخطابات القريبة من الانتهاء
Guarantee::expiring(30)->get()

// الخطابات المنتهية
Guarantee::expired()->get()

// حساب العمولة
$guarantee->calculateCommission()
```

## الحالات

### حالات خطاب الضمان
- `draft` - مسودة
- `active` - نشط
- `expired` - منتهي
- `released` - محرر
- `claimed` - مطالب به
- `renewed` - مجدد
- `cancelled` - ملغي

## التحسينات المستقبلية
- [ ] تصدير PDF لخطابات الضمان
- [ ] تصدير Excel للتقارير
- [ ] Dashboard widget
- [ ] إشعارات تلقائية عبر البريد الإلكتروني
- [ ] API endpoints
- [ ] سجل التدقيق (Audit Log)
- [ ] رفع المستندات الممسوحة ضوئياً
- [ ] تقارير مفصلة حسب الفترة الزمنية

## المتطلبات
- PHP 8.2+
- Laravel 12.x
- PostgreSQL
- Composer

## الاعتمادات
تم تطوير هذه الوحدة كجزء من نظام CEMS ERP لإدارة المقاولات.

## الترخيص
خاص - CEMS ERP System
