<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCustomer extends FormRequest
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
            'first' => 'required|string|max:35',
            'last' => 'required|string|max:35',
            'phone_one' => 'required|string|max:14',
            'phone_two' => 'string|max:14|nullable'
        ];
    }
}
