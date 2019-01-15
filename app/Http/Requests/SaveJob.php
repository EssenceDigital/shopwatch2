<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveJob extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'numeric|nullable',
            'work_order_id' => 'required|numeric',
            'tech' => 'required|numeric',
            'title' => 'required|string|max:100',
            'description' => 'string|max:255|nullable',
            'is_flat_rate' => 'required|boolean',
            'hours' => 'required|numeric|between:0,1000000000000.9',
            'shop_rate' => 'required|numeric|between:0,1000000000000.99',
            'flat_rate' => 'required|numeric|between:0,1000000000000.99',
            'flat_rate_cost' => 'required|numeric|between:0,1000000000000.99'
        ];
    }
}
