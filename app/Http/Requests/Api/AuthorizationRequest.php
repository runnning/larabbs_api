<?php

namespace App\Http\Requests\Api;


class AuthorizationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username'=>'required|string',
            'password'=>'required|alpha_dash|min:6',
        ];
    }
}
