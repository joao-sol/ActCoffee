<?php

namespace App\Http\Controllers;

use App\Models\CoffeeDuty;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\View\View;

class PublicScheduleController extends Controller
{
    public function index(ScheduleGeneratorService $schedule): View
    {
        $today = Carbon::today();
        $schedule->ensureDutyForDate($today);

        return view('public.index', [
            'today' => $today,
            'todayStatus' => $schedule->getDayStatus($today),
            'upcoming' => $schedule->generate($today->copy()->addDay(), $today->copy()->addDays(60), 8),
            'history' => $this->recentHistory(),
        ]);
    }

    public function schedule(ScheduleGeneratorService $schedule): View
    {
        $today = Carbon::today();
        $schedule->ensureDutyForDate($today);

        return view('public.schedule', [
            'days' => $schedule->generate($today, $today->copy()->addDays(90), 30),
        ]);
    }

    public function history(): View
    {
        return view('public.history', [
            'history' => $this->recentHistory(30),
        ]);
    }

    private function recentHistory(int $limit = 10)
    {
        return CoffeeDuty::with(['employee', 'originalEmployee'])
            ->whereDate('duty_date', '>=', Carbon::today()->subDays(30)->toDateString())
            ->whereDate('duty_date', '<=', Carbon::today()->toDateString())
            ->orderByDesc('duty_date')
            ->limit($limit)
            ->get();
    }
}
