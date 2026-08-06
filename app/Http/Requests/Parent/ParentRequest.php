<?php

namespace App\Http\Requests\Parent;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class ParentRequest extends FormRequest
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
        $parent = $this->route('parent');
        $userId = $parent?->user_id;
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email',Rule::unique('users', 'email')->ignore($userId)],
            'phone'         => ['nullable', 'string', 'max:20', new Phone()->international()],
            'password'      => ['nullable', 'string', 'min:8'],
            'student_ids'   => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ];
    }
}