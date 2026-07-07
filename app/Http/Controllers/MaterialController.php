<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\ClassSubject;
use App\Models\Material;
use App\Services\MaterialService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(private readonly MaterialService $materialService) {}

    public function index(ClassSubject $classSubject)
    {
        $this->authorize('view', $classSubject);

        $materials = $this->materialService->getByClassSubject($classSubject->id);

        return view('materials.index', compact('classSubject', 'materials'));
    }

    public function create(ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        return view('materials.create', compact('classSubject'));
    }

    public function store(StoreMaterialRequest $request, ClassSubject $classSubject)
    {
        $this->authorize('manage', $classSubject);

        $data = $request->validated();
        $data['class_subject_id'] = $classSubject->id;
        $data['created_by'] = auth()->id();
        $data['published_at'] = !empty($data['is_published']) ? now() : null;
        $data = $this->materialService->prepareData($data, $request->file('file'));

        unset($data['file']);
        Material::create($data);

        return redirect()->route('materials.index', $classSubject)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function show(Material $material)
    {
        $this->authorize('view', $material);

        $material->load(['classSubject.subject', 'classSubject.class', 'creator', 'comments.user', 'comments.children.user']);

        return view('materials.show', compact('material'));
    }

    public function download(Material $material)
    {
        $this->authorize('view', $material);
        abort_unless($material->isDownloadable(), 404);

        return $this->materialService->download($material);
    }

    public function edit(Material $material)
    {
        $this->authorize('update', $material);

        $classSubject = $material->classSubject;

        return view('materials.edit', compact('material', 'classSubject'));
    }

    public function update(StoreMaterialRequest $request, Material $material)
    {
        $this->authorize('update', $material);

        $data = $request->validated();

        if (!empty($data['is_published']) && !$material->is_published) {
            $data['published_at'] = now();
        } elseif (empty($data['is_published'])) {
            $data['published_at'] = null;
        }

        if ($request->hasFile('file')) {
            $this->materialService->deleteFile($material->file_path);
        }

        $data = $this->materialService->prepareData($data, $request->file('file'));
        unset($data['file']);
        $material->update($data);

        return redirect()->route('materials.index', $material->class_subject_id)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $this->authorize('delete', $material);

        $classSubjectId = $material->class_subject_id;
        $this->materialService->deleteFile($material->file_path);
        $material->delete();

        return redirect()->route('materials.index', $classSubjectId)
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function togglePublish(Material $material)
    {
        $this->authorize('update', $material);

        $isPublished = !$material->is_published;
        $material->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Status publikasi materi berhasil diubah.');
    }

    public function studentMaterials()
    {
        $student = auth()->user()->student;

        if (!$student || !$student->class_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar dalam kelas apapun.');
        }

        $classSubjects = ClassSubject::with(['subject', 'teacher', 'materials' => function ($query) {
            $query->where('is_published', true)->orderBy('order')->orderBy('created_at', 'desc');
        }])
            ->where('class_id', $student->class_id)
            ->get();

        return view('materials.student-materials', compact('classSubjects'));
    }
}
