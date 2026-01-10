<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Variance Analysis Threshold
    |--------------------------------------------------------------------------
    |
    | The percentage threshold for triggering variance analysis. Any variance
    | exceeding this threshold (positive or negative) will create a variance
    | analysis record for review.
    |
    */
    'variance_threshold' => env('COST_CONTROL_VARIANCE_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Auto-generate Reports
    |--------------------------------------------------------------------------
    |
    | Enable automatic generation of monthly cost reports. When enabled,
    | the system will automatically generate cost reports at the end of
    | each month for all active projects.
    |
    */
    'auto_generate_reports' => env('COST_CONTROL_AUTO_REPORTS', false),

    /*
    |--------------------------------------------------------------------------
    | Budget Approval Required
    |--------------------------------------------------------------------------
    |
    | Require approval before budgets can be activated. When enabled,
    | budgets must go through an approval workflow before they can be
    | used to track costs.
    |
    */
    'approval_required' => env('COST_CONTROL_APPROVAL_REQUIRED', true),

    /*
    |--------------------------------------------------------------------------
    | Alert on Overrun
    |--------------------------------------------------------------------------
    |
    | Send alerts when actual costs exceed budgeted amounts by this
    | percentage. Set to null to disable overrun alerts.
    |
    */
    'overrun_alert_threshold' => env('COST_CONTROL_OVERRUN_ALERT', 10),
];
