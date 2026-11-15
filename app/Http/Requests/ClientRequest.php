<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];

        // Logo is required on create, optional on update
        if ($this->isMethod('post')) {
            $rules['logo'] = ['required', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'];
        } else {
            $rules['logo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'website_url' => 'website URL',
            'display_order' => 'display order',
            'is_active' => 'active status',
        ];
    }
}
