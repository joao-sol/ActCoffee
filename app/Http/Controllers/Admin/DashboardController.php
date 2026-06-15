<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Vacation;
use App\Services\HolidayService;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ScheduleGeneratorService $schedule, HolidayService $holidays): View
    {
        $today = Carbon::today();
        $schedule->ensureDutyForDate($today);

        $nextHolidays = $holidays->getHolidaysForYear((int) $today->format('Y'))
            ->merge($holidays->getHolidaysForYear((int) $today->copy()->addYear()->format('Y')))
            ->filter(fn (array $holiday): bool => $holiday['date']->gte($today))
            ->take(5)
            ->values();

        return view('admin.dashboard', [
            'activeEmployees' => Employee::active()->count(),
            'inactiveEmployees' => Employee::query()->where('active', false)->count(),
            'todayStatus' => $schedule->getDayStatus($today),
            'upcoming' => $schedule->generate($today->copy()->addDay(), $today->copy()->addDays(60), 5),
            'nextHolidays' => $nextHolidays,
            'currentVacations' => Vacation::with('employee')
                ->whereDate('start_date', '<=', $today->toDateString())
                ->whereDate('end_date', '>=', $today->toDateString())
                ->get(),
        ]);
    }
}
