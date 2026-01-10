<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PunchItem;
use App\Models\PunchItemComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PunchCommentController extends Controller
{
    public function byItem($itemId)
    {
        $item = PunchItem::findOrFail($itemId);
        $comments = $item->comments()->with('commentedBy')->latest()->get();

        return response()->json($comments);
    }

    public function store(Request $request, $itemId)
    {
        $item = PunchItem::findOrFail($itemId);

        $validated = $request->validate([
            'comment' => 'required|string',
            'comment_type' => 'required|in:note,query,response,rejection',
        ]);

        $validated['punch_item_id'] = $item->id;
        $validated['commented_by_id'] = Auth::id();

        $comment = PunchItemComment::create($validated);

        // Add history
        $item->addHistory('comment_added', null, $validated['comment_type'], Auth::id(), substr($validated['comment'], 0, 100));

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $comment->load('commentedBy')
        ], 201);
    }
}
