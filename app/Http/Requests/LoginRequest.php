<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'=>'required|email',
            'password'=>'required'
        ];
    }
}
        
        // if($validator->fails())
        // {
        //     return response()->json($validator->errors(),422);
        // }
             //public function createNewToken($token)
     //{
       //   return response()->json([ 'access_token'=>$token]);
     //}