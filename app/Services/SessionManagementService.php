<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SessionManagementService
{
  public function activeSessions(?int $userId = null, int $limit = 50): Collection
  {
    $query = DB::table('sessions')
      ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
      ->select([
        'sessions.id',
        'sessions.user_id',
        'sessions.ip_address',
        'sessions.user_agent',
        'sessions.last_activity',
        'users.name as user_name',
        'users.username',
        'users.role',
      ])
      ->where('sessions.last_activity', '>=', now()->subMinutes((int) config('session.lifetime', 120))->timestamp)
      ->orderByDesc('sessions.last_activity');

    if ($userId) {
      $query->where('sessions.user_id', $userId);
    }

    return $query->limit($limit)->get()->map(function ($row) {
      $row->last_activity_at = date('d/m/Y H:i', $row->last_activity);
      $row->is_current = $row->id === session()->getId();

      return $row;
    });
  }

  public function revoke(string $sessionId, ?int $ownerUserId = null): bool
  {
    $query = DB::table('sessions')->where('id', $sessionId);

    if ($ownerUserId) {
      $query->where('user_id', $ownerUserId);
    }

    return (bool) $query->delete();
  }

  public function revokeAllExceptCurrent(?int $userId = null): int
  {
    $userId ??= auth()->id();
    $currentId = session()->getId();

    return DB::table('sessions')
      ->where('user_id', $userId)
      ->where('id', '!=', $currentId)
      ->delete();
  }

  public function revokeAllForUser(int $userId): int
  {
    return DB::table('sessions')->where('user_id', $userId)->delete();
  }
}
