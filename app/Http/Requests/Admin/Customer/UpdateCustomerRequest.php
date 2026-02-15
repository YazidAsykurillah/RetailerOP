<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customerId,
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . $customerId,
            'address' => 'nullable|string',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'is_active' => 'boolean',
        ];
    }
}
