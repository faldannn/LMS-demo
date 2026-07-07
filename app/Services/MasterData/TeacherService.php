<?php

namespace App\Services\MasterData;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\TeacherRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherService extends BaseMasterDataService
{
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

            $username = $data['nip'] ?? Str::slug($data['name'], '.');
            $password = Str::random(8);

            $user = User::create([
                'username' => $username,
                'password' => bcrypt($password),
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'role' => UserRole::Guru,
                'status' => true,
            ]);

            $data['user_id'] = $user->id;
            $teacher = parent::create($data);
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

        $teacher = parent::update($model, $data);
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
