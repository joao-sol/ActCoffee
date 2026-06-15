<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class AdminScheduleController extends Controller
{
    public function index(ScheduleGeneratorService $schedule): View
    {
        $today = Carbon::today();
        $schedule->ensureDutyForDate($today);

        return view('admin.schedule.index', [
            'today' => $today,
            'todayStatus' => $schedule->getDayStatus($today),
            'days' => $schedule->generate($today, $today->copy()->addDays(90), 30),
        ]);
    }

    public function complete(string $date, ScheduleGeneratorService $schedule): RedirectResponse
    {
        $duty = $schedule->ensureDutyForDate(Carbon::parse($date));

        if ($duty === null) {
            return back()->with('error', 'Nao existe responsavel para essa data.');
        }

        $schedule->completeDuty($duty);

        return back()->with('success', 'Lavagem marcada como concluida.');
    }

    public function swap(string $date, ScheduleGeneratorService $schedule): RedirectResponse
    {
        $duty = $schedule->ensureDutyForDate(Carbon::parse($date));

        if ($duty === null) {
            return back()->with('error', 'Nao existe responsavel para essa data.');
        }

        try {
            $schedule->swapDuty($duty);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Responsavel trocado com o proximo disponivel da fila.');
    }
}
