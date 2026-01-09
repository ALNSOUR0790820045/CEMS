<?php

namespace App\Http\Controllers;

use App\Models\MeasurementSheet;
use App\Models\ProgressBill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeasurementSheetController extends Controller
{
    public function index(): JsonResponse
    {
        $sheets = MeasurementSheet::with([
            'progressBill',
            'boqItem',
            'unit',
            'calculatedBy',
            'checkedBy',
        ])->paginate(15);

        return response()->json($sheets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'progress_bill_id' => 'required|exists:progress_bills,id',
            'boq_item_id' => 'nullable|exists:boq_items,id',
            'sheet_number' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'date_measured' => 'nullable|date',
            'photos' => 'nullable|array',
            'remarks' => 'nullable|string',
        ]);

        $sheet = new MeasurementSheet($validated);
        $sheet->calculated_by_id = Auth::id();
        
        // Auto-calculate quantity if dimensions provided
        if (!$validated['quantity'] && ($validated['length'] ?? false)) {
            $sheet->calculateQuantity();
        }
        
        $sheet->save();

        return response()->json($sheet->load('boqItem', 'unit'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $sheet = MeasurementSheet::with([
            'progressBill',
            'boqItem',
            'unit',
            'calculatedBy',
            'checkedBy',
        ])->findOrFail($id);

        return response()->json($sheet);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $sheet = MeasurementSheet::findOrFail($id);

        $validated = $request->validate([
            'location' => 'sometimes|string',
            'description' => 'sometimes|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'date_measured' => 'nullable|date',
            'photos' => 'nullable|array',
            'remarks' => 'nullable|string',
        ]);

        $sheet->update($validated);
        
        // Recalculate if dimensions changed
        if (isset($validated['length']) || isset($validated['width']) || isset($validated['height'])) {
            $sheet->calculateQuantity();
        }

        return response()->json($sheet->load('boqItem', 'unit'));
    }

    public function destroy(int $id): JsonResponse
    {
        $sheet = MeasurementSheet::findOrFail($id);
        $sheet->delete();

        return response()->json(['message' => 'Measurement sheet deleted successfully']);
    }

    public function byBill(int $billId): JsonResponse
    {
        $sheets = MeasurementSheet::where('progress_bill_id', $billId)
            ->with(['boqItem', 'unit', 'calculatedBy', 'checkedBy'])
            ->get();

        return response()->json($sheets);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $sheet = MeasurementSheet::findOrFail($id);
        
        $sheet->checked_by_id = Auth::id();
        $sheet->save();

        return response()->json($sheet);
    }
}
