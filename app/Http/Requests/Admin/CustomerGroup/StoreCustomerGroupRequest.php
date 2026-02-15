<?php

namespace App\Http\Requests\Admin\CustomerGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerGroupRequest extends FormRequest
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
            'is_default' => $this->has('is_default'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:customer_groups,name',
            'code' => 'required|string|max:50|unique:customer_groups,code',
            'percentage_discount' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
        ];
    }
}
