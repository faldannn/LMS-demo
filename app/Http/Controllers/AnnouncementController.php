<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Classes;
use App\Services\AnnouncementService;

class AnnouncementController extends Controller
{
    public function __construct(private readonly AnnouncementService $announcementService)
    {
    }

    public function index()
    {
        $announcements = $this->announcementService->listForUser(auth()->user());

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $classes = Classes::orderBy('name')->get();

        return view('announcements.create', compact('classes'));
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $this->announcementService->create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'class_id' => $request->type === 'kelas' ? $request->class_id : null,
            'created_by' => auth()->id(),
            'is_published' => $request->boolean('is_published', true),
        ], $request->file('attachment'));

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load(['creator', 'class', 'comments.user', 'comments.children.user']);

        return view('announcements.show', compact('announcement'));
    }

    public function download(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        abort_unless($announcement->hasAttachment(), 404);

        return $this->announcementService->download($announcement);
    }

    public function destroy(Announcement $announcement)
    {
        if (auth()->user()->isGuru() && $announcement->created_by !== auth()->id()) {
            abort(403);
        }

        $this->announcementService->delete($announcement);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
