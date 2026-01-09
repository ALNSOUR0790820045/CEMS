# Financial Regret Index API Documentation

## Overview
The Financial Regret Index module is a powerful negotiation tool that calculates the financial cost of terminating a contract versus continuing with the current contractor.

### Formula
```
Regret Index = (Cost to Terminate) - (Cost to Continue)
```
- **Positive value** = Better to continue with current contractor
- **Negative value** = Termination might be more economical

---

## API Endpoints

All endpoints require authentication using Laravel Sanctum tokens.

Base URL: `/api/regret-index`

### 1. List All Analyses
**GET** `/api/regret-index`

Get a paginated list of all regret index analyses.

**Query Parameters:**
- `project_id` (optional) - Filter by project ID
- `contract_id` (optional) - Filter by contract ID
- `from_date` (optional) - Filter analyses from this date (YYYY-MM-DD)
- `to_date` (optional) - Filter analyses to this date (YYYY-MM-DD)
- `per_page` (optional) - Results per page (default: 15)

**Response Example:**
```json
{
  "data": [
    {
      "id": 1,
      "analysis_number": "FRA-20260104-0001",
      "project_id": 1,
      "contract_id": 1,
      "analysis_date": "2026-01-04",
      "regret_index": "150000.00",
      "regret_percentage": "15.00",
      "recommendation": "continue",
      "project": { ... },
      "contract": { ... }
    }
  ],
  "meta": { ... }
}
```

---

### 2. Calculate New Analysis
**POST** `/api/regret-index/calculate`

Create a new regret index analysis with automatic calculations.

**Request Body:**
```json
{
  "project_id": 1,
  "contract_id": 1,
  "analysis_date": "2026-01-04",
  
  // Current project status
  "work_completed_value": 500000.00,
  "work_completed_percentage": 50.00,
  "elapsed_days": 180,
  
  // Continuation costs
  "continuation_remaining_cost": 500000.00,
  "continuation_claims_estimate": 50000.00,
  "continuation_variations": 20000.00,
  
  // Termination costs
  "termination_payment_due": 550000.00,
  "termination_demobilization": 30000.00,
  "termination_claims": 100000.00,
  "termination_legal_costs": 50000.00,
  
  // New contractor costs
  "new_contractor_mobilization": 80000.00,
  "new_contractor_learning_curve": 40000.00,
  "new_contractor_premium": 50000.00,
  "new_contractor_remaining_work": 520000.00,
  
  // Delay costs
  "estimated_delay_days": 60,
  "delay_cost_per_day": 1000.00,
  
  // Optional
  "analysis_notes": "Notes about the analysis",
  "negotiation_points": "Key points for negotiation",
  "reviewed_by": 2
}
```

**Required Fields:**
- `project_id`, `contract_id`, `analysis_date`
- `work_completed_value`, `work_completed_percentage`, `elapsed_days`
- `continuation_remaining_cost`
- `termination_payment_due`
- `new_contractor_mobilization`, `new_contractor_learning_curve`
- `new_contractor_premium`, `new_contractor_remaining_work`
- `estimated_delay_days`, `delay_cost_per_day`

**Response Example:**
```json
{
  "message": "تم حساب مؤشر الندم المالي بنجاح",
  "data": {
    "id": 1,
    "analysis_number": "FRA-20260104-0001",
    "regret_index": "150000.00",
    "regret_percentage": "15.00",
    "cost_to_continue": "570000.00",
    "cost_to_terminate": "720000.00",
    "recommendation": "continue",
    ...
  }
}
```

---

### 3. Get Single Analysis
**GET** `/api/regret-index/{id}`

Retrieve a specific analysis with all details including scenarios.

**Response Example:**
```json
{
  "id": 1,
  "analysis_number": "FRA-20260104-0001",
  "project": { ... },
  "contract": { ... },
  "preparedBy": { ... },
  "reviewedBy": { ... },
  "scenarios": [
    {
      "id": 1,
      "scenario_name": "Optimistic Scenario",
      "scenario_type": "optimistic",
      "regret_index": "180000.00",
      "assumptions": { ... }
    }
  ],
  "continuation_total": "570000.00",
  "termination_total": "730000.00",
  "new_contractor_total": "690000.00",
  "total_delay_cost": "60000.00",
  "cost_to_continue": "570000.00",
  "cost_to_terminate": "720000.00",
  "regret_index": "150000.00",
  "regret_percentage": "15.00",
  "recommendation": "continue"
}
```

---

### 4. Add Scenario
**POST** `/api/regret-index/{id}/scenarios`

Add alternative scenarios (optimistic, realistic, pessimistic) to an analysis.

**Request Body:**
```json
{
  "scenario_name": "Pessimistic Scenario",
  "scenario_type": "pessimistic",
  "assumptions": {
    "continuation_cost_multiplier": 1.2,
    "continuation_claims_multiplier": 1.5,
    "continuation_variations_multiplier": 1.3,
    "termination_payment_multiplier": 1.1,
    "termination_claims_multiplier": 2.0,
    "termination_legal_multiplier": 1.5,
    "new_contractor_work_multiplier": 1.3,
    "delay_days_multiplier": 1.5
  }
}
```

**Scenario Types:**
- `optimistic` - Best case scenario
- `realistic` - Most likely scenario
- `pessimistic` - Worst case scenario

**Multiplier Fields:**
All multipliers are optional and default to 1.0 (no change):
- `continuation_cost_multiplier` - Multiplies continuation remaining cost
- `continuation_claims_multiplier` - Multiplies continuation claims estimate
- `continuation_variations_multiplier` - Multiplies continuation variations
- `termination_payment_multiplier` - Multiplies termination payment due
- `termination_claims_multiplier` - Multiplies termination claims
- `termination_legal_multiplier` - Multiplies legal costs
- `new_contractor_work_multiplier` - Multiplies new contractor work cost
- `delay_days_multiplier` - Multiplies estimated delay days

**Response Example:**
```json
{
  "message": "تم إضافة السيناريو بنجاح",
  "data": {
    "id": 2,
    "analysis_id": 1,
    "scenario_name": "Pessimistic Scenario",
    "scenario_type": "pessimistic",
    "regret_index": "95000.00",
    "assumptions": { ... }
  }
}
```

---

### 5. Export to PDF
**GET** `/api/regret-index/{id}/export`

Export the analysis as a professional PDF report in Arabic (RTL).

**Response:**
- Content-Type: `application/pdf`
- File download: `regret-index-{analysis_number}.pdf`

**PDF Contents:**
- Project and contract information
- Cost breakdown (continuation, termination, new contractor, delay)
- Final results and recommendation
- All scenarios
- Negotiation points
- Analysis notes

---

### 6. Get Presentation Data
**GET** `/api/regret-index/{id}/presentation`

Get formatted data optimized for client presentations.

**Response Example:**
```json
{
  "analysis": { ... },
  "summary": {
    "contract_value": "1,000,000.00",
    "work_completed": "50.0%",
    "cost_to_continue": "570,000.00",
    "cost_to_terminate": "720,000.00",
    "regret_index": "150,000.00",
    "regret_percentage": "15.0%",
    "recommendation": "يُوصى بالاستمرار مع المقاول الحالي"
  },
  "cost_breakdown": {
    "continuation": { ... },
    "termination": { ... },
    "new_contractor": { ... },
    "delay": { ... }
  },
  "scenarios": [
    {
      "name": "Optimistic Scenario",
      "type": "optimistic",
      "regret_index": "180,000.00",
      "assumptions": { ... }
    }
  ],
  "negotiation_points": [
    "Point 1",
    "Point 2"
  ]
}
```

---

## Recommendations

The system automatically generates recommendations based on the regret index:

| Recommendation | Meaning | Condition |
|---------------|---------|-----------|
| `continue` | يُوصى بالاستمرار مع المقاول الحالي | Regret index > 20% and positive |
| `negotiate` | يُوصى بإعادة التفاوض | Regret index between 10-20% |
| `review` | يتطلب مراجعة دقيقة | Regret index < 10% or negative |

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "Validation error",
  "errors": {
    "project_id": ["The project id field is required."]
  }
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\FinancialRegretAnalysis] {id}"
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

---

## Example Usage

### Complete Workflow

1. **Create a new analysis:**
```bash
curl -X POST http://localhost/api/regret-index/calculate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

2. **Add scenarios:**
```bash
curl -X POST http://localhost/api/regret-index/1/scenarios \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "scenario_name": "Pessimistic",
    "scenario_type": "pessimistic",
    "assumptions": {
      "continuation_claims_multiplier": 1.5,
      "termination_claims_multiplier": 2.0
    }
  }'
```

3. **Export to PDF:**
```bash
curl -X GET http://localhost/api/regret-index/1/export \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -o report.pdf
```

4. **Get presentation data:**
```bash
curl -X GET http://localhost/api/regret-index/1/presentation \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Database Schema

### `financial_regret_analyses` Table
- Complete analysis record with all cost components
- Auto-calculated totals and recommendations
- Links to project, contract, and users

### `regret_index_scenarios` Table
- Alternative scenarios with multipliers
- JSON assumptions storage
- Auto-calculated regret index per scenario

### Supporting Tables
- `projects` - Project master data
- `contracts` - Contract details and values
- `users` - User information for prepared_by and reviewed_by

---

## RTL Support

The module fully supports Arabic (RTL) content:
- PDF reports are generated in RTL format
- All Arabic labels and recommendations
- Proper number formatting for Arabic context

---

## Notes

1. The analysis_number is auto-generated in format: `FRA-YYYYMMDD-####`
2. All monetary values use decimal(18,2) precision
3. Percentages use decimal(5,2) precision
4. Currency defaults to 'JOD' but is configurable per analysis
5. The calculation is done automatically when creating an analysis
6. Scenarios inherit the base analysis values and apply multipliers
