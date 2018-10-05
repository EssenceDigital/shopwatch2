<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveUser extends FormRequest
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
            'name' => 'required|string|max:35',
            'email' => 'required|string|max:100',
            'password' => 'required|string|min:6|max:20|confirmed',
            'role' => 'required|string|max:5',
            'hourly_wage' => 'numeric|between:0,1000000000000.99|nullable'
        ];
    }
}
