<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoReportController extends Controller
{
    /**
     * Get purchase order status report
     */
    public function statusReport(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $statusCounts = PurchaseOrder::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total_value'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'status_breakdown' => $statusCounts,
            'summary' => [
                'total_orders' => PurchaseOrder::where('company_id', $companyId)->count(),
                'total_value' => PurchaseOrder::where('company_id', $companyId)->sum('total_amount'),
            ]
        ]);
    }

    /**
     * Get purchase orders by vendor
     */
    public function byVendor(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = PurchaseOrder::with('vendor')
            ->where('company_id', $companyId)
            ->select('vendor_id', DB::raw('count(*) as order_count'), DB::raw('sum(total_amount) as total_value'))
            ->groupBy('vendor_id');

        if ($request->has('from_date')) {
            $query->where('po_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('po_date', '<=', $request->to_date);
        }

        $vendorStats = $query->get();

        return response()->json([
            'vendor_statistics' => $vendorStats
        ]);
    }

    /**
     * Get pending deliveries report
     */
    public function pendingDeliveries(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $pendingOrders = PurchaseOrder::with(['vendor', 'items'])
            ->where('company_id', $companyId)
            ->whereIn('status', ['sent', 'partially_received'])
            ->get();

        $pendingItems = [];

        foreach ($pendingOrders as $order) {
            foreach ($order->items as $item) {
                $pendingQty = $item->quantity - ($item->quantity_received ?? 0);
                if ($pendingQty > 0) {
                    $pendingItems[] = [
                        'po_number' => $order->po_number,
                        'vendor' => $order->vendor->name,
                        'item_description' => $item->description,
                        'ordered_quantity' => $item->quantity,
                        'received_quantity' => $item->quantity_received ?? 0,
                        'pending_quantity' => $pendingQty,
                        'delivery_date' => $item->delivery_date ?? $order->delivery_date,
                    ];
                }
            }
        }

        return response()->json([
            'pending_deliveries' => $pendingItems,
            'summary' => [
                'total_pending_orders' => $pendingOrders->count(),
                'total_pending_items' => count($pendingItems),
            ]
        ]);
    }

    /**
     * Get price variance report
     */
    public function priceVariance(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = PurchaseOrderItem::with(['purchaseOrder', 'material'])
            ->whereHas('purchaseOrder', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        if ($request->has('from_date')) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('po_date', '>=', $request->from_date);
            });
        }

        if ($request->has('to_date')) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('po_date', '<=', $request->to_date);
            });
        }

        $items = $query->get();

        $varianceData = [];

        foreach ($items as $item) {
            if ($item->material) {
                $standardCost = $item->material->standard_cost ?? 0;
                $actualCost = $item->unit_price;
                $variance = $actualCost - $standardCost;
                $variancePercentage = $standardCost > 0 ? ($variance / $standardCost) * 100 : 0;

                $varianceData[] = [
                    'po_number' => $item->purchaseOrder->po_number,
                    'material_name' => $item->material->name,
                    'standard_cost' => $standardCost,
                    'actual_cost' => $actualCost,
                    'variance' => $variance,
                    'variance_percentage' => round($variancePercentage, 2),
                    'quantity' => $item->quantity,
                    'total_variance' => $variance * $item->quantity,
                ];
            }
        }

        return response()->json([
            'price_variances' => $varianceData,
            'summary' => [
                'total_items_analyzed' => count($varianceData),
                'total_variance_amount' => array_sum(array_column($varianceData, 'total_variance')),
            ]
        ]);
    }
}
