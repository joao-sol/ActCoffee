<?php

namespace App\Services;

use App\Models\CoffeeDuty;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
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
            ? $this->nextIndexAfterEmployee($activeQueue, $lastDuty->employee_id)
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
            return $existing->load(['employee', 'originalEmployee']);
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

    public function swapDuty(CoffeeDuty $duty): CoffeeDuty
    {
        if ($duty->status === CoffeeDuty::STATUS_COMPLETED) {
            throw new RuntimeException('Lavagem ja concluida. A troca nao pode mais alterar este dia.');
        }

        $date = $duty->duty_date->copy()->startOfDay();
        $activeQueue = $this->queue->getActiveQueue()->values();

        if ($activeQueue->count() < 2) {
            throw new RuntimeException('E preciso ter ao menos dois funcionarios ativos para trocar a escala.');
        }

        $currentIndex = $activeQueue->search(fn (Employee $employee): bool => $employee->id === $duty->employee_id);
        $queueIndex = $currentIndex === false ? 0 : ($currentIndex + 1) % $activeQueue->count();
        $replacement = null;

        for ($attempts = 0; $attempts < $activeQueue->count(); $attempts++) {
            $employee = $activeQueue[$queueIndex];
            $queueIndex = ($queueIndex + 1) % $activeQueue->count();

            if ($employee->id === $duty->employee_id) {
                continue;
            }

            if ($this->isEmployeeAvailable($employee, $date)) {
                $replacement = $employee;
                break;
            }
        }

        if ($replacement === null) {
            throw new RuntimeException('Nenhum proximo funcionario disponivel para assumir hoje.');
        }

        $duty->update([
            'original_employee_id' => $duty->original_employee_id ?? $duty->employee_id,
            'employee_id' => $replacement->id,
            'status' => CoffeeDuty::STATUS_SCHEDULED,
            'notes' => trim(($duty->notes ? $duty->notes.PHP_EOL : '').'Troca registrada em '.now()->format('d/m/Y H:i').'.'),
        ]);

        return $duty->refresh()->load(['employee', 'originalEmployee']);
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
            $queueIndex = $this->nextIndexAfterEmployee($activeQueue, $existing->employee_id, $queueIndex);

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
