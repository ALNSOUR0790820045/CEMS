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
        $companyId = Auth::user()?->company_id;
        
        if (!$companyId) {
            return response()->json([
                'message' => 'عذراً، يجب أن تكون مرتبطاً بشركة للوصول إلى هذه الخدمة.'
            ], 403)->throwResponse();
        }
        
        return $companyId;
    }
}
