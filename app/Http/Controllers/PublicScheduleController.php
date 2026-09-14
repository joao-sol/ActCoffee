<?php

namespace App\Http\Controllers;

use App\Models\CoffeeDuty;
use App\Models\Employee;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PublicScheduleController extends Controller
{
    public function index(ScheduleGeneratorService $schedule): View
    {
        $today = Carbon::today();
        $schedule->ensureDutyForDate($today);
        $todayStatus = $schedule->getDayStatus($today);
        $history = $this->recentHistory();
        $historySwapCandidates = $history
            ->take(2)
            ->filter(fn (CoffeeDuty $duty): bool => $schedule->canSwapDuty($duty, $today))
            ->mapWithKeys(fn (CoffeeDuty $duty): array => [
                $duty->id => $schedule->getSwapCandidates($duty),
            ]);

        return view('public.index', [
            'today' => $today,
            'todayStatus' => $todayStatus,
            'upcoming' => $schedule->generate($today->copy()->addDay(), $today->copy()->addDays(60), 8),
            'history' => $history,
            'historySwapCandidates' => $historySwapCandidates,
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

    public function swap(Request $request, string $date, ScheduleGeneratorService $schedule): RedirectResponse
    {
        $swapDate = Carbon::parse($date)->startOfDay();

        if ($swapDate->isFuture()) {
            return back()->with('error', 'Não é possível registrar uma troca antes da data da lavagem.');
        }

        $validated = $request->validate([
            'replacement_employee_ids' => ['required', 'array', 'size:1'],
            'replacement_employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
        ], [
            'replacement_employee_ids.required' => 'Selecione uma pessoa para assumir a cafeteira nesta data.',
            'replacement_employee_ids.size' => 'Selecione apenas uma pessoa para a troca.',
        ]);

        $duty = $swapDate->isToday()
            ? $schedule->ensureDutyForDate($swapDate)
            : CoffeeDuty::query()->whereDate('duty_date', $swapDate->toDateString())->first();

        if ($duty === null) {
            return back()->with('error', 'Não existe responsável para essa data.');
        }

        if (! $schedule->canSwapDuty($duty)) {
            return back()->with('error', 'O prazo de três dias úteis para alterar esta lavagem já terminou.');
        }

        $replacement = Employee::findOrFail((int) $validated['replacement_employee_ids'][0]);

        try {
            $schedule->swapDutyWith($duty, $replacement);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Responsável trocado com a pessoa selecionada.');
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
