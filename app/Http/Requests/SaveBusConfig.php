<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBusConfig extends FormRequest
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
            'shop_rate' => 'required|numeric|between:0,1000000000000.99',
            'gst_rate' => 'required|numeric|between:0,1000000000000.99',
            'shop_supply_rate' => 'required|numeric|between:0,1000000000000.99',
            'city' => 'required|string|max:50',
            'province' => 'required|string|max:25',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:7',
            'phone' => 'required|string|max:14'
        ];
    }
}
