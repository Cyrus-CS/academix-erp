<?php

namespace App\Http\Requests\Setting;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'school_name'          => ['required', 'string', 'max:200'],
            'school_email'         => ['required', 'email', 'max:200'],
            'school_phone'         => ['nullable', 'string', 'max:20'],
            'school_address'       => ['nullable', 'string', 'max:500'],
            'school_motto'         => ['nullable', 'string', 'max:300'],
            'school_website'       => ['nullable', 'url', 'max:200'],
            'currency'             => ['required', 'in:FCFA,USD,EUR,GBP'],
            'language'             => ['required', 'in:fr,en'],
            'timezone'             => ['required', 'timezone'],
            'academic_year_format' => ['required', 'string', 'max:50'],
            'logo'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'favicon'              => ['nullable', 'image', 'mimes:ico,png,svg', 'max:512'],
        ];
    }

    public function messages() : array{
        return [
            'school_name.required'  => "Le nom de l'école est obligatoire.",
            'school_email.required' => "L'email de l'école est obligatoire.",
            'school_email.email'    => "L'email n'est pas valide.",
            'school_website.url'    => "L'URL du site web n'est pas valide.",
            'currency.required'     => 'La devise est obligatoire.',
            'language.required'     => 'La langue est obligatoire.',
            'timezone.required'     => 'Le fuseau horaire est obligatoire.',
            'timezone.timezone'     => 'Le fuseau horaire sélectionné est invalide.',
            'logo.mimes'            => 'Le logo doit être au format JPG, PNG, SVG ou WEBP.',
            'logo.max'              => 'Le logo ne doit pas dépasser 2 Mo.',
            'favicon.mimes'         => 'Le favicon doit être au format ICO, PNG ou SVG.',
            'favicon.max'           => 'Le favicon ne doit pas dépasser 512 Ko.',
        ];
    }
}