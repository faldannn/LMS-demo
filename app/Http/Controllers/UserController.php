<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\SessionManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage};

class UserController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly SessionManagementService $sessionService,
    ) {
    }

    public function index(Request $request)
    {
        $users = User::with(['teacher', 'student'])
            ->when(
                $request->search,
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $classes = \App\Models\Classes::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        return view('users.create', compact('classes', 'subjects'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request) {
            $userData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
                'status' => true,
            ];

            $user = User::create($userData);

            if ($data['role'] === 'siswa') {
                $studentData = [
                    'user_id' => $user->id,
                    'nis' => $data['nis'],
                    'nisn' => $data['nisn'] ?? null,
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'dob' => $data['dob'] ?? null,
                    'pob' => $data['pob'] ?? null,
                    'address' => $data['address'] ?? null,
                    'class_id' => $data['class_id'],
                    'parent_name' => $data['parent_name'] ?? null,
                    'parent_phone' => $data['parent_phone'] ?? null,
                    'parent_email' => $data['parent_email'] ?? null,
                    'status' => true,
                ];
                \App\Models\Student::create($studentData);
            } elseif ($data['role'] === 'guru') {
                $teacherData = [
                    'user_id' => $user->id,
                    'nip' => $data['nip'] ?? null,
                    'name' => $data['name'],
                    'dob' => $data['dob'],
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                    'status' => true,
                ];
                $teacher = \App\Models\Teacher::create($teacherData);
                if (!empty($data['subject_ids'])) {
                    $teacher->subjects()->sync($data['subject_ids']);
                }
            }

            $this->activityLogService->logCrud('user_create', $user, "User {$user->username} dibuat");

            return redirect()->route('users.index')
                ->with('success', "User {$data['name']} berhasil ditambahkan.");
        });
    }

    public function edit(User $user)
    {
        $user->load(['student', 'teacher.subjects']);
        $classes = \App\Models\Classes::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        return view('users.edit', compact('user', 'classes', 'subjects'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $user, $request) {
            $oldRole = $user->role instanceof \App\Enums\UserRole ? $user->role->value : (string) $user->role;
            $newRole = $data['role'];

            $userData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'role' => $newRole,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user->update($userData);

            // Delete old relation records if role changes
            if ($oldRole !== $newRole) {
                if ($oldRole === 'siswa' && $user->student) {
                    $user->student->delete();
                }
                if ($oldRole === 'guru' && $user->teacher) {
                    $user->teacher->delete();
                }
            }

            // Create/Update relation records
            if ($newRole === 'siswa') {
                $studentData = [
                    'nis' => $data['nis'],
                    'nisn' => $data['nisn'] ?? null,
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'dob' => $data['dob'] ?? null,
                    'pob' => $data['pob'] ?? null,
                    'address' => $data['address'] ?? null,
                    'class_id' => $data['class_id'],
                    'parent_name' => $data['parent_name'] ?? null,
                    'parent_phone' => $data['parent_phone'] ?? null,
                    'parent_email' => $data['parent_email'] ?? null,
                    'status' => (bool)($user->status),
                ];
                
                $student = $user->student;
                if ($student) {
                    $student->update($studentData);
                } else {
                    $studentData['user_id'] = $user->id;
                    \App\Models\Student::create($studentData);
                }
            } elseif ($newRole === 'guru') {
                $teacherData = [
                    'nip' => $data['nip'] ?? null,
                    'name' => $data['name'],
                    'dob' => $data['dob'],
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                    'status' => (bool)($user->status),
                ];

                $teacher = $user->teacher;
                if ($teacher) {
                    $teacher->update($teacherData);
                } else {
                    $teacherData['user_id'] = $user->id;
                    $teacher = \App\Models\Teacher::create($teacherData);
                }

                if (isset($data['subject_ids'])) {
                    $teacher->subjects()->sync($data['subject_ids']);
                }
            }

            $this->activityLogService->logCrud('user_update', $user, "User {$user->username} diperbarui");

            return redirect()->route('users.index')
                ->with('success', "User {$user->name} berhasil diperbarui.");
        });
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $this->activityLogService->log('user_delete', auth()->user(), $user, "User {$user->username} dihapus");

        if ($user->student) {
            $user->student->delete();
        }
        if ($user->teacher) {
            $user->teacher->delete();
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('user_ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter(array_map('intval', (array) $ids));

        $ids = array_values(array_diff($ids, [Auth::id()]));

        if (empty($ids)) {
            return back()->withErrors(['error' => 'Pilih setidaknya satu user untuk dihapus.']);
        }

        $users = User::with(['student', 'teacher'])->whereIn('id', $ids)->get();
        $count = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($users, &$count) {
            foreach ($users as $user) {
                if ($user->student) {
                    $user->student->delete();
                }
                if ($user->teacher) {
                    $user->teacher->delete();
                }
                $user->delete();
                $count++;
            }
        });

        $this->activityLogService->log('user_bulk_delete', auth()->user(), null, "Menghapus {$count} user secara massal");

        return back()->with('success', "{$count} user berhasil dihapus.");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);
        $label = $user->status ? 'diaktifkan' : 'dinonaktifkan';

        $this->activityLogService->log('user_toggle_status', auth()->user(), $user, "User {$user->username} {$label}");

        if (!$user->status) {
            $this->sessionService->revokeAllForUser($user->id);
        }

        return back()->with('success', "Akun {$user->name} berhasil {$label}.");
    }

    public function resetPassword(User $user)
    {
        $user->loadMissing(['student', 'teacher']);
        
        $newPass = null;
        if ($user->role === \App\Enums\UserRole::Guru && $user->teacher) {
            $newPass = $user->teacher->dob ? $user->teacher->dob->format('Y-m-d') : 'guru123';
        } elseif ($user->role === \App\Enums\UserRole::Siswa && $user->student) {
            $newPass = $user->student->dob ? $user->student->dob->format('Y-m-d') : (string) $user->student->nis;
        } else {
            $newPass = 'Password123';
        }

        $user->update(['password' => Hash::make($newPass)]);

        $this->sessionService->revokeAllForUser($user->id);

        $this->activityLogService->log('user_reset_password', auth()->user(), $user, "Password user {$user->username} direset");

        return back()->with('success', "Password {$user->name} berhasil direset ke default ({$newPass}). Semua sesi user telah dicabut.");
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load(['teacher.subjects', 'student.class']);
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => "nullable|email|unique:users,email,{$user->id}",
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar)
                Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);
        return back()->with('success', 'Password berhasil diubah. Sekarang Anda dapat menggunakan seluruh fasilitas LMS.');
    }
}