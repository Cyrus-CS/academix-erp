<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('view subjects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $subject = $this->route('subject');
        
        return [
            'name'  => ['required', 'string', 'max:100', Rule::unique('subjects', 'name')->ignore($subject?->id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('subjects', 'code')->ignore($subject?->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'is_active'   => ['nullable','boolean'],
        ];
    }

    public function messages() : Array{
        return [
            'name.required'        => 'Le nom de la matière est obligatoire.',
            'name.unique'          => 'Une matière avec ce nom existe déjà.',
            'code.required'        => 'Le code de la matière est obligatoire.',
            'code.unique'          => 'Ce code est déjà utilisé.',
            'coefficient.required' => 'Le coefficient est obligatoire.',
            'coefficient.min'      => 'Le coefficient minimum est 0.5.',
            'coefficient.max'      => 'Le coefficient maximum est 10.',
        ];
    }
}