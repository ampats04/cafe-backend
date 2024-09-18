<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'size' => 'required|in:Small,Medium,Large,No Size',
            'productImage' => 'sometimes|file|mimes:jpeg,jpg,png|max:2048',
            'type' => 'required|in:Food,Beverage,Shake',
            'availability' => 'required|in:Available,Not Available',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'size.in' => 'The size must be one of: Small, Medium, Large, No Size.',
            'type.in' => 'The size must be one of: Food,Beverage,Shake.',
            'availability.in' => 'The availability must be either Available or Not Available.',
        ];
    }
}
