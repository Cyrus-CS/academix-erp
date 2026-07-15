<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('view students');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // $this->student est l'élève lié à la route (route model binding)
        $student = $this->route('student');
        $userId = $student?->user_id;
        return [
            'name' => [ 'required', 'string','max:255'],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],

            'password' => [
                'nullable',
                'min:8'
            ],  
            'class_id' => [
                'required',
                'exists:classes,id'
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id'
            ],

            'phone' => ['nullable', new Phone()],

            'birth_date' => [
                'required',
                'date',
                'before:today'
            ],

            'gender' => [
                'required',
                Rule::in(['male', 'female'])
            ],

            'guardian_name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'guardian_phone' => [
                'nullable',
                new Phone()->international(),
            ],

            'address' => [
                'nullable',
                'string',
                'max:120'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5048'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Le nom complet est obligatoire.',
            'birth_date.required'       => 'La date de naissance est obligatoire.',
            'birth_date.before'         => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'gender.required'           => 'Le genre est obligatoire.',
            'gender.in'                 => 'Le genre doit être masculin ou féminin.',
            'class_id.required'         => 'La classe est obligatoire.',
            'class_id.exists'           => 'La classe sélectionnée est invalide.',
            'academic_year_id.required' => 'L\'année académique est obligatoire.',
            'academic_year_id.exists'   => 'L\'année académique sélectionnée est invalide.',
            'photo.image'               => 'Le fichier doit être une image.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
        ];
    }
    

}