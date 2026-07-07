<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscussionThreadRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\ClassSubject;
use App\Models\DiscussionThread;
use App\Services\CommentService;
use App\Services\DiscussionService;

class DiscussionController extends Controller
{
    public function __construct(
        private readonly DiscussionService $discussionService,
        private readonly CommentService $commentService,
    ) {
    }

    public function index(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $threads = $this->discussionService->listForClassSubject($classSubject->id);

        return view('discussions.index', compact('classSubject', 'threads'));
    }

    public function create(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        return view('discussions.create', compact('classSubject'));
    }

    public function store(StoreDiscussionThreadRequest $request, ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $thread = $this->discussionService->create([
            'class_subject_id' => $classSubject->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('discussions.show', $thread)
            ->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function show(DiscussionThread $discussion)
    {
        $this->authorize('view', $discussion);

        $discussion->load([
            'user:id,name',
            'classSubject.class',
            'classSubject.subject',
            'comments.user',
            'comments.children.user',
        ]);

        return view('discussions.show', ['thread' => $discussion, 'classSubject' => $discussion->classSubject]);
    }

    public function reply(StoreCommentRequest $request, DiscussionThread $discussion)
    {
        $this->authorize('reply', $discussion);

        $this->commentService->add($discussion, auth()->id(), $request->body, $request->parent_id);

        return redirect()->route('discussions.show', $discussion)->with('success', 'Balasan berhasil dikirim.');
    }
}
