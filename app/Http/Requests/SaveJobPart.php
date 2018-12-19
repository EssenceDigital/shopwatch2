<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveJobPart extends FormRequest
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
            'id' => 'string|max:255|nullable',
            'job_id' => 'required|numeric',
            'supplier' => 'required|string|max:100',
            'title' => 'required|string|max:100',
            'part_number' => 'required|string|max:100',
            'quantity' => 'required|max:25',
            'total_cost' => 'required|numeric|between:0,1000000000000.99',
            'billing_price' => 'required|numeric|between:0,1000000000000.99'
        ];
    }
}
