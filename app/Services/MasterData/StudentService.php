<?php

namespace App\Services\MasterData;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
                $password = !empty($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : (string) ($data['nisn'] ?? $data['nis']);
                $data['status'] = true;
                try {
                    $user = User::create([
                        'username' => $data['nisn'] ?? $data['nis'],
                        'password' => Hash::make($password),
                        'name' => $data['name'],
                        'email' => $data['parent_email'] ?? null,
                        'role' => UserRole::Siswa,
                        'status' => true,
                        'password_changed_at' => null,
                    ]);
                    $data['user_id'] = $user->id;
                } catch (UniqueConstraintViolationException | QueryException $e) {
                    if (str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), 'users_username_unique')) {
                        throw ValidationException::withMessages([
                            'nis' => 'NIS ini sudah terdaftar pada data akun pengguna (username).',
                        ]);
                    }
                    throw $e;
                }
            } else {
                $data['status'] = false;
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
        $createAccount = !empty($data['create_account']);
        $password = $data['password'] ?? null;
        unset($data['create_account'], $data['password']);

        $student = parent::update($model, $data);

        $userUpdates = [
            'username' => $student->nisn ?: $student->nis,
            'name' => $student->name,
            'status' => (bool) ($data['status'] ?? false),
        ];
        if (!empty($data['parent_email'])) {
            $userUpdates['email'] = $data['parent_email'];
        }
        if (!empty($password)) {
            $userUpdates['password'] = Hash::make($password);
            $userUpdates['password_changed_at'] = now();
        }

        if ($student->user) {
            $student->user->update($userUpdates);
        } else {
            $passToUse = !empty($password) ? $password : (!empty($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : (string) ($student->nisn ?: $student->nis));
            $user = User::create([
                'username' => $student->nisn ?: $student->nis,
                'password' => Hash::make($passToUse),
                'name' => $student->name,
                'email' => $student->parent_email ?? null,
                'role' => UserRole::Siswa,
                'status' => true,
                'password_changed_at' => empty($password) ? null : now(),
            ]);
            $student->update([
                'user_id' => $user->id,
                'status' => true,
            ]);
        }

        return $student;
    }

    public function delete(Model $model): bool
    {
        /** @var Student $model */
        $user = $model->user;
        $result = parent::delete($model);

        if ($user) {
            $user->delete();
        }

        return $result;
    }

    public function forceDelete(int $id): bool
    {
        $student = $this->find($id, true);
        $user = $student->user;

        $result = parent::forceDelete($id);

        if ($user) {
            $user->delete();
        }

        return $result;
    }

    public function restore(int $id): Model
    {
        /** @var Student $student */
        $student = parent::restore($id);

        if (!$student->user) {
            $password = !empty($student->dob) ? $student->dob->format('Y-m-d') : (string) ($student->nisn ?: $student->nis);
            $user = User::create([
                'username' => $student->nisn ?: $student->nis,
                'password' => Hash::make($password),
                'name' => $student->name,
                'email' => $student->parent_email ?? null,
                'role' => UserRole::Siswa,
                'status' => true,
                'password_changed_at' => null,
            ]);
            $student->update([
                'user_id' => $user->id,
                'status' => true,
            ]);
        }

        return $student;
    }
}
