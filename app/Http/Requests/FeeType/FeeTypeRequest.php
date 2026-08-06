<?php

namespace App\Http\Requests\FeeType;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FeeTypeRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:100', 'unique:fee_types,name'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'frequency'   => ['required', 'in:monthly,quarterly,yearly,one_time'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages() : array{
        return [
            'name.required'      => 'Le nom du type de frais est obligatoire.',
            'name.unique'        => 'Ce type de frais existe déjà.',
            'amount.required'    => 'Le montant est obligatoire.',
            'amount.min'         => 'Le montant doit être positif.',
            'frequency.required' => 'La fréquence est obligatoire.',
            'frequency.in'       => 'La fréquence sélectionnée est invalide.',
        ];
    }
}