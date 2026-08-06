<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_id'  => ['required', 'exists:classes,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
            'date'  => ['required', 'date', 'before_or_equal:today'],
            'attendances'  => ['required', 'array'],
            'attendances.*.student_id'  => ['required', 'exists:students,id'],
            'attendances.*.status'      => ['required', 'in:present,absent,late'],
            'attendances.*.note'        => ['nullable', 'string', 'max:200'],
        ];
    }
    public function messages() : array{
        return [
            'class_id.required'    => 'La classe est obligatoire.',
            'schedule_id.required' => 'Le créneau est obligatoire.',
            'date.required'        => 'La date est obligatoire.',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'attendances.required' => 'Aucun étudiant sélectionné.',
        ];
    }
}