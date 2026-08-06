<?php

namespace App\Http\Requests\TeacherContract;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherContractRequest extends FormRequest
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
        $teacher_contract = $this->route('teacher_contract');
        return [
            'teacher_id'  => ['required', 'exists:teachers,id'],
            'contract_type' => ['required', 'in:permanent,temporary,part_time,internship'],
            'start_date'  => ['required', 'date'],
            'contract_number' => ['required', 'string', 'max:150', Rule::unique('teachers_contracts', 'contract_number')->ignore($teacher_contract?->teacher_id)],
            'end_date'    => ['nullable', 'date', 'after:start_date'],
            'salary'      => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,expired,terminated'],
            'description' => ['nullable', 'string', 'max:1000'],
            'contract_pdf_path'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages() : array {
        return [
            'teacher_id.required' => "L'enseignant est obligatoire.",
            'type.required'       => 'Le type de contrat est obligatoire.',
            'start_date.required' => 'La date de début est obligatoire.',
            'salary.required'     => 'Le salaire est obligatoire.',
            'end_date.after'      => 'La date de fin doit être après la date de début.',
            'contract_pdf_path.mimes'      => 'Le document doit être un fichier PDF, DOC ou DOCX.',
            'contract_pdf_path.max'        => 'Le document ne doit pas dépasser 5 Mo.',
        ];
    }
}