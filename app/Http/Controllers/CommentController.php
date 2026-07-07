<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Announcement;
use App\Models\Comment;
use App\Models\Material;
use App\Services\CommentService;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService)
    {
    }

    public function store(StoreCommentRequest $request, string $type, int $id)
    {
        $model = match ($type) {
            'material' => Material::findOrFail($id),
            'announcement' => Announcement::findOrFail($id),
            default => abort(404),
        };

        if ($model instanceof Material) {
            $this->authorize('view', $model);
        }

        $this->commentService->add($model, auth()->id(), $request->body, $request->parent_id);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $this->commentService->delete($comment);

        return redirect()->back()->with('success', 'Komentar dihapus.');
    }
}
