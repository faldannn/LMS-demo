<?php

namespace App\Services\MasterData;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentService extends BaseMasterDataService
{
    public function __construct(StudentRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['photo']) && is_object($data['photo'])) {
                $data['photo'] = $data['photo']->store('students/photos', 'public');
            }

            $password = null;
            if (!empty($data['create_account'])) {
                $password = (string) $data['nis'];
                $user = User::create([
                    'username' => $data['nis'],
                    'password' => Hash::make($password),
                    'name' => $data['name'],
                    'email' => $data['parent_email'] ?? null,
                    'role' => UserRole::Siswa,
                    'status' => true,
                ]);
                $data['user_id'] = $user->id;
            }
            unset($data['create_account']);

            $student = parent::create($data);
            if ($password) {
                $student->setAttribute('generated_password', $password);
            }

            return $student;
        });
    }

    public function update(Model $model, array $data): Model
    {
        /** @var Student $model */
        if (isset($data['photo']) && is_object($data['photo'])) {
            if ($model->photo) {
                Storage::disk('public')->delete($model->photo);
            }
            $data['photo'] = $data['photo']->store('students/photos', 'public');
        }
        unset($data['create_account']);

        return parent::update($model, $data);
    }
}
