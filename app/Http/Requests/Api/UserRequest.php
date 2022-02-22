<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function rules()
    {
        switch ($this->method()){
            case 'POST':
                return [
                    'name'=>'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
                    'password'=>'required|string|min:6',
                    'verification_key'=>'required|string',
                    'verification_code'=>'required|string',
                ];
                break;
            case 'PATCH':
                $userId=auth('api')->id();
                return [
                    'name'=>'between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,'.$userId,
                    'email'=>'email|unique:users,email,'.$userId,
                    'introduction'=>'max:80',
                    'avatar_image_id'=>'exists:images,id,type,avatar,user_id,'.$userId,

                ];
                break;
        }
    }
    public function attributes(): array
    {
        return[
            'verification_key'=>'短信验证码key',
            'verification_code'=>'短信验证码',
        ];
    }
    public function messages(): array
    {
        return [
            'name.unique'=>'用户名已被占用,请重新填写。',
            'name.regex'=>'用户名只支持英文、数字、横杠和下划线。',
            'name.between'=>'用户名必须介于3-25个字符。',
            'name.required'=>'用户名不能为空。',
        ];
    }
}