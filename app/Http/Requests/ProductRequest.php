<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Backpack\CRUD\app\Library\Validation\Rules\ValidUpload;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required',Rule::unique('products')->ignore($this->id)],
            'price_buy' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => ValidUpload::field('required')->file('file|mimes:jpeg,png,jpg|max:100'),
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
