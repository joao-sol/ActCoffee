<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminScheduleController extends Controller
{
    public function index(ScheduleGeneratorService $schedule): View
    {
        $today = Carbon::today();
        $todayDuty = $schedule->ensureDutyForDate($today);
        $todayStatus = $schedule->getDayStatus($today);

        return view('admin.schedule.index', [
            'today' => $today,
            'todayStatus' => $todayStatus,
            'swapCandidates' => $todayDuty !== null ? $schedule->getSwapCandidates($todayDuty) : collect(),
            'days' => $schedule->generate($today, $today->copy()->addDays(90), 30),
        ]);
    }

    public function complete(string $date, ScheduleGeneratorService $schedule): RedirectResponse
    {
        $duty = $schedule->ensureDutyForDate(Carbon::parse($date));

        if ($duty === null) {
            return back()->with('error', 'Não existe responsável para essa data.');
        }

        $schedule->completeDuty($duty);

        return back()->with('success', 'Lavagem marcada como concluída.');
    }

    public function swap(Request $request, string $date, ScheduleGeneratorService $schedule): RedirectResponse
    {
        $swapDate = Carbon::parse($date)->startOfDay();

        if (! $swapDate->isSameDay(Carbon::today())) {
            return back()->with('error', 'A troca manual está disponível apenas para o dia corrente.');
        }

        $validated = $request->validate([
            'replacement_employee_ids' => ['required', 'array', 'size:1'],
            'replacement_employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
        ], [
            'replacement_employee_ids.required' => 'Selecione uma pessoa para assumir a cafeteira hoje.',
            'replacement_employee_ids.size' => 'Selecione apenas uma pessoa para a troca.',
        ]);

        $duty = $schedule->ensureDutyForDate($swapDate);

        if ($duty === null) {
            return back()->with('error', 'Não existe responsável para essa data.');
        }

        $replacement = Employee::findOrFail((int) $validated['replacement_employee_ids'][0]);

        try {
            $schedule->swapDutyWith($duty, $replacement);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Responsável trocado com a pessoa selecionada.');
    }
}
