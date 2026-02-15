<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')->id;

        return [
            'sku' => 'required|string|max:50|unique:products,sku,' . $productId,
            'name' => 'required|string|max:255', // Name doesn't need to be unique for updates based on previous pattern but usually it is. Actually, task said "name is should be unique" for category. Let's apply it here too for consistency if not specified otherwise, but standard is unique. The controller had 'sku' unique, 'name' unique. So I should keep it.
            // Wait, looking at controller code:
            // Store: 'name' => 'required|string|max:255|unique:products,name',
            // Update: 'name' => 'required|string|max:255', (Not unique in update?)
            // Let's re-read controller code.
            // Step 146 View File:
            // Store: 'name' => 'required|string|max:255|unique:products,name',
            // Update: 'name' => 'required|string|max:255',
            // It seems implementation plan said: "matches Store, but sku and name unique rules ignore current product ID."
            // So I should make name unique as well in update, adhering to the plan which user approved.
            // But verify if original controller had unique on update.
            // Original Update: 'name' => 'required|string|max:255', (NO unique)
            // Original Update: 'sku' => 'unique:...,sku,' . $id
            
            // However, the PLAN says: "matches Store, but sku and name unique rules ignore current product ID."
            // Since user approved the plan, I will follow the plan and add unique to name in update as well.
            
            'name' => 'required|string|max:255|unique:products,name,' . $productId,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'base_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ];
    }
}
