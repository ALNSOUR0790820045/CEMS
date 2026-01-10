<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Retention Percentage
    |--------------------------------------------------------------------------
    |
    | This value is used as the default retention percentage for IPCs
    | when not specified in the project contract.
    |
    */
    'default_retention_percent' => env('IPC_DEFAULT_RETENTION_PERCENT', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Default Tax Rate
    |--------------------------------------------------------------------------
    |
    | This value is used as the default tax rate for IPCs
    | when not specified explicitly.
    |
    */
    'default_tax_rate' => env('IPC_DEFAULT_TAX_RATE', 15.00),
];
