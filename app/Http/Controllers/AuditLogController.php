<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
  public function index(Request $request)
  {
    $logs = ActivityLog::query()
      ->with('user:id,name,username,role')
      ->when($request->action, fn ($q, $v) => $q->where('action', $v))
      ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
      ->when($request->date_from, fn ($q, $v) => $q->whereDate('logged_at', '>=', $v))
      ->when($request->date_to, fn ($q, $v) => $q->whereDate('logged_at', '<=', $v))
      ->orderByDesc('logged_at')
      ->paginate(30)
      ->withQueryString();

    $actions = \Illuminate\Support\Facades\Cache::remember('audit_log_actions', 1800, function () {
        return ActivityLog::query()->distinct()->orderBy('action')->pluck('action');
    });

    return view('security.audit-logs', compact('logs', 'actions'));
  }
}
