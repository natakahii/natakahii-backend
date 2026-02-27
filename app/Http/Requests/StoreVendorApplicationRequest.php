<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth('api')->id();

        return [
            'business_name' => ['required', 'string', 'max:255'],
            'business_email' => ['required', 'email', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'ward' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:100'],
            'region' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'Business name is required.',
            'business_email.required' => 'Business email is required.',
            'business_email.email' => 'Business email must be a valid email address.',
            'full_name.required' => 'Full name is required.',
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'ward.required' => 'Ward is required.',
            'street.required' => 'Street is required.',
            'region.required' => 'Region is required.',
        ];
    }
}
