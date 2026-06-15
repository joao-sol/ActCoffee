<?php

namespace Database\Seeders;

use App\Models\CoffeeDuty;
use App\Models\CustomHoliday;
use App\Models\Employee;
use App\Models\User;
use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => 'password',
            ],
        );

        $names = ['Ana Silva', 'Bruno Costa', 'Carla Mendes', 'Diego Lima', 'Marina Souza'];

        foreach ($names as $index => $name) {
            Employee::updateOrCreate(
                ['name' => $name],
                [
                    'active' => true,
                    'queue_position' => $index + 1,
                    'hired_at' => Carbon::today()->subMonths(8 - $index)->toDateString(),
                    'dismissed_at' => null,
                ],
            );
        }

        Employee::updateOrCreate(
            ['name' => 'Rafael Antigo'],
            [
                'active' => false,
                'queue_position' => 6,
                'hired_at' => Carbon::today()->subYear()->toDateString(),
                'dismissed_at' => Carbon::today()->subMonth()->toDateString(),
            ],
        );

        $marina = Employee::where('name', 'Marina Souza')->first();

        Vacation::updateOrCreate(
            [
                'employee_id' => $marina->id,
                'start_date' => Carbon::today()->addDays(4)->toDateString(),
            ],
            [
                'end_date' => Carbon::today()->addDays(8)->toDateString(),
                'reason' => 'Ferias programadas',
            ],
        );

        CustomHoliday::updateOrCreate(
            ['date' => Carbon::today()->addDays(15)->toDateString()],
            [
                'name' => 'Recesso interno',
                'description' => 'Data de exemplo para demonstracao.',
            ],
        );

        $activeEmployees = Employee::active()->orderBy('queue_position')->get()->values();
        $cursor = Carbon::today()->subDays(12);
        $employeeIndex = 0;

        while ($cursor->lt(Carbon::today())) {
            if (! $cursor->isWeekend() && $activeEmployees->isNotEmpty()) {
                $employee = $activeEmployees[$employeeIndex % $activeEmployees->count()];

                CoffeeDuty::updateOrCreate(
                    ['duty_date' => $cursor->toDateString()],
                    [
                        'employee_id' => $employee->id,
                        'status' => CoffeeDuty::STATUS_COMPLETED,
                    ],
                );

                $employeeIndex++;
            }

            $cursor->addDay();
        }
    }
}
