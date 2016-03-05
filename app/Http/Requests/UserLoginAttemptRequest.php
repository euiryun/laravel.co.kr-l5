<?php

namespace App\Http\Requests;

class UserLoginAttemptRequest extends Request
{
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
