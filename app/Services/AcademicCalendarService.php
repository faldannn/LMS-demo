<?php

namespace App\Services;

use App\Models\AcademicCalendarEvent;
use App\Models\User;
use Carbon\Carbon;

class AcademicCalendarService
{
    public function listForUser(User $user, ?int $month = null, ?int $year = null)
    {
        $month ??= now()->month;
        $year ??= now()->year;
        $start = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = AcademicCalendarEvent::with(['creator:id,name', 'class:id,name'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                    });
            });

        if ($user->isSiswa()) {
            $classId = $user->student?->class_id;
            $query->where(function ($q) use ($classId) {
                $q->where('is_public', true);
                if ($classId) {
                    $q->orWhere('class_id', $classId);
                }
            });
        }

        return $query->orderBy('start_date')->get();
    }

    public function upcoming(int $limit = 5)
    {
        return AcademicCalendarEvent::where('start_date', '>=', today())
            ->where('is_public', true)
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }
}
