<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\DiscussionThread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommentService
{
    public function add(Model $commentable, int $userId, string $body, ?int $parentId = null): Comment
    {
        return DB::transaction(function () use ($commentable, $userId, $body, $parentId) {
            $comment = Comment::create([
                'commentable_type' => $commentable->getMorphClass(),
                'commentable_id' => $commentable->id,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'body' => $body,
            ]);

            if ($commentable instanceof DiscussionThread && !$parentId) {
                $commentable->increment('replies_count');
            }

            return $comment->load('user');
        });
    }

    public function delete(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            if ($comment->commentable instanceof DiscussionThread && !$comment->parent_id) {
                $comment->commentable->decrement('replies_count');
            }
            $comment->delete();
        });
    }
}
