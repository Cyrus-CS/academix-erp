<?php

namespace App\Http\Requests\Payment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'student_id'            => ['required', 'exists:students,id'],
            'fee_type_id'           => ['required', 'exists:fee_types,id'],
            'amount_paid'                => ['required', 'numeric', 'min:0'],
            'payment_method'        => ['required', 'in:cash,bank_transfer,mobile_money,check,card'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'status'  => ['required', 'in:paid,pending,overdue,cancelled'],
            'paid_at'               => ['required', 'date'],
            'note'                 => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages() : array {
        return [
            'student_id.required'     => "L'étudiant est obligatoire.",
            'fee_type_id.required'    => 'Le type de frais est obligatoire.',
            'amount_paid.required'         => 'Le montant est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_method.in'       => 'Le mode de paiement sélectionné est invalide.',
            'status.required'         => 'Le statut est obligatoire.',
            'paid_at.required'        => 'La date de paiement est obligatoire.',
        ];
    }
}