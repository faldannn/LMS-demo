<?php

namespace App\Services\MasterData;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\TeacherRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherService extends BaseMasterDataService
{
    private const DEFAULT_PASSWORD = 'guru123';

    public function __construct(TeacherRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['photo']) && is_object($data['photo'])) {
                $data['photo'] = $data['photo']->store('teachers/photos', 'public');
            }

            $username = $data['nip'] ?? $data['email'] ?? Str::slug($data['name'], '.');
            $password = !empty($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : self::DEFAULT_PASSWORD;

            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'role' => UserRole::Guru,
                'status' => true,
            ]);

            $subjectIds = $data['subject_ids'] ?? [];
            if (!empty($subjectIds)) {
                $subjectNames = \App\Models\Subject::whereIn('id', $subjectIds)->pluck('name')->toArray();
                $data['subject'] = implode(', ', $subjectNames);
            } else {
                $data['subject'] = null;
            }

            $data['user_id'] = $user->id;
            unset($data['subject_ids']);
            $teacher = parent::create($data);
            
            if (!empty($subjectIds)) {
                $teacher->subjects()->sync($subjectIds);
            }

            $teacher->setAttribute('generated_password', $password);
            $teacher->setAttribute('generated_username', $username);

            return $teacher;
        });
    }

    public function update(Model $model, array $data): Model
    {
        /** @var Teacher $model */
        if (isset($data['photo']) && is_object($data['photo'])) {
            if ($model->photo) {
                Storage::disk('public')->delete($model->photo);
            }
            $data['photo'] = $data['photo']->store('teachers/photos', 'public');
        }

        $subjectIds = $data['subject_ids'] ?? [];
        if (!empty($subjectIds)) {
            $subjectNames = \App\Models\Subject::whereIn('id', $subjectIds)->pluck('name')->toArray();
            $data['subject'] = implode(', ', $subjectNames);
        } else {
            $data['subject'] = null;
        }

        unset($data['subject_ids']);
        $teacher = parent::update($model, $data);
        $teacher->subjects()->sync($subjectIds);

        $teacher->user?->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? $teacher->user->email,
        ]);

        return $teacher;
    }

    public function delete(Model $model): bool
    {
        /** @var Teacher $model */
        return parent::delete($model);
    }

    public function forceDelete(int $id): bool
    {
        $teacher = $this->find($id, true);
        $teacher->user?->forceDelete();

        return parent::forceDelete($id);
    }
}
