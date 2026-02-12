<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $vendorId = $this->user('api')?->vendor?->id;

        return [
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:vendors,shop_slug,'.$vendorId],
            'description' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shop_slug.unique' => 'This shop URL is already taken. Please choose another.',
            'shop_slug.alpha_dash' => 'Shop URL may only contain letters, numbers, dashes, and underscores.',
        ];
    }
}
