<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'title'      => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string'],
            'audience'   => ['required', 'in:all,teachers,students,parents'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_pinned'  => ['boolean'],
        ];
    }

    public function messages() : array{
        return [
            'title.required'    => 'Le titre est obligatoire.',
            'message.required'  => 'Le contenu est obligatoire.',
            'audience.required' => 'L\'audience est obligatoire.',
            'expires_at.after'  => 'La date d\'expiration doit être dans le futur.',
        ];
    }
}