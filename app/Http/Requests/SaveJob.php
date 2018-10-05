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
            'tech' => 'required|string|max:50',
            'title' => 'required|string|max:100',
            'description' => 'string|max:255|nullable',
            'hours' => 'required|numeric|between:0,1000000000000.9',
            'shop_rate' => 'numeric|between:0,1000000000000.99|nullable'
        ];
    }
}
