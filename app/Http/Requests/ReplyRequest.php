<?php

namespace App\Http\Requests;

class ReplyRequest extends Request
{
    public function rules(): array
    {
       return ['content'=>'required|min:2'];
    }

}
