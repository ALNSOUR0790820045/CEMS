<?php

namespace App\Http\Controllers;

use App\Models\SiteReceipt;
use App\Models\SiteReceiptItem;
use App\Models\SiteReceiptPhoto;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceiptNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SiteReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SiteReceipt::with(['project', 'supplier', 'items', 'engineer', 'storekeeper', 'grn']);

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        $siteReceipts = $query->latest()->paginate(20);

        // Get all receipts with GPS for map view
        $receiptsWithGps = SiteReceipt::select('id', 'receipt_number', 'latitude', 'longitude', 'location_name', 'status')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $projects = Project::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();

        return view('site-receipts.index', compact('siteReceipts', 'receiptsWithGps', 'projects', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'partial_received'])->get();

        return view('site-receipts.create', compact('projects', 'suppliers', 'products', 'purchaseOrders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'receipt_time' => 'required',
            'vehicle_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string',
            'invoice_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'delivery_note' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'packing_list' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'quality_certificates.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'engineer_id' => 'required|exists:users,id',
            'engineer_signature' => 'required|string',
            'engineer_notes' => 'nullable|string',
            'storekeeper_id' => 'required|exists:users,id',
            'storekeeper_signature' => 'required|string',
            'storekeeper_notes' => 'nullable|string',
            'driver_signature_name' => 'required|string',
            'driver_signature' => 'required|string',
            'general_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate receipt number
            $receiptNumber = $this->generateReceiptNumber();

            // Upload documents
            $invoicePath = $request->file('invoice_document')->store('site-receipts/invoices', 'public');
            $deliveryNotePath = $request->file('delivery_note')->store('site-receipts/delivery-notes', 'public');
            $packingListPath = $request->file('packing_list')->store('site-receipts/packing-lists', 'public');

            $qualityCertificates = [];
            if ($request->hasFile('quality_certificates')) {
                foreach ($request->file('quality_certificates') as $cert) {
                    $qualityCertificates[] = $cert->store('site-receipts/quality-certificates', 'public');
                }
            }

            // Create site receipt
            $siteReceipt = SiteReceipt::create([
                'project_id' => $validated['project_id'],
                'supplier_id' => $validated['supplier_id'],
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'receipt_number' => $receiptNumber,
                'receipt_date' => $validated['receipt_date'],
                'receipt_time' => $validated['receipt_time'],
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'location_name' => $validated['location_name'],
                'gps_captured_at' => now(),
                'invoice_document' => $invoicePath,
                'delivery_note' => $deliveryNotePath,
                'packing_list' => $packingListPath,
                'quality_certificates' => $qualityCertificates,
                'status' => 'pending_verification',
                'engineer_id' => $validated['engineer_id'],
                'engineer_signature' => $validated['engineer_signature'],
                'engineer_signed_at' => now(),
                'engineer_notes' => $validated['engineer_notes'] ?? null,
                'storekeeper_id' => $validated['storekeeper_id'],
                'storekeeper_signature' => $validated['storekeeper_signature'],
                'storekeeper_signed_at' => now(),
                'storekeeper_notes' => $validated['storekeeper_notes'] ?? null,
                'driver_signature_name' => $validated['driver_signature_name'],
                'driver_signature' => $validated['driver_signature'],
                'driver_signed_at' => now(),
                'general_notes' => $validated['general_notes'] ?? null,
            ]);

            // Create items
            foreach ($validated['items'] as $item) {
                SiteReceiptItem::create([
                    'site_receipt_id' => $siteReceipt->id,
                    'product_id' => $item['product_id'],
                    'po_item_id' => $item['po_item_id'] ?? null,
                    'ordered_quantity' => $item['ordered_quantity'] ?? 0,
                    'received_quantity' => $item['received_quantity'],
                    'accepted_quantity' => $item['accepted_quantity'] ?? $item['received_quantity'],
                    'rejected_quantity' => $item['rejected_quantity'] ?? 0,
                    'unit' => $item['unit'],
                    'condition' => $item['condition'] ?? 'good',
                    'condition_notes' => $item['condition_notes'] ?? null,
                    'batch_number' => $item['batch_number'] ?? null,
                    'serial_number' => $item['serial_number'] ?? null,
                    'manufacturing_date' => $item['manufacturing_date'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
            }

            // Save photos if provided
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $photoPath = $photo->store('site-receipts/photos', 'public');
                    $photoData = $request->input('photos_data')[$index] ?? [];

                    $hash = SiteReceiptPhoto::generateHash(
                        $photoPath,
                        $photoData['latitude'] ?? $validated['latitude'],
                        $photoData['longitude'] ?? $validated['longitude'],
                        now()->toIso8601String()
                    );

                    SiteReceiptPhoto::create([
                        'site_receipt_id' => $siteReceipt->id,
                        'photo_path' => $photoPath,
                        'title' => $photoData['title'] ?? null,
                        'latitude' => $photoData['latitude'] ?? $validated['latitude'],
                        'longitude' => $photoData['longitude'] ?? $validated['longitude'],
                        'captured_at' => now(),
                        'device_info' => $photoData['device_info'] ?? null,
                        'hash' => $hash,
                        'verified' => true,
                        'category' => $photoData['category'] ?? 'materials',
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Auto-create GRN if all signatures are complete
            if ($siteReceipt->hasAllSignatures()) {
                $this->createAutoGRN($siteReceipt);
            }

            DB::commit();

            return redirect()->route('site-receipts.show', $siteReceipt)
                ->with('success', 'تم إنشاء استلام الموقع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteReceipt $siteReceipt)
    {
        $siteReceipt->load([
            'project',
            'supplier',
            'purchaseOrder',
            'engineer',
            'storekeeper',
            'grn',
            'items.product',
            'photos.uploadedBy'
        ]);

        return view('site-receipts.show', compact('siteReceipt'));
    }

    /**
     * Show the verification form.
     */
    public function verify(SiteReceipt $siteReceipt)
    {
        $siteReceipt->load([
            'project',
            'supplier',
            'purchaseOrder',
            'engineer',
            'storekeeper',
            'items.product',
            'photos'
        ]);

        return view('site-receipts.verify', compact('siteReceipt'));
    }

    /**
     * Process verification.
     */
    public function processVerification(Request $request, SiteReceipt $siteReceipt)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($validated['action'] === 'approve') {
                $siteReceipt->update([
                    'status' => 'verified',
                    'general_notes' => ($siteReceipt->general_notes ?? '') . "\n\nموافقة المدير: " . ($validated['notes'] ?? 'تم الموافقة'),
                ]);

                // Create GRN if not already created
                if (!$siteReceipt->grn_id) {
                    $this->createAutoGRN($siteReceipt);
                }
            } else {
                $siteReceipt->update([
                    'status' => 'rejected',
                    'general_notes' => ($siteReceipt->general_notes ?? '') . "\n\nرفض المدير: " . ($validated['notes'] ?? 'تم الرفض'),
                ]);
            }

            DB::commit();

            return redirect()->route('site-receipts.show', $siteReceipt)
                ->with('success', 'تم معالجة التحقق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique receipt number.
     */
    private function generateReceiptNumber(): string
    {
        $year = date('Y');
        $lastReceipt = SiteReceipt::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastReceipt ? intval(substr($lastReceipt->receipt_number, -3)) + 1 : 1;

        return 'SR-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Auto-create GRN from site receipt.
     */
    private function createAutoGRN(SiteReceipt $siteReceipt): void
    {
        // Generate GRN number
        $year = date('Y');
        $lastGrn = GoodsReceiptNote::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastGrn ? intval(substr($lastGrn->grn_number, -3)) + 1 : 1;
        $grnNumber = 'GRN-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Create GRN
        $grn = GoodsReceiptNote::create([
            'project_id' => $siteReceipt->project_id,
            'purchase_order_id' => $siteReceipt->purchase_order_id,
            'supplier_id' => $siteReceipt->supplier_id,
            'grn_number' => $grnNumber,
            'receipt_date' => $siteReceipt->receipt_date,
            'status' => 'verified',
            'notes' => 'تم الإنشاء تلقائياً من استلام موقع رقم: ' . $siteReceipt->receipt_number,
            'received_by' => $siteReceipt->storekeeper_id,
            'verified_by' => $siteReceipt->engineer_id,
            'verified_at' => now(),
            'inventory_updated' => true,
        ]);

        // Update site receipt with GRN
        $siteReceipt->update([
            'grn_id' => $grn->id,
            'auto_grn_created' => true,
            'grn_created_at' => now(),
            'status' => 'grn_created',
            'finance_notified' => true,
            'finance_notified_at' => now(),
            'payment_status' => 'ready_for_payment',
        ]);
    }

    /**
     * Get PO items via AJAX.
     */
    public function getPOItems(PurchaseOrder $purchaseOrder)
    {
        $items = $purchaseOrder->items()->with('product')->get();
        return response()->json($items);
    }
}

