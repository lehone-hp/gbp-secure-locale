<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Step 2: gateway tokens for an order that already exists.
 */
class CheckoutRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_nonce' => 'required_if:payment_gateway,braintree',
            'payment_method_id' => 'required_if:payment_gateway,stripe',
            'payment_source_id' => 'required_if:payment_gateway,square',
            'payment_gateway' => 'nullable|in:braintree,stripe,paypal,payunit,square,whop,mollie',
        ];
    }
}
