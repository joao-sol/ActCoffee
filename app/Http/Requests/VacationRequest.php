<?php

namespace App\Http\Requests;

use App\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where('active', true),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $vacation = $this->route('vacation');
            $vacationId = $vacation instanceof Vacation ? $vacation->id : null;
            $start = Carbon::parse($this->input('start_date'))->toDateString();
            $end = Carbon::parse($this->input('end_date'))->toDateString();

            $conflict = Vacation::query()
                ->where('employee_id', $this->integer('employee_id'))
                ->when($vacationId, fn ($query) => $query->whereKeyNot($vacationId))
                ->whereDate('start_date', '<=', $end)
                ->whereDate('end_date', '>=', $start)
                ->exists();

            if ($conflict) {
                $validator->errors()->add('start_date', 'Este funcionario ja possui ferias cadastradas nesse periodo.');
            }
        });
    }
}
