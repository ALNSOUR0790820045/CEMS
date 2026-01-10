<?php

namespace App\Http\Middleware;

use App\Services\TimeBarProtectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTimeBarProtection
{
    protected TimeBarProtectionService $protectionService;

    public function __construct(TimeBarProtectionService $protectionService)
    {
        $this->protectionService = $protectionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $entityType, string $action = 'edit'): Response
    {
        // Get the model instance from route parameter
        $model = $this->getModelFromRequest($request, $entityType);

        if (!$model) {
            return $next($request);
        }

        $canProceed = match ($action) {
            'edit', 'update' => $this->protectionService->canEdit($model, $entityType),
            'delete', 'destroy' => $this->protectionService->canDelete($model, $entityType),
            default => true,
        };

        if (!$canProceed) {
            $protectionInfo = $this->protectionService->getProtectionInfo($model, $entityType);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'This record is protected and cannot be modified.',
                    'protection_info' => $protectionInfo,
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'This record is protected and cannot be modified. Protection type: ' . ($protectionInfo['protection_type'] ?? 'unknown'));
        }

        return $next($request);
    }

    /**
     * Get the model instance from the request.
     */
    protected function getModelFromRequest(Request $request, string $entityType): mixed
    {
        // Try to get the model from route parameters
        $routeParameters = $request->route()->parameters();
        
        foreach ($routeParameters as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getTable')) {
                return $parameter;
            }
        }

        return null;
    }
}
