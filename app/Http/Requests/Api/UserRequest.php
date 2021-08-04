<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name'=>'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
            'password'=>'required|string',
            'verification_key'=>'required|string',
            'verification_code'=>'required|string',
        ];
    }
    public function attributes(): array
    {
        return[
            'verification_key'=>'短信验证码key',
            'verification_code'=>'短信验证码',
        ];
    }
}
