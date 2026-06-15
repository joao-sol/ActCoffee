<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeQueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('admin.employees.index', [
            'employees' => Employee::query()
                ->orderBy('queue_position')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.employees.create', [
            'employee' => new Employee(),
        ]);
    }

    public function store(EmployeeRequest $request, EmployeeQueueService $queue): RedirectResponse
    {
        $data = $request->validated();

        $employee = new Employee([
            'name' => $data['name'],
            'hired_at' => $data['hired_at'] ?? null,
        ]);

        $queue->addEmployeeToEnd($employee);

        return redirect()
            ->route('admin.funcionarios.index')
            ->with('success', 'Funcionario cadastrado no final da fila.');
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', [
            'employee' => $employee,
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if (! $data['active'] && empty($data['dismissed_at'])) {
            $data['dismissed_at'] = now()->toDateString();
        }

        if ($data['active']) {
            $data['dismissed_at'] = null;
        }

        $employee->update($data);

        return redirect()
            ->route('admin.funcionarios.index')
            ->with('success', 'Funcionario atualizado.');
    }

    public function deactivate(Employee $employee): RedirectResponse
    {
        $employee->update([
            'active' => false,
            'dismissed_at' => now()->toDateString(),
        ]);

        return back()->with('success', 'Funcionario inativado para as proximas escalas.');
    }
}
