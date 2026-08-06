<?php

namespace App\Http\Requests\Schedule;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TimesTableRequest extends FormRequest
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
             'class_id'         => ['required', 'exists:classes,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'day_of_week'      => ['required', Rule::in(array_keys(Schedule::DAYS))],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'room'             => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages() : array{
        return [
            'class_id.required'    => 'La classe est obligatoire.',
            'subject_id.required'  => 'La matière est obligatoire.',
            'teacher_id.required'  => "L'enseignant est obligatoire.",
            'day_of_week.required' => 'Le jour est obligatoire.',
            'start_time.required'  => "L'heure de début est obligatoire.",
            'end_time.required'    => "L'heure de fin est obligatoire.",
            'end_time.after'       => "L'heure de fin doit être après l'heure de début.",
        ];
    }
}