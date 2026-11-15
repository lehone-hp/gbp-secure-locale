<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificationRequest extends FormRequest
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
            'name.en' => ['required', 'string', 'max:255'],
            'name.fr' => ['required', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];

        // Image is required on create, optional on update
        if ($this->isMethod('post')) {
            $rules['image'] = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
        } else {
            $rules['image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name.en' => 'name (English)',
            'name.fr' => 'name (French)',
            'display_order' => 'display order',
            'is_active' => 'active status',
        ];
    }
}
