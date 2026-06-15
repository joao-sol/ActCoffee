<?php

namespace App\Http\Requests;

use App\Models\CustomHoliday;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $holiday = $this->route('custom_holiday');
        $holidayId = $holiday instanceof CustomHoliday ? $holiday->id : null;

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'date' => [
                'required',
                'date',
                Rule::unique('custom_holidays', 'date')->ignore($holidayId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
