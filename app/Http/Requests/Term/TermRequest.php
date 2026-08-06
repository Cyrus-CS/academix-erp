<?php

namespace App\Http\Requests\Term;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
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
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name'             => ['required', 'string', 'max:100'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
            'is_current'       => ['boolean'],
        ];
    }

    public function messages() : array{
        return [
            'academic_year_id.required' => "L'année académique est obligatoire.",
            'academic_year_id.exists'   => "L'année académique sélectionnée est invalide.",
            'name.required'             => 'Le nom du trimestre est obligatoire.',
            'start_date.required'       => 'La date de début est obligatoire.',
            'end_date.required'         => 'La date de fin est obligatoire.',
            'end_date.after'            => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}