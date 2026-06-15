<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomHolidayRequest;
use App\Models\CustomHoliday;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomHolidayController extends Controller
{
    public function index(): View
    {
        return view('admin.custom-holidays.index', [
            'holidays' => CustomHoliday::query()
                ->orderByDesc('date')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.custom-holidays.create', [
            'customHoliday' => new CustomHoliday(),
        ]);
    }

    public function store(CustomHolidayRequest $request): RedirectResponse
    {
        CustomHoliday::create($request->validated());

        return redirect()
            ->route('admin.feriados-personalizados.index')
            ->with('success', 'Feriado personalizado cadastrado.');
    }

    public function edit(CustomHoliday $custom_holiday): View
    {
        return view('admin.custom-holidays.edit', [
            'customHoliday' => $custom_holiday,
        ]);
    }

    public function update(CustomHolidayRequest $request, CustomHoliday $custom_holiday): RedirectResponse
    {
        $custom_holiday->update($request->validated());

        return redirect()
            ->route('admin.feriados-personalizados.index')
            ->with('success', 'Feriado personalizado atualizado.');
    }

    public function destroy(CustomHoliday $custom_holiday): RedirectResponse
    {
        $custom_holiday->delete();

        return back()->with('success', 'Feriado personalizado removido.');
    }
}
