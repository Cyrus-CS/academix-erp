<?php

namespace App\Http\Requests\ReportCard;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReportCardRequest extends FormRequest
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
            'student_id'       => ['required', 'exists:students,id'],
            'term_id'          => ['required', 'exists:terms,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id'         => ['required', 'exists:classes,id'],
        ];
    }

    public function messages() : array{
        return [
            'student_id.required' => "L'étudiant est obligatoire.",
            'term_id.required'         => 'Le trimestre est obligatoire.',
            'academic_year_id.required' => "L'année académique est obligatoire",
            'class_id.required' => 'La classe est obligatoire.',
        ];
    }
}