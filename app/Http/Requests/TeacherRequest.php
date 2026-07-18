<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class TeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['Admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $teacher = $this->route('teacher');
        return [
             // Compte utilisateur
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'password' => [
                'nullable',
                'min:8'
            ],  

            // Informations enseignant
            'employee_number'       => ['required', 'string', 'max:50', Rule::unique('teachers', 'employee_number')->ignore($teacher?->id)],
            'phone'  => ['nullable', 'string', new Phone()->international()],
            'nationality'  => ['nullable', 'string', 'max:100'],
            'qualification'  => ['required', 'string', 'max:150'],
            'specialty'        => ['nullable', 'string', 'max:150'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'gender'  => ['required', 'in:male,female'],
            'address' => ['nullable', 'string', 'max:255'],
            'status'  => ['required', 'in:active,inactive,on_leave'],
            'bio'  => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,webp,jpeg']
        ];
    }

    public function messages() : array{
        return [
            'name.required'            => 'Le nom complet est obligatoire.',
            'email.required'           => "L'email est obligatoire.",
            'email.unique'             => 'Cet email est déjà utilisé.',
            'password.required'        => 'Le mot de passe est obligatoire.',
            'password.confirmed'       => 'La confirmation du mot de passe ne correspond pas.',
            'employee_number.required' => 'Le numéro employé est obligatoire.',
            'employee_number.unique'   => 'Ce numéro employé est déjà utilisé.',
            'qualification.required'   => 'La qualification est obligatoire.',
            'gender.required'          => 'Le genre est obligatoire.',
            'status.required'          => 'Le statut est obligatoire.',
        ];
    }
}