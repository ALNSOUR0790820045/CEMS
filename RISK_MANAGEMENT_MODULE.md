# Risk Management Module (إدارة المخاطر)

## نظرة عامة (Overview)

نظام متكامل لإدارة المخاطر في المشاريع الإنشائية يوفر إدارة كاملة لدورة حياة المخاطر من التحديد إلى الإغلاق.

A comprehensive Risk Management Module for construction projects providing complete lifecycle management from identification to closure.

## هيكل قاعدة البيانات (Database Structure)

### 1. risk_registers - سجل المخاطر
Primary risk register for each project with approval workflow.

**Fields:**
- `id` - Primary key
- `register_number` - Auto-generated (RR-YYYY-XXXX)
- `project_id` - Foreign key to projects
- `name` - Register name
- `description` - Detailed description
- `version` - Version number (default: 1.0)
- `status` - draft, active, closed
- `prepared_by_id` - Foreign key to users
- `approved_by_id` - Foreign key to users (nullable)
- `approved_at` - Approval timestamp
- `review_frequency` - weekly, monthly, quarterly
- `last_review_date` - Last review date
- `next_review_date` - Next scheduled review
- `company_id` - Foreign key to companies
- Soft deletes enabled

### 2. risks - المخاطر
Individual risk records with auto-calculated scores.

**Fields:**
- `id` - Primary key
- `risk_number` - Auto-generated (RSK-YYYY-XXXX)
- `risk_register_id` - Foreign key to risk_registers
- `project_id` - Foreign key to projects
- `title` - Risk title
- `description` - Detailed description
- `category` - technical, financial, schedule, safety, environmental, contractual, resource, external
- `source` - Risk source
- `trigger_events` - Triggering events
- `affected_objectives` - JSON array (cost, time, quality, safety, scope)
- `identification_date` - When risk was identified
- `identified_by_id` - Foreign key to users
- `probability` - very_low, low, medium, high, very_high
- `probability_score` - 1-5 (auto-calculated to risk_score)
- `impact` - very_low, low, medium, high, very_high
- `impact_score` - 1-5 (auto-calculated to risk_score)
- `risk_score` - probability_score × impact_score (1-25)
- `risk_level` - low, medium, high, critical (auto-calculated)
- `cost_impact_min` - Minimum cost impact
- `cost_impact_max` - Maximum cost impact
- `cost_impact_expected` - Expected cost impact
- `schedule_impact_days` - Schedule delay in days
- `response_strategy` - avoid, mitigate, transfer, accept
- `response_plan` - Response plan details
- `contingency_plan` - Contingency plan
- `residual_probability` - Post-response probability (1-5)
- `residual_impact` - Post-response impact (1-5)
- `residual_score` - Auto-calculated residual risk
- `owner_id` - Risk owner (foreign key to users)
- `status` - identified, analyzing, responding, monitoring, closed, occurred
- `due_date` - Response due date
- `closed_date` - Closure date
- `closure_reason` - Reason for closure
- `lessons_learned` - Lessons learned
- `company_id` - Foreign key to companies
- Soft deletes enabled

### 3. risk_categories - فئات المخاطر
Hierarchical risk categories for organization.

**Fields:**
- `id` - Primary key
- `code` - Unique category code
- `name` - Arabic name
- `name_en` - English name
- `parent_id` - Self-referencing foreign key
- `description` - Category description
- `default_probability` - Default probability score (1-5)
- `default_impact` - Default impact score (1-5)
- `is_active` - Active status
- `company_id` - Foreign key to companies

### 4. risk_assessments - تقييمات المخاطر
Assessment history for risks.

**Fields:**
- `id` - Primary key
- `risk_id` - Foreign key to risks
- `assessment_date` - Assessment date
- `assessment_type` - initial, reassessment, post_response
- `assessed_by_id` - Foreign key to users
- `probability` - Assessed probability
- `probability_score` - 1-5
- `impact` - Assessed impact
- `impact_score` - 1-5
- `risk_score` - Auto-calculated
- `risk_level` - Auto-calculated
- `cost_impact` - Cost impact estimate
- `schedule_impact` - Schedule impact in days
- `justification` - Assessment justification
- `recommendations` - Recommendations

### 5. risk_responses - استجابات المخاطر
Response actions for risks.

**Fields:**
- `id` - Primary key
- `risk_id` - Foreign key to risks
- `response_number` - Response identifier
- `response_type` - preventive, corrective, contingency
- `strategy` - avoid, mitigate, transfer, accept
- `description` - Response description
- `action_required` - Required actions
- `responsible_id` - Foreign key to users
- `target_date` - Target completion date
- `actual_date` - Actual completion date
- `cost_of_response` - Response cost
- `effectiveness` - not_started, in_progress, effective, partially_effective, ineffective
- `status` - planned, in_progress, completed, cancelled
- `remarks` - Additional notes

### 6. risk_monitoring - مراقبة المخاطر
Periodic monitoring records.

**Fields:**
- `id` - Primary key
- `risk_id` - Foreign key to risks
- `monitoring_date` - Monitoring date
- `monitored_by_id` - Foreign key to users
- `current_status` - Current risk status
- `probability_change` - increased, same, decreased
- `impact_change` - increased, same, decreased
- `trigger_status` - not_triggered, warning, triggered
- `early_warning_signs` - Warning signs observed
- `actions_taken` - Actions taken
- `effectiveness` - Effectiveness assessment
- `recommendations` - Recommendations
- `next_review_date` - Next review date

### 7. risk_incidents - حوادث المخاطر
Actual risk occurrence tracking.

**Fields:**
- `id` - Primary key
- `incident_number` - Auto-generated (RI-YYYY-XXXX)
- `risk_id` - Foreign key to risks (nullable)
- `project_id` - Foreign key to projects
- `incident_date` - Incident date
- `title` - Incident title
- `description` - Incident description
- `category` - Incident category
- `actual_cost_impact` - Actual cost impact
- `actual_schedule_impact` - Actual schedule delay in days
- `root_cause` - Root cause analysis
- `immediate_actions` - Immediate actions taken
- `corrective_actions` - Corrective actions
- `preventive_actions` - Preventive actions
- `lessons_learned` - Lessons learned
- `reported_by_id` - Foreign key to users
- `status` - reported, investigating, resolved, closed
- `company_id` - Foreign key to companies

### 8. risk_matrix_settings - إعدادات مصفوفة المخاطر
Configurable risk matrix settings per company.

**Fields:**
- `id` - Primary key
- `company_id` - Foreign key to companies
- `probability_levels` - JSON array of probability levels
- `impact_levels` - JSON array of impact levels
- `risk_thresholds` - JSON object with thresholds
- `cost_impact_ranges` - JSON array (optional)
- `schedule_impact_ranges` - JSON array (optional)
- `is_active` - Active status

## API Endpoints

### Risk Registers
```
GET    /api/risk-registers              - List all registers
POST   /api/risk-registers              - Create new register
GET    /api/risk-registers/{id}         - Get register details
PUT    /api/risk-registers/{id}         - Update register
DELETE /api/risk-registers/{id}         - Delete register
GET    /api/risk-registers/project/{id} - Get registers by project
POST   /api/risk-registers/{id}/approve - Approve register
```

### Risks
```
GET    /api/risks                  - List all risks
POST   /api/risks                  - Create new risk
GET    /api/risks/{id}             - Get risk details
PUT    /api/risks/{id}             - Update risk
DELETE /api/risks/{id}             - Delete risk
GET    /api/risks/project/{id}     - Get risks by project
GET    /api/risks/register/{id}    - Get risks by register
POST   /api/risks/{id}/assess      - Assess risk
POST   /api/risks/{id}/respond     - Add response
POST   /api/risks/{id}/monitor     - Add monitoring record
POST   /api/risks/{id}/close       - Close risk
POST   /api/risks/{id}/escalate    - Escalate risk
```

### Risk Categories
```
GET    /api/risk-categories       - List categories
POST   /api/risk-categories       - Create category
GET    /api/risk-categories/{id}  - Get category details
PUT    /api/risk-categories/{id}  - Update category
DELETE /api/risk-categories/{id}  - Delete category
GET    /api/risk-categories/tree  - Get hierarchical tree
```

### Risk Assessments
```
GET  /api/risks/{riskId}/assessments - Get all assessments for a risk
POST /api/risks/{riskId}/assessments - Create new assessment
```

### Risk Responses
```
GET  /api/risks/{riskId}/responses    - Get all responses for a risk
POST /api/risks/{riskId}/responses    - Create new response
POST /api/risk-responses/{id}/complete - Mark response as completed
```

### Risk Monitoring
```
GET  /api/risks/{riskId}/monitoring - Get all monitoring records for a risk
POST /api/risks/{riskId}/monitoring - Create new monitoring record
```

### Risk Incidents
```
GET    /api/risk-incidents          - List all incidents
POST   /api/risk-incidents          - Create new incident
GET    /api/risk-incidents/{id}     - Get incident details
PUT    /api/risk-incidents/{id}     - Update incident
DELETE /api/risk-incidents/{id}     - Delete incident
POST   /api/risk-incidents/{id}/resolve - Resolve incident
```

### Risk Reports
```
GET /api/reports/risk-summary/{projectId}      - Risk summary statistics
GET /api/reports/risk-matrix/{projectId}       - Risk matrix data
GET /api/reports/risk-heat-map/{projectId}     - Heat map data
GET /api/reports/risk-trend/{projectId}        - Trend analysis
GET /api/reports/top-risks/{projectId}         - Top 10 risks
GET /api/reports/risk-exposure/{projectId}     - Risk exposure by category
GET /api/reports/response-status/{projectId}   - Response status summary
```

## Business Rules

### Risk Score Calculation
```
Risk Score = Probability Score × Impact Score
```

Where:
- Probability Score: 1-5
- Impact Score: 1-5
- Risk Score: 1-25

### Risk Level Determination
```
Risk Level = {
  Low:      1-4
  Medium:   5-9
  High:     10-15
  Critical: 16-25
}
```

### Auto-Numbering Format
- Risk Register: `RR-YYYY-XXXX` (e.g., RR-2026-0001)
- Risk: `RSK-YYYY-XXXX` (e.g., RSK-2026-0001)
- Risk Incident: `RI-YYYY-XXXX` (e.g., RI-2026-0001)

### Risk Lifecycle
```
identified → analyzing → responding → monitoring → closed/occurred
```

### Response Strategies
1. **Avoid** - Eliminate the risk by changing the plan
2. **Mitigate** - Reduce probability or impact
3. **Transfer** - Shift responsibility to third party
4. **Accept** - Accept the risk and its consequences

## Usage Examples

### 1. Create Risk Register
```json
POST /api/risk-registers
{
  "project_id": 1,
  "name": "Project Risk Register",
  "description": "Main risk register for construction project",
  "review_frequency": "monthly",
  "company_id": 1
}
```

### 2. Create Risk
```json
POST /api/risks
{
  "risk_register_id": 1,
  "project_id": 1,
  "title": "Material Shortage",
  "description": "Risk of steel shortage affecting construction timeline",
  "category": "resource",
  "identification_date": "2026-01-09",
  "probability": "high",
  "probability_score": 4,
  "impact": "high",
  "impact_score": 4,
  "cost_impact_expected": 50000,
  "schedule_impact_days": 15,
  "response_strategy": "mitigate",
  "response_plan": "Secure alternative suppliers",
  "company_id": 1
}
```

### 3. Assess Risk
```json
POST /api/risks/1/assess
{
  "assessment_date": "2026-01-15",
  "assessment_type": "reassessment",
  "probability": "medium",
  "probability_score": 3,
  "impact": "high",
  "impact_score": 4,
  "cost_impact": 30000,
  "schedule_impact": 10,
  "justification": "Partial mitigation achieved through supplier agreements"
}
```

### 4. Add Response
```json
POST /api/risks/1/respond
{
  "response_number": "RESP-001",
  "response_type": "preventive",
  "strategy": "mitigate",
  "description": "Establish backup supplier contracts",
  "action_required": "Sign agreements with 2 backup suppliers",
  "target_date": "2026-02-01",
  "cost_of_response": 5000
}
```

### 5. Monitor Risk
```json
POST /api/risks/1/monitor
{
  "monitoring_date": "2026-01-20",
  "current_status": "Under control",
  "probability_change": "decreased",
  "impact_change": "same",
  "trigger_status": "not_triggered",
  "actions_taken": "Signed backup supplier agreements",
  "next_review_date": "2026-02-03"
}
```

## Models and Relationships

### RiskRegister
- **Belongs To:** Project, Company, User (preparedBy, approvedBy)
- **Has Many:** Risks

### Risk
- **Belongs To:** RiskRegister, Project, Company, User (identifiedBy, owner)
- **Has Many:** RiskAssessments, RiskResponses, RiskMonitoring, RiskIncidents

### RiskCategory
- **Belongs To:** Company, RiskCategory (parent)
- **Has Many:** RiskCategory (children)

### RiskAssessment
- **Belongs To:** Risk, User (assessedBy)

### RiskResponse
- **Belongs To:** Risk, User (responsible)

### RiskMonitoring
- **Belongs To:** Risk, User (monitoredBy)

### RiskIncident
- **Belongs To:** Risk, Project, Company, User (reportedBy)

### RiskMatrixSetting
- **Belongs To:** Company

## Testing

The module includes comprehensive tests covering:
- Risk register CRUD operations
- Risk CRUD operations with auto-calculations
- Risk assessment workflow
- Risk response management
- Risk monitoring
- Risk closure process
- Incident tracking
- Report generation

Run tests with:
```bash
php artisan test --filter=RiskManagement
```

## Security

- All endpoints protected with `auth:sanctum` middleware
- Company-level data isolation using `company_id`
- User authentication required for all operations
- Audit trail with timestamps and user tracking
- Soft deletes for data recovery

## Notes

- The module is separate from the existing tender risk system
- Supports multi-tenant architecture
- JSON fields allow flexible data storage
- Auto-calculations reduce manual errors
- Comprehensive reporting for decision-making
- Ready for production use

## Future Enhancements

Potential future improvements:
- Email notifications for critical risks
- Risk dashboard with charts
- Integration with project scheduling
- Risk-based cost estimation
- Mobile app support
- PDF report generation
- Risk templates library
- Machine learning for risk prediction
