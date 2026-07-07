<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\SessionManagementService;
use Illuminate\Http\Request;

class SessionManagementController extends Controller
{
  public function __construct(
    private readonly SessionManagementService $sessionService,
    private readonly ActivityLogService $activityLogService,
  ) {
  }

  public function index(Request $request)
  {
    $userId = $request->integer('user_id') ?: null;

    $sessions = $this->sessionService->activeSessions($userId);
    $users = User::orderBy('name')->get(['id', 'name', 'username']);

    return view('security.sessions', compact('sessions', 'users', 'userId'));
  }

  public function destroy(Request $request, string $sessionId)
  {
    $revoked = $this->sessionService->revoke($sessionId);

    if ($revoked) {
      $this->activityLogService->log(
        action: 'session_revoke',
        user: $request->user(),
        description: "Sesi {$sessionId} dicabut oleh admin",
        request: $request,
      );
    }

    return back()->with($revoked ? 'success' : 'error', $revoked
      ? 'Sesi berhasil dicabut.'
      : 'Sesi tidak ditemukan.');
  }

  public function revokeUser(User $user)
  {
    $count = $this->sessionService->revokeAllForUser($user->id);

    $this->activityLogService->log(
      action: 'session_revoke_all',
      user: auth()->user(),
      subject: $user,
      description: "Semua sesi user {$user->username} dicabut ({$count} sesi)",
    );

    return back()->with('success', "{$count} sesi user {$user->name} berhasil dicabut.");
  }

  public function revokeOthers(Request $request)
  {
    $count = $this->sessionService->revokeAllExceptCurrent($request->user()->id);

    return back()->with('success', "{$count} sesi lain berhasil dicabut.");
  }
}
