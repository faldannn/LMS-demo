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
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $this->activityLogService->logCrud('user_create', $user, "User {$user->username} dibuat");

        return redirect()->route('users.index')
            ->with('success', "User {$data['name']} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $this->activityLogService->logCrud('user_update', $user, "User {$user->username} diperbarui");

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $this->activityLogService->log('user_delete', auth()->user(), $user, "User {$user->username} dihapus");

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
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
        $newPass = \Illuminate\Support\Str::random(12);
        $user->update(['password' => Hash::make($newPass)]);

        $this->sessionService->revokeAllForUser($user->id);

        $this->activityLogService->log('user_reset_password', auth()->user(), $user, "Password user {$user->username} direset");

        return back()->with('success', "Password {$user->name} berhasil direset. Semua sesi user telah dicabut. Silakan informasikan password baru melalui saluran yang aman.");
    }

    public function profile()
    {
        return view('users.profile', ['user' => Auth::user()]);
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

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}