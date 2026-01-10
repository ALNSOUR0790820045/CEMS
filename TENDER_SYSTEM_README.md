# Tender Registration & Opportunities Management System

## نظام إدارة العطاءات والفرص

This comprehensive system manages the entire tender lifecycle from announcement to award decision, providing a complete solution for construction and engineering companies to track, evaluate, and manage tender opportunities.

## Features / المزايا

### 1. Tender Management / إدارة العطاءات
- ✅ Complete tender registration with multi-tab form
- ✅ Auto-generated tender numbers (TND-YYYY-NNN)
- ✅ Support for multiple tender types (construction, infrastructure, buildings, roads, bridges, water, electrical, mechanical, maintenance, consultancy)
- ✅ Multiple contract types (lump sum, unit price, cost plus, time & material, design-build, EPC, BOT)
- ✅ Multi-currency support
- ✅ Document management
- ✅ Status tracking throughout the tender lifecycle

### 2. Dashboard & KPIs / لوحة التحكم والمؤشرات
- ✅ Active tenders count
- ✅ Tenders in preparation
- ✅ Win/Loss rate calculation
- ✅ Pipeline value tracking
- ✅ Upcoming deadlines with countdown
- ✅ Recent tenders overview
- ✅ Tenders by type visualization

### 3. Decision Making / اتخاذ القرار
- ✅ Go/No-Go decision workflow
- ✅ SWOT analysis template
- ✅ Committee decisions tracking
- ✅ Decision history and audit trail
- ✅ Approved budget tracking

### 4. Site Visits / زيارات الموقع
- ✅ Site visit registration
- ✅ Attendee tracking
- ✅ Observations and notes
- ✅ Photo upload with metadata
- ✅ GPS coordinates recording

### 5. Clarifications / الاستفسارات
- ✅ Question submission tracking
- ✅ Answer management
- ✅ Status tracking (pending/answered)
- ✅ Date-stamped Q&A records

### 6. Competitor Analysis / تحليل المنافسين
- ✅ Competitor registration
- ✅ Classification (strong, medium, weak)
- ✅ Price estimation
- ✅ Strengths and weaknesses analysis
- ✅ Comparative analysis view

### 7. Timeline & Deadline Management / إدارة المواعيد
- ✅ Visual timeline display
- ✅ Color-coded deadline urgency:
  - 🟢 Green: > 30 days
  - 🟡 Yellow: 15-30 days
  - 🔴 Red: < 15 days
- ✅ Automatic countdown calculation
- ✅ Multiple key dates tracking:
  - Announcement date
  - Document sale period
  - Site visit date
  - Questions deadline
  - **Submission deadline** (highlighted)
  - Opening date

### 8. Bid Bond Management / إدارة كفالة العطاء
- ✅ Bid bond requirement tracking
- ✅ Percentage and amount calculation
- ✅ Validity period management
- ✅ Auto-calculation from estimated value

### 9. Advanced Features / مزايا متقدمة
- ✅ Filter and search capabilities
- ✅ Pagination for large datasets
- ✅ Responsive RTL design
- ✅ Professional Apple-inspired UI
- ✅ Multi-language support (Arabic/English)
- ✅ User assignment and responsibility tracking
- ✅ Comprehensive audit trail

## Database Structure / هيكل قاعدة البيانات

### Main Tables / الجداول الرئيسية

1. **countries** - Countries database
2. **cities** - Cities linked to countries
3. **currencies** - Multi-currency support
4. **tenders** - Main tender information
5. **tender_site_visits** - Site visit records
6. **tender_clarifications** - Q&A tracking
7. **tender_competitors** - Competitor analysis
8. **tender_committee_decisions** - Decision records

## Installation / التثبيت

### 1. Run Migrations / تشغيل الهجرات

```bash
php artisan migrate
```

This will create:
- countries and cities tables
- currencies table
- tenders table with comprehensive fields
- tender-related tables (site visits, clarifications, competitors, committee decisions)

### 2. Seed Sample Data / إدخال بيانات تجريبية

```bash
php artisan db:seed
```

This will populate:
- GCC countries (Saudi Arabia, UAE, Kuwait, Qatar, Bahrain, Oman)
- Major cities for each country
- Common currencies (SAR, AED, KWD, QAR, BHD, OMR, USD, EUR)
- Sample tenders for testing

### 3. Access the System / الوصول للنظام

Navigate to:
- Dashboard: `/tenders/dashboard`
- All Tenders: `/tenders`
- Create New Tender: `/tenders/create`

## Tender Lifecycle / دورة حياة العطاء

```
1. announced (معلن)
   ↓
2. evaluating (قيد التقييم)
   ↓
3. decision_pending (قيد اتخاذ القرار)
   ↓
4. preparing (قيد التحضير) / passed (لم نتقدم)
   ↓
5. submitted (تم التقديم)
   ↓
6. awarded (تمت الترسية) / lost (خسرنا) / cancelled (ألغي)
```

## Usage Examples / أمثلة الاستخدام

### Creating a New Tender / إنشاء عطاء جديد

1. Navigate to "إضافة عطاء جديد"
2. Fill in the multi-tab form:
   - **Basic Info**: Name, description, owner
   - **Classification**: Type, contract type, value
   - **Location**: Country, city, project location
   - **Important Dates**: All key dates including submission deadline
   - **Bid Bond**: Requirements and amounts
   - **Requirements**: Eligibility criteria

### Making a Go/No-Go Decision / اتخاذ قرار المشاركة

1. Open the tender details
2. Click "اتخاذ قرار المشاركة"
3. Select "المشاركة" or "عدم المشاركة"
4. Fill in SWOT analysis
5. Provide reasons and justification
6. Submit decision

### Recording a Site Visit / تسجيل زيارة موقع

1. Open tender details
2. Click "تسجيل زيارة موقع"
3. Enter visit date and time
4. List attendees
5. Add observations
6. Upload photos
7. Record GPS coordinates

### Adding Competitors / إضافة منافسين

1. Open tender details
2. Click "إضافة / إدارة المنافسين"
3. Enter competitor information
4. Classify as strong, medium, or weak
5. Add strengths and weaknesses
6. Estimate their price

## Key Features Implementation / تطبيق المزايا الرئيسية

### Auto-Generated Tender Numbers
Format: `TND-YYYY-NNN`
- TND: Tender prefix
- YYYY: Current year
- NNN: Sequential number (001, 002, etc.)

### Deadline Urgency Color Coding
- Days > 30: Green (Safe)
- Days 15-30: Yellow (Warning)
- Days < 15: Red (Critical)
- Expired: Gray

### Helper Methods in Tender Model
```php
$tender->getDaysUntilSubmission()    // Returns days remaining
$tender->getDeadlineUrgency()        // Returns: safe, warning, critical, expired
$tender->getDeadlineColor()          // Returns: green, yellow, red, gray
```

## Security & Best Practices / الأمان وأفضل الممارسات

- ✅ All forms use CSRF protection
- ✅ Input validation on all fields
- ✅ Proper relationship constraints with cascading deletes
- ✅ User authentication required
- ✅ Audit trail through timestamps and user tracking
- ✅ Soft deletes can be added if needed

## Future Enhancements / التحسينات المستقبلية

Potential additions:
- 📧 Email notifications for deadlines
- 📱 SMS alerts for critical deadlines
- 📊 Advanced reporting and analytics
- 📄 Document version control
- 🔔 Real-time notifications
- 📈 Dashboard charts and visualizations
- 📤 Export to Excel/PDF
- 🔄 Integration with project management module
- 📋 BOQ (Bill of Quantities) management
- 💰 Financial analysis and profitability projections

## Support / الدعم

For questions or issues, contact the development team.

## License / الترخيص

Proprietary - CEMS ERP System
