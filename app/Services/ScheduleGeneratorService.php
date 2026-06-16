<?php

namespace App\Services;

use App\Models\CoffeeDuty;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ScheduleGeneratorService
{
    public function __construct(
        private readonly HolidayService $holidays,
        private readonly EmployeeQueueService $queue,
        private readonly HistoryCleanupService $historyCleanup,
    ) {
    }

    public function generate(CarbonInterface $startDate, CarbonInterface $endDate, ?int $limit = null): Collection
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();
        $activeQueue = $this->queue->getActiveQueue()->values();

        if ($activeQueue->isEmpty() || $end->lt($start)) {
            return collect();
        }

        $lastDuty = CoffeeDuty::query()
            ->whereDate('duty_date', '<', $start->toDateString())
            ->orderByDesc('duty_date')
            ->first();

        $cursor = $lastDuty
            ? $lastDuty->duty_date->copy()->addDay()->startOfDay()
            : $start->copy();

        $queueIndex = $lastDuty
            ? $this->nextIndexAfterEmployee($activeQueue, $this->queueEmployeeIdForDuty($lastDuty))
            : 0;

        $entries = collect();

        while ($cursor->lte($end)) {
            if ($this->isBusinessDay($cursor)) {
                $entry = $this->entryForBusinessDay($cursor, $activeQueue, $queueIndex);

                if ($entry !== null && $cursor->gte($start)) {
                    $entries->push($entry);

                    if ($limit !== null && $entries->count() >= $limit) {
                        break;
                    }
                }
            }

            $cursor->addDay();
        }

        return $entries;
    }

    public function getDayStatus(CarbonInterface $date): array
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->isWeekend()) {
            return [
                'type' => 'weekend',
                'date' => $date,
                'label' => 'Fim de semana',
            ];
        }

        $holidayName = $this->holidays->getHolidayName($date);

        if ($holidayName !== null) {
            return [
                'type' => 'holiday',
                'date' => $date,
                'label' => $holidayName,
            ];
        }

        $duty = CoffeeDuty::with(['employee', 'originalEmployee'])
            ->whereDate('duty_date', $date->toDateString())
            ->first();

        if ($duty !== null) {
            return $this->entryFromDuty($date, $duty);
        }

        return $this->generate($date, $date)->first() ?? [
            'type' => 'empty',
            'date' => $date,
            'label' => 'Nenhum funcionario disponivel',
        ];
    }

    public function ensureDutyForDate(CarbonInterface $date): ?CoffeeDuty
    {
        $date = Carbon::parse($date)->startOfDay();

        if (! $this->isBusinessDay($date)) {
            return null;
        }

        $existing = CoffeeDuty::query()
            ->whereDate('duty_date', $date->toDateString())
            ->first();

        if ($existing !== null) {
            $existing = $existing->load(['employee', 'originalEmployee']);
            $this->ensureSwapCounterpart($existing);

            return $existing->refresh()->load(['employee', 'originalEmployee']);
        }

        $entry = $this->generate($date, $date)->first();

        if (($entry['employee'] ?? null) === null) {
            return null;
        }

        $duty = CoffeeDuty::create([
            'employee_id' => $entry['employee']->id,
            'duty_date' => $date->toDateString(),
            'status' => CoffeeDuty::STATUS_SCHEDULED,
        ]);

        $this->historyCleanup->removeOlderThanThirtyDays($date);

        return $duty->load(['employee', 'originalEmployee']);
    }

    public function completeDuty(CoffeeDuty $duty): CoffeeDuty
    {
        $duty->update([
            'status' => CoffeeDuty::STATUS_COMPLETED,
        ]);

        return $duty->refresh()->load(['employee', 'originalEmployee']);
    }

    public function getSwapCandidates(CoffeeDuty $duty): Collection
    {
        $date = $duty->duty_date->copy()->startOfDay();
        $activeQueue = $this->queue->getActiveQueue()->values();
        $originalEmployeeId = $this->queueEmployeeIdForDuty($duty);
        $currentIndex = $activeQueue->search(fn (Employee $employee): bool => $employee->id === $originalEmployeeId);

        $orderedCandidates = $currentIndex === false
            ? $activeQueue
            : $activeQueue->slice($currentIndex + 1)->concat($activeQueue->slice(0, $currentIndex))->values();

        return $orderedCandidates
            ->filter(fn (Employee $employee): bool => $employee->id !== $duty->employee_id)
            ->filter(fn (Employee $employee): bool => $employee->id !== $originalEmployeeId)
            ->filter(fn (Employee $employee): bool => $this->isEmployeeAvailable($employee, $date))
            ->values();
    }

    public function swapDuty(CoffeeDuty $duty): CoffeeDuty
    {
        $replacement = $this->getSwapCandidates($duty)->first();

        if ($replacement === null) {
            throw new RuntimeException('Nenhum funcionário disponível para assumir hoje.');
        }

        return $this->swapDutyWith($duty, $replacement);
    }

    public function swapDutyWith(CoffeeDuty $duty, Employee $replacement): CoffeeDuty
    {
        if ($duty->status === CoffeeDuty::STATUS_COMPLETED) {
            throw new RuntimeException('Lavagem já concluída. A troca não pode mais alterar este dia.');
        }

        $date = $duty->duty_date->copy()->startOfDay();
        $originalEmployee = Employee::find($this->queueEmployeeIdForDuty($duty));

        if ($originalEmployee === null) {
            throw new RuntimeException('Não existe responsável original para trocar esta data.');
        }

        if ($replacement->id === $duty->employee_id || $replacement->id === $originalEmployee->id) {
            throw new RuntimeException('Selecione uma pessoa diferente da responsável atual.');
        }

        if (! $replacement->active) {
            throw new RuntimeException('O funcionário selecionado não participa das próximas escalas.');
        }

        if ($replacement->isOnVacation($date)) {
            throw new RuntimeException('O funcionário selecionado está em férias nesta data.');
        }

        return DB::transaction(function () use ($duty, $replacement, $originalEmployee, $date): CoffeeDuty {
            $previousReplacementId = $duty->employee_id;

            if ($previousReplacementId !== null && $previousReplacementId !== $originalEmployee->id) {
                $this->removePendingCounterpartSwap($date, $originalEmployee, $previousReplacementId);
            }

            $targetEntry = $this->findNextNaturalDutyForEmployee($replacement, $date);

            if ($targetEntry === null) {
                throw new RuntimeException('Não foi possível encontrar a próxima data desse funcionário para concluir a troca.');
            }

            $targetDate = $targetEntry['date']->copy()->startOfDay();
            $targetDuty = $targetEntry['duty'];

            if ($targetDuty instanceof CoffeeDuty && $targetDuty->employee_id !== $replacement->id) {
                throw new RuntimeException('A próxima data desse funcionário já possui uma troca registrada.');
            }

            $this->recordCounterpartSwap($targetDate, $originalEmployee, $replacement, $targetDuty);

            $duty->update([
                'original_employee_id' => $originalEmployee->id,
                'employee_id' => $replacement->id,
                'status' => CoffeeDuty::STATUS_SCHEDULED,
                'notes' => trim(($duty->notes ? $duty->notes.PHP_EOL : '').'Troca registrada em '.now()->format('d/m/Y H:i').' com '.$replacement->name.'. '.$originalEmployee->name.' assumiu o dia de '.$replacement->name.' em '.$targetDate->format('d/m/Y').'.'),
            ]);

            return $duty->refresh()->load(['employee', 'originalEmployee']);
        });
    }

    public function ensureSwapCounterpart(CoffeeDuty $duty): void
    {
        if ($duty->employee_id === null || $duty->original_employee_id === null || $duty->employee_id === $duty->original_employee_id) {
            return;
        }

        if ($this->hasPreviousMirrorSwap($duty) || $this->hasFutureMirrorSwap($duty)) {
            return;
        }

        $originalEmployee = $duty->originalEmployee ?? Employee::find($duty->original_employee_id);
        $replacement = $duty->employee ?? Employee::find($duty->employee_id);

        if ($originalEmployee === null || $replacement === null || ! $replacement->active) {
            return;
        }

        $targetEntry = $this->findNextNaturalDutyForEmployee($replacement, $duty->duty_date);

        if ($targetEntry === null) {
            return;
        }

        $targetDuty = $targetEntry['duty'];

        if ($targetDuty instanceof CoffeeDuty && ($targetDuty->status === CoffeeDuty::STATUS_COMPLETED || $targetDuty->employee_id !== $replacement->id)) {
            return;
        }

        DB::transaction(function () use ($targetEntry, $originalEmployee, $replacement, $targetDuty): void {
            $this->recordCounterpartSwap($targetEntry['date']->copy()->startOfDay(), $originalEmployee, $replacement, $targetDuty);
        });
    }

    public function isBusinessDay(CarbonInterface $date): bool
    {
        return ! $date->isWeekend() && ! $this->holidays->isHoliday($date);
    }

    public function isEmployeeAvailable(Employee $employee, CarbonInterface $date): bool
    {
        return $employee->active && ! $employee->isOnVacation($date);
    }

    private function entryForBusinessDay(Carbon $date, Collection $activeQueue, int &$queueIndex): ?array
    {
        $existing = CoffeeDuty::with(['employee', 'originalEmployee'])
            ->whereDate('duty_date', $date->toDateString())
            ->first();

        if ($existing !== null) {
            $queueIndex = $this->nextIndexAfterEmployee($activeQueue, $this->queueEmployeeIdForDuty($existing), $queueIndex);

            return $this->entryFromDuty($date, $existing);
        }

        $employee = $this->getNextAvailableEmployee($date, $activeQueue, $queueIndex);

        if ($employee === null) {
            return null;
        }

        return [
            'type' => 'duty',
            'date' => $date->copy(),
            'employee' => $employee,
            'status' => CoffeeDuty::STATUS_SCHEDULED,
            'duty' => null,
            'original_employee' => null,
        ];
    }

    private function entryFromDuty(Carbon $date, CoffeeDuty $duty): array
    {
        return [
            'type' => 'duty',
            'date' => $date->copy(),
            'employee' => $duty->employee,
            'status' => $duty->status,
            'duty' => $duty,
            'original_employee' => $duty->originalEmployee,
        ];
    }

    private function queueEmployeeIdForDuty(CoffeeDuty $duty): ?int
    {
        return $duty->original_employee_id ?? $duty->employee_id;
    }

    private function findNextNaturalDutyForEmployee(Employee $employee, CarbonInterface $afterDate): ?array
    {
        $start = Carbon::parse($afterDate)->addDay()->startOfDay();
        $entries = $this->generate($start, $start->copy()->addYear());

        return $entries->first(function (array $entry) use ($employee): bool {
            $duty = $entry['duty'];
            $employeeId = $duty instanceof CoffeeDuty
                ? $this->queueEmployeeIdForDuty($duty)
                : $entry['employee']->id;

            return $employeeId === $employee->id;
        });
    }

    private function removePendingCounterpartSwap(CarbonInterface $date, Employee $originalEmployee, int $previousReplacementId): void
    {
        CoffeeDuty::query()
            ->whereDate('duty_date', '>', $date->toDateString())
            ->where('employee_id', $originalEmployee->id)
            ->where('original_employee_id', $previousReplacementId)
            ->where('status', CoffeeDuty::STATUS_SCHEDULED)
            ->delete();
    }

    private function recordCounterpartSwap(CarbonInterface $targetDate, Employee $originalEmployee, Employee $replacement, ?CoffeeDuty $targetDuty): void
    {
        if ($targetDuty instanceof CoffeeDuty && $targetDuty->status === CoffeeDuty::STATUS_COMPLETED) {
            throw new RuntimeException('A próxima data desse funcionário já foi concluída.');
        }

        $note = 'Troca registrada em '.now()->format('d/m/Y H:i').': '.$originalEmployee->name.' assumiu o dia de '.$replacement->name.'.';
        $notes = trim((($targetDuty?->notes ?? '') ? $targetDuty->notes.PHP_EOL : '').$note);

        if ($targetDuty instanceof CoffeeDuty) {
            $targetDuty->update([
                'employee_id' => $originalEmployee->id,
                'original_employee_id' => $replacement->id,
                'status' => CoffeeDuty::STATUS_SCHEDULED,
                'notes' => $notes,
            ]);

            return;
        }

        CoffeeDuty::create([
            'employee_id' => $originalEmployee->id,
            'original_employee_id' => $replacement->id,
            'duty_date' => Carbon::parse($targetDate)->toDateString(),
            'status' => CoffeeDuty::STATUS_SCHEDULED,
            'notes' => $notes,
        ]);
    }

    private function hasPreviousMirrorSwap(CoffeeDuty $duty): bool
    {
        return CoffeeDuty::query()
            ->whereDate('duty_date', '<', $duty->duty_date->toDateString())
            ->where('employee_id', $duty->original_employee_id)
            ->where('original_employee_id', $duty->employee_id)
            ->exists();
    }

    private function hasFutureMirrorSwap(CoffeeDuty $duty): bool
    {
        return CoffeeDuty::query()
            ->whereDate('duty_date', '>', $duty->duty_date->toDateString())
            ->where('employee_id', $duty->original_employee_id)
            ->where('original_employee_id', $duty->employee_id)
            ->exists();
    }

    private function getNextAvailableEmployee(CarbonInterface $date, Collection $activeQueue, int &$queueIndex): ?Employee
    {
        $total = $activeQueue->count();

        for ($attempts = 0; $attempts < $total; $attempts++) {
            $employee = $activeQueue[$queueIndex];
            $queueIndex = ($queueIndex + 1) % $total;

            if ($this->isEmployeeAvailable($employee, $date)) {
                return $employee;
            }
        }

        return null;
    }

    private function nextIndexAfterEmployee(Collection $activeQueue, ?int $employeeId, int $fallback = 0): int
    {
        $total = $activeQueue->count();

        if ($total === 0 || $employeeId === null) {
            return 0;
        }

        $index = $activeQueue->search(fn (Employee $employee): bool => $employee->id === $employeeId);

        if ($index === false) {
            return $fallback % $total;
        }

        return ($index + 1) % $total;
    }
}
