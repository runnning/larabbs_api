<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'phone'=> 'required|phone:CN,mobile|unique:users',
        ];
    }
}
