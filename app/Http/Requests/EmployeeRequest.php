<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $employeeId = $employee instanceof Employee ? $employee->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('employees', 'name')
                    ->where(fn ($query) => $query->where('active', true))
                    ->ignore($employeeId),
            ],
            'active' => ['sometimes', 'boolean'],
            'hired_at' => ['nullable', 'date'],
            'dismissed_at' => ['nullable', 'date', 'after_or_equal:hired_at'],
        ];
    }
}
