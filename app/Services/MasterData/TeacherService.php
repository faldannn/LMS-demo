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

            $nip = !empty($data['nip']) ? trim($data['nip']) : null;
            $email = !empty($data['email']) ? trim($data['email']) : null;

            if ($nip) {
                $username = $nip;
            } elseif ($email) {
                $username = $email;
            } else {
                $base = Str::slug($data['name'], '.');
                $username = $base ?: 'guru';
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = ($base ?: 'guru') . $counter;
                    $counter++;
                }
            }

            $password = !empty($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : self::DEFAULT_PASSWORD;

            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'name' => $data['name'],
                'email' => $email,
                'role' => UserRole::Guru,
                'status' => true,
            ]);

            $subjectIds = $data['subject_ids'] ?? [];
            if (!empty($subjectIds)) {
                $subjectNames = \App\Models\Subject::query()->whereIn('id', $subjectIds)->pluck('name')->toArray();
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
            $subjectNames = \App\Models\Subject::query()->whereIn('id', $subjectIds)->pluck('name')->toArray();
            $data['subject'] = implode(', ', $subjectNames);
        } else {
            $data['subject'] = null;
        }

        $password = $data['password'] ?? null;
        unset($data['password']);
        unset($data['subject_ids']);
        $teacher = parent::update($model, $data);
        $teacher->subjects()->sync($subjectIds);

        $userUpdates = [
            'name' => $data['name'],
            'email' => $data['email'] ?? $teacher->user->email,
        ];

        if (!empty($password)) {
            $userUpdates['password'] = Hash::make($password);
        }

        $teacher->user?->update($userUpdates);

        return $teacher;
    }

    public function delete(Model $model): bool
    {
        /** @var Teacher $model */
        $user = $model->user;
        $result = parent::delete($model);

        if ($user) {
            $user->delete();
        }

        return $result;
    }

    public function forceDelete(int $id): bool
    {
        $teacher = $this->find($id, true);
        $user = $teacher->user;

        // Delete the teacher model first to trigger all Eloquent events and avoid cascade race condition
        $result = parent::forceDelete($id);

        // Delete the associated user afterwards
        if ($user) {
            $user->delete();
        }

        return $result;
    }

    public function restore(int $id): Model
    {
        /** @var Teacher $teacher */
        $teacher = parent::restore($id);

        if (!$teacher->user) {
            $username = $teacher->nip ?? $teacher->email ?? \Illuminate\Support\Str::slug($teacher->name, '.');
            $password = !empty($teacher->dob) ? $teacher->dob->format('Y-m-d') : self::DEFAULT_PASSWORD;

            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'name' => $teacher->name,
                'email' => $teacher->email ?? null,
                'role' => UserRole::Guru,
                'status' => true,
            ]);
            $teacher->update(['user_id' => $user->id, 'status' => true]);
        }

        return $teacher;
    }
}
