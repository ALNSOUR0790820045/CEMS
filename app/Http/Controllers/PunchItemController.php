<?php

namespace App\Http\Controllers;

use App\Models\PunchItem;
use App\Models\PunchList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PunchItemController extends Controller
{
    public function index()
    {
        $items = PunchItem::with(['punchList', 'assignedTo', 'verifiedBy'])
            ->latest()
            ->paginate(20);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'punch_list_id' => 'required|exists:punch_lists,id',
            'location' => 'nullable|string',
            'room_number' => 'nullable|string',
            'grid_reference' => 'nullable|string',
            'element' => 'nullable|string',
            'description' => 'required|string',
            'category' => 'required|in:defect,incomplete,damage,missing,wrong',
            'severity' => 'required|in:minor,major,critical',
            'discipline' => 'nullable|in:architectural,structural,electrical,mechanical,plumbing,fire,hvac',
            'trade' => 'nullable|string',
            'responsible_party' => 'nullable|string',
            'assigned_to_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'cost_to_rectify' => 'nullable|numeric|min:0',
            'back_charge' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $list = PunchList::findOrFail($validated['punch_list_id']);

            // Generate item number
            $sequence = $list->items()->count() + 1;
            $validated['item_number'] = $list->list_number.'-'.str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $validated['status'] = 'open';

            $item = PunchItem::create($validated);

            // Add history
            $item->addHistory('created', null, 'open', Auth::id(), 'Item created');

            // Update list statistics
            $list->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Punch item created successfully',
                'data' => $item->load(['punchList', 'assignedTo'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $item = PunchItem::with([
            'punchList.project',
            'assignedTo',
            'verifiedBy',
            'comments.commentedBy',
            'history.performedBy'
        ])->findOrFail($id);

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        $validated = $request->validate([
            'location' => 'nullable|string',
            'room_number' => 'nullable|string',
            'grid_reference' => 'nullable|string',
            'element' => 'nullable|string',
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:defect,incomplete,damage,missing,wrong',
            'severity' => 'sometimes|in:minor,major,critical',
            'discipline' => 'nullable|in:architectural,structural,electrical,mechanical,plumbing,fire,hvac',
            'trade' => 'nullable|string',
            'responsible_party' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'cost_to_rectify' => 'nullable|numeric|min:0',
            'back_charge' => 'nullable|boolean',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Punch item updated successfully',
            'data' => $item->fresh(['punchList', 'assignedTo'])
        ]);
    }

    public function destroy($id)
    {
        $item = PunchItem::findOrFail($id);
        $list = $item->punchList;
        
        $item->delete();

        // Update list statistics
        $list->updateStatistics();

        return response()->json(['message' => 'Punch item deleted successfully']);
    }

    public function byList($listId)
    {
        $items = PunchItem::with(['assignedTo', 'verifiedBy'])
            ->where('punch_list_id', $listId)
            ->latest()
            ->get();

        return response()->json($items);
    }

    public function assign(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        $validated = $request->validate([
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $oldAssignee = $item->assigned_to_id;
            $item->update($validated);

            // Add history
            $item->addHistory(
                'assigned',
                $oldAssignee ? (string)$oldAssignee : null,
                (string)$validated['assigned_to_id'],
                Auth::id(),
                'Item assigned'
            );

            DB::commit();

            return response()->json([
                'message' => 'Punch item assigned successfully',
                'data' => $item->fresh(['assignedTo'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to assign punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        if (!in_array($item->status, ['open', 'in_progress'])) {
            return response()->json(['error' => 'Only open or in-progress items can be completed'], 400);
        }

        $validated = $request->validate([
            'completion_remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'completed',
                'completed_date' => now(),
                'completion_remarks' => $validated['completion_remarks'] ?? null,
            ]);

            // Add history
            $item->addHistory('status_changed', $item->getOriginal('status'), 'completed', Auth::id(), 'Item completed');

            // Update list statistics
            $item->punchList->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Punch item completed successfully',
                'data' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to complete punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        if ($item->status !== 'completed') {
            return response()->json(['error' => 'Only completed items can be verified'], 400);
        }

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'verified',
                'verified_date' => now(),
                'verified_by_id' => Auth::id(),
            ]);

            // Add history
            $item->addHistory('verified', 'completed', 'verified', Auth::id(), 'Item verified');

            // Update list statistics
            $item->punchList->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Punch item verified successfully',
                'data' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to verify punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        if ($item->status !== 'completed') {
            return response()->json(['error' => 'Only completed items can be rejected'], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $item->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Add history
            $item->addHistory('rejected', 'completed', 'rejected', Auth::id(), $validated['rejection_reason']);

            // Update list statistics
            $item->punchList->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Punch item rejected successfully',
                'data' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to reject punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function reopen(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        if (!in_array($item->status, ['rejected', 'completed'])) {
            return response()->json(['error' => 'Only rejected or completed items can be reopened'], 400);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $item->status;
            $item->update([
                'status' => 'in_progress',
                'rejection_reason' => null,
            ]);

            // Add history
            $item->addHistory('status_changed', $oldStatus, 'in_progress', Auth::id(), 'Item reopened');

            // Update list statistics
            $item->punchList->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Punch item reopened successfully',
                'data' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to reopen punch item', 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadPhotos(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $photos = $item->photos ?? [];

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('punch-items/'.$item->id.'/photos', 'public');
            $photos[] = $path;
        }

        $item->update(['photos' => $photos]);

        // Add history
        $item->addHistory('photo_added', null, 'Photos uploaded', Auth::id());

        return response()->json([
            'message' => 'Photos uploaded successfully',
            'data' => $item->fresh()
        ]);
    }

    public function uploadCompletionPhotos(Request $request, $id)
    {
        $item = PunchItem::findOrFail($id);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $photos = $item->completion_photos ?? [];

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('punch-items/'.$item->id.'/completion', 'public');
            $photos[] = $path;
        }

        $item->update(['completion_photos' => $photos]);

        // Add history
        $item->addHistory('photo_added', null, 'Completion photos uploaded', Auth::id());

        return response()->json([
            'message' => 'Completion photos uploaded successfully',
            'data' => $item->fresh()
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:punch_items,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:open,in_progress,completed,verified,rejected,disputed',
            'updates.assigned_to_id' => 'sometimes|exists:users,id',
            'updates.priority' => 'sometimes|in:low,medium,high,urgent',
            'updates.due_date' => 'sometimes|date',
        ]);

        DB::beginTransaction();
        try {
            $items = PunchItem::whereIn('id', $validated['item_ids'])->get();

            foreach ($items as $item) {
                $item->update($validated['updates']);

                // Add history
                $item->addHistory('status_changed', null, json_encode($validated['updates']), Auth::id(), 'Bulk update');
            }

            // Update list statistics for affected lists
            $affectedLists = $items->pluck('punch_list_id')->unique();
            PunchList::whereIn('id', $affectedLists)->get()->each->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Items updated successfully',
                'updated_count' => count($validated['item_ids'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to bulk update items', 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:punch_items,id',
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $items = PunchItem::whereIn('id', $validated['item_ids'])->get();

            foreach ($items as $item) {
                $item->update(['assigned_to_id' => $validated['assigned_to_id']]);

                // Add history
                $item->addHistory('assigned', null, (string)$validated['assigned_to_id'], Auth::id(), 'Bulk assignment');
            }

            DB::commit();

            return response()->json([
                'message' => 'Items assigned successfully',
                'assigned_count' => count($validated['item_ids'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to bulk assign items', 'message' => $e->getMessage()], 500);
        }
    }

    public function history($itemId)
    {
        $item = PunchItem::findOrFail($itemId);
        $history = $item->history()->with('performedBy')->latest('performed_at')->get();

        return response()->json($history);
    }
}
