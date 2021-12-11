<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPhotoRequest extends FormRequest
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
            'access_token'=>'required',
            'date'=>'string',
            'time'=>'string',
            'name'=>'string',
            'extensions'=>'in:jpeg,jpg,png,gif',
            'access'=>'in:Public,Private,Hidden'
        ];
    }
}
