<?php

namespace App\Http\Requests\TeacherAssignment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeacherAssignmentRequest extends FormRequest
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
            'teacher_id'       => ['required', 'exists:teachers,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'class_id'  => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function messages() : array{
        return [
            'teacher_id.required'       => "L'enseignant est obligatoire.",
            'subject_id.required'       => 'La matière est obligatoire.',
            'class_id.required'  => 'La classe est obligatoire.',
            'academic_year_id.required' => "L'année académique est obligatoire.",
        ];
    }
}