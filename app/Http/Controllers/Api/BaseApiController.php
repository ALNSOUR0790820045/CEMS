<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseApiController extends Controller
{
    /**
     * Get the current user's company ID.
     */
    protected function getCompanyId()
    {
        return Auth::user()?->company_id ?? abort(403, 'User must be associated with a company');
    }
}
