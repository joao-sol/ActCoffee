<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

class EmployeeQueueService
{
    public function getActiveQueue(): Collection
    {
        return Employee::query()
            ->active()
            ->orderBy('queue_position')
            ->orderBy('name')
            ->get();
    }

    public function getNextQueuePosition(): int
    {
        return ((int) Employee::query()->max('queue_position')) + 1;
    }

    public function addEmployeeToEnd(Employee $employee): Employee
    {
        $employee->queue_position = $this->getNextQueuePosition();
        $employee->active = true;
        $employee->save();

        return $employee;
    }
}
