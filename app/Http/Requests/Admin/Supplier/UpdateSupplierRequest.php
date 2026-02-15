<?php

namespace App\Http\Requests\Admin\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
        $supplierId = $this->route('supplier')->id;

        return [
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplierId,
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email,' . $supplierId,
            'phone' => 'required|string|max:255|unique:suppliers,phone,' . $supplierId,
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
