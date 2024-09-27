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
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'size' => 'sometimes|in:Small,Medium,Large,No Size',
            'productImage' => 'file|mimes:jfif,jpeg,jpg,png|max:10240',
            'type' => 'sometimes|in:Food,Beverage,Milktea',
            'availability' => 'sometimes|in:Available,Not Available',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'size.in' => 'The size must be one of: Small, Medium, Large, No Size.',
            'type.in' => 'The type must be one of: Food,Beverage,Milktea.',
            'availability.in' => 'The availability must be either Available or Not Available.',
        ];
    }
}
