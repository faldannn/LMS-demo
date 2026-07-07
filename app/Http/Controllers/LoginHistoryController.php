<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
  public function index(Request $request)
  {
    $logs = LoginLog::query()
      ->with('user:id,name,username')
      ->when($request->status, fn ($q, $v) => $q->where('status', $v))
      ->when($request->username, fn ($q, $v) => $q->where('username', 'like', "%{$v}%"))
      ->when($request->date_from, fn ($q, $v) => $q->whereDate('logged_at', '>=', $v))
      ->when($request->date_to, fn ($q, $v) => $q->whereDate('logged_at', '<=', $v))
      ->orderByDesc('logged_at')
      ->paginate(30)
      ->withQueryString();

    $stats = \Illuminate\Support\Facades\Cache::remember('login_history_stats_' . today()->toDateString(), 120, function () {
        return [
          'success' => LoginLog::successful()->whereDate('logged_at', today())->count(),
          'failed' => LoginLog::failed()->whereDate('logged_at', today())->count(),
        ];
    });

    return view('security.login-history', compact('logs', 'stats'));
  }
}
