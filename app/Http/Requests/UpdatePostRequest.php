<?php

namespace App\Http\Requests;

class UpdatePostRequest extends Request
{
    public function rules()
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'category' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
