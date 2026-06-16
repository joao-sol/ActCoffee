<?php

namespace Tests\Feature;

use App\Models\CoffeeDuty;
use App\Models\CustomHoliday;
use App\Models\Employee;
use App\Models\Vacation;
use App\Services\EmployeeQueueService;
use App\Services\HistoryCleanupService;
use App\Services\HolidayService;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_skips_saturday_and_sunday(): void
    {
        $this->employee('Ana', 1);
        $this->employee('Bruno', 2);

        $entries = $this->schedule()->generate(Carbon::parse('2026-06-12'), Carbon::parse('2026-06-15'));

        $this->assertSame(['2026-06-12', '2026-06-15'], $entries->pluck('date')->map->toDateString()->all());
        $this->assertSame(['Ana', 'Bruno'], $entries->pluck('employee.name')->all());
    }

    public function test_it_skips_fixed_national_holiday_without_consuming_turn(): void
    {
        $this->employee('Ana', 1);
        $this->employee('Bruno', 2);

        $entries = $this->schedule()->generate(Carbon::parse('2026-01-01'), Carbon::parse('2026-01-02'));

        $this->assertCount(1, $entries);
        $this->assertSame('2026-01-02', $entries->first()['date']->toDateString());
        $this->assertSame('Ana', $entries->first()['employee']->name);
    }

    public function test_it_skips_guarapuava_local_holiday_on_december_ninth(): void
    {
        $this->employee('Ana', 1);
        $this->employee('Bruno', 2);

        $entries = $this->schedule()->generate(Carbon::parse('2026-12-08'), Carbon::parse('2026-12-10'));

        $this->assertSame(['2026-12-08', '2026-12-10'], $entries->pluck('date')->map->toDateString()->all());
        $this->assertSame(['Ana', 'Bruno'], $entries->pluck('employee.name')->all());
    }

    public function test_it_calculates_easter_using_meeus_algorithm(): void
    {
        $this->assertSame('2026-04-05', app(HolidayService::class)->calculateEaster(2026)->toDateString());
    }

    public function test_it_skips_movable_holiday_based_on_easter(): void
    {
        $this->employee('Ana', 1);
        $this->employee('Bruno', 2);

        $entries = $this->schedule()->generate(Carbon::parse('2026-04-02'), Carbon::parse('2026-04-06'));

        $this->assertSame(['2026-04-02', '2026-04-06'], $entries->pluck('date')->map->toDateString()->all());
        $this->assertSame(['Ana', 'Bruno'], $entries->pluck('employee.name')->all());
    }

    public function test_it_skips_custom_holiday_without_consuming_turn(): void
    {
        $this->employee('Ana', 1);
        $this->employee('Bruno', 2);
        CustomHoliday::create([
            'name' => 'Recesso interno',
            'date' => '2026-06-15',
        ]);

        $entries = $this->schedule()->generate(Carbon::parse('2026-06-12'), Carbon::parse('2026-06-16'));

        $this->assertSame(['2026-06-12', '2026-06-16'], $entries->pluck('date')->map->toDateString()->all());
        $this->assertSame(['Ana', 'Bruno'], $entries->pluck('employee.name')->all());
    }

    public function test_it_ignores_inactive_employees(): void
    {
        $this->employee('Ana', 1, false);
        $this->employee('Bruno', 2);

        $entry = $this->schedule()->generate(Carbon::parse('2026-06-12'), Carbon::parse('2026-06-12'))->first();

        $this->assertSame('Bruno', $entry['employee']->name);
    }

    public function test_it_adds_new_employee_to_end_of_queue(): void
    {
        $this->employee('Ana', 1);

        $employee = new Employee(['name' => 'Bruno']);
        app(EmployeeQueueService::class)->addEmployeeToEnd($employee);

        $this->assertSame(2, $employee->queue_position);
        $this->assertTrue($employee->active);
    }

    public function test_it_skips_vacation_only_when_turn_falls_inside_period_and_keeps_order(): void
    {
        $this->employee('Ana', 1);
        $maria = $this->employee('Maria', 2);
        $this->employee('Joao', 3);
        $this->employee('Carlos', 4);

        Vacation::create([
            'employee_id' => $maria->id,
            'start_date' => '2026-06-09',
            'end_date' => '2026-06-11',
        ]);

        $entries = $this->schedule()->generate(Carbon::parse('2026-06-08'), Carbon::parse('2026-06-12'));

        $this->assertSame(['Ana', 'Joao', 'Carlos', 'Ana', 'Maria'], $entries->pluck('employee.name')->all());
    }

    public function test_it_swaps_today_with_next_available_employee_and_can_complete(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        $ana = $this->employee('Ana', 1);
        $this->employee('Bruno', 2);

        $schedule = $this->schedule();
        $duty = $schedule->ensureDutyForDate(Carbon::parse('2026-06-10'));

        $this->assertSame($ana->id, $duty->employee_id);

        $swapped = $schedule->swapDuty($duty);
        $this->assertSame('Bruno', $swapped->employee->name);
        $this->assertSame($ana->id, $swapped->original_employee_id);

        $completed = $schedule->completeDuty($swapped);
        $this->assertSame(CoffeeDuty::STATUS_COMPLETED, $completed->status);

        Carbon::setTestNow();
    }

    public function test_swap_candidates_keep_queue_order_after_current_employee(): void
    {
        $this->employee('Ana', 1);
        $bruno = $this->employee('Bruno', 2);
        $carlos = $this->employee('Carlos', 3);

        $duty = CoffeeDuty::create([
            'employee_id' => $bruno->id,
            'duty_date' => '2026-06-10',
        ]);

        $schedule = $this->schedule();
        $candidates = $schedule->getSwapCandidates($duty);

        $this->assertSame(['Carlos', 'Ana'], $candidates->pluck('name')->all());

        $swapped = $schedule->swapDuty($duty);

        $this->assertSame($carlos->id, $swapped->employee_id);
        $this->assertSame($bruno->id, $swapped->original_employee_id);
    }

    public function test_it_swaps_today_with_selected_available_employee(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        $ana = $this->employee('Ana', 1);
        $this->employee('Bruno', 2);
        $carlos = $this->employee('Carlos', 3);
        $this->employee('Dora', 4);

        $schedule = $this->schedule();
        $duty = $schedule->ensureDutyForDate(Carbon::parse('2026-06-10'));
        $candidates = $schedule->getSwapCandidates($duty);

        $this->assertSame(['Bruno', 'Carlos', 'Dora'], $candidates->pluck('name')->all());

        $swapped = $schedule->swapDutyWith($duty, $carlos);

        $this->assertSame('Carlos', $swapped->employee->name);
        $this->assertSame($ana->id, $swapped->original_employee_id);

        $counterpart = CoffeeDuty::whereDate('duty_date', '2026-06-12')->firstOrFail();
        $this->assertSame($ana->id, $counterpart->employee_id);
        $this->assertSame($carlos->id, $counterpart->original_employee_id);

        $entries = $schedule->generate(Carbon::parse('2026-06-10'), Carbon::parse('2026-06-16'));

        $this->assertSame(['Carlos', 'Bruno', 'Ana', 'Dora', 'Ana'], $entries->pluck('employee.name')->all());
        $this->assertSame(['Ana', null, 'Carlos', null, null], $entries->pluck('original_employee.name')->all());

        Carbon::setTestNow();
    }

    public function test_it_repairs_existing_swap_without_counterpart(): void
    {
        $ana = $this->employee('Ana', 1);
        $this->employee('Bruno', 2);
        $carlos = $this->employee('Carlos', 3);
        $this->employee('Dora', 4);

        CoffeeDuty::create([
            'employee_id' => $carlos->id,
            'original_employee_id' => $ana->id,
            'duty_date' => '2026-06-10',
        ]);

        $schedule = $this->schedule();
        $schedule->ensureDutyForDate(Carbon::parse('2026-06-10'));

        $counterpart = CoffeeDuty::whereDate('duty_date', '2026-06-12')->firstOrFail();
        $this->assertSame($ana->id, $counterpart->employee_id);
        $this->assertSame($carlos->id, $counterpart->original_employee_id);

        $entries = $schedule->generate(Carbon::parse('2026-06-10'), Carbon::parse('2026-06-16'));

        $this->assertSame(['Carlos', 'Bruno', 'Ana', 'Dora', 'Ana'], $entries->pluck('employee.name')->all());
    }

    public function test_it_rejects_selected_employee_on_vacation_for_swap(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        $this->employee('Ana', 1);
        $bruno = $this->employee('Bruno', 2);

        Vacation::create([
            'employee_id' => $bruno->id,
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-10',
        ]);

        $schedule = $this->schedule();
        $duty = $schedule->ensureDutyForDate(Carbon::parse('2026-06-10'));

        try {
            $schedule->swapDutyWith($duty, $bruno);
            $this->fail('A troca deveria rejeitar funcionário em férias.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('férias', $exception->getMessage());
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_it_rejects_inactive_selected_employee_for_swap(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        $this->employee('Ana', 1);
        $bruno = $this->employee('Bruno', 2, false);

        $schedule = $this->schedule();
        $duty = $schedule->ensureDutyForDate(Carbon::parse('2026-06-10'));

        try {
            $schedule->swapDutyWith($duty, $bruno);
            $this->fail('A troca deveria rejeitar funcionário inativo.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('não participa', $exception->getMessage());
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_it_removes_history_older_than_thirty_days(): void
    {
        $employee = $this->employee('Ana', 1);

        CoffeeDuty::create([
            'employee_id' => $employee->id,
            'duty_date' => '2026-05-29',
        ]);
        CoffeeDuty::create([
            'employee_id' => $employee->id,
            'duty_date' => '2026-05-31',
        ]);

        app(HistoryCleanupService::class)->removeOlderThanThirtyDays(Carbon::parse('2026-06-30'));

        $this->assertDatabaseMissing('coffee_duties', ['duty_date' => '2026-05-29']);
        $this->assertTrue(CoffeeDuty::whereDate('duty_date', '2026-05-31')->exists());
    }

    private function employee(string $name, int $position, bool $active = true): Employee
    {
        return Employee::create([
            'name' => $name,
            'queue_position' => $position,
            'active' => $active,
            'dismissed_at' => $active ? null : now()->toDateString(),
        ]);
    }

    private function schedule(): ScheduleGeneratorService
    {
        return app(ScheduleGeneratorService::class);
    }
}
