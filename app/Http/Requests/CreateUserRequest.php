<?php

namespace App\Http\Requests;

class CreateUserRequest extends Request
{
    public function rules()
    {
        return [
            'username'  => 'required|unique:users,username|min:5|max:20',
            'password'  => 'required|min:5|max:20',
            'email'     => 'required|email|unique:users',
            'nickname'  => 'required|unique:users,nickname|min:2|max:10'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
