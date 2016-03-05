<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UserInfoUpdateRequest extends Request
{
    public function rules()
    {
        $user = Auth::user();

        $rules = [
            'nickname'  => 'required|unique:users,nickname,' . $user->id . '|min:2|max:10',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'about'     => 'max:100',
            'password'  => 'min:5|max:20'
        ];

        return $rules;
    }
    
    public function authorize()
    {
        return true;
    }
}
