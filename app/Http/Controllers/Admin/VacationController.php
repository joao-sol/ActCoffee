<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VacationRequest;
use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VacationController extends Controller
{
    public function index(): View
    {
        return view('admin.vacations.index', [
            'vacations' => Vacation::with('employee')
                ->orderByDesc('start_date')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.vacations.create', [
            'vacation' => new Vacation(),
            'employees' => Employee::active()->orderBy('name')->get(),
        ]);
    }

    public function store(VacationRequest $request): RedirectResponse
    {
        Vacation::create($request->validated());

        return redirect()
            ->route('admin.ferias.index')
            ->with('success', 'Ferias cadastradas.');
    }

    public function edit(Vacation $vacation): View
    {
        return view('admin.vacations.edit', [
            'vacation' => $vacation,
            'employees' => Employee::active()->orderBy('name')->get(),
        ]);
    }

    public function update(VacationRequest $request, Vacation $vacation): RedirectResponse
    {
        $vacation->update($request->validated());

        return redirect()
            ->route('admin.ferias.index')
            ->with('success', 'Ferias atualizadas.');
    }

    public function destroy(Vacation $vacation): RedirectResponse
    {
        $vacation->delete();

        return back()->with('success', 'Ferias removidas.');
    }
}
