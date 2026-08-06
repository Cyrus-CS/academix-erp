<?php

namespace App\Http\Requests\Grade;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeRequest extends FormRequest
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
            'student_id'      => ['required', 'exists:students,id'],
            'subject_id'      => ['required', 'exists:subjects,id'],
            'term_id'         => ['required', 'exists:terms,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'type'   => ['required', Rule::in(['homework', 'test', 'exam'])],
            'score' => ['required', 'numeric', 'min:0', 'max:20'],
            'max_score'       => ['required', 'numeric', 'min:1', 'max:20'],
            'comment'         => ['nullable', 'string', 'max:500'],
            'graded_at'       => ['required', 'date'],
        ];
    }

    public function messages() : array{
        return [
            'student_id.required'      => "L'étudiant est obligatoire.",
            'subject_id.required'      => 'La matière est obligatoire.',
            'term_id.required'         => 'Le trimestre est obligatoire.',
            'class_id.required' => 'La classe est obligatoire.',
            'type.required'            => 'Le type de note est obligatoire.',
            'score.required'           => 'La note est obligatoire.',
            'score.min'                => 'La note minimum est 0.',
            'score.max'                => 'La note maximum est 20.',
            'graded_at.required'       => 'La date d\'évaluation est obligatoire.',
        ];
    }
}