<?php


namespace Tests\Traits;

use App\Models\User;

trait ActingJWTUser
{
    public function JWTActing(User $user): ActingJWTUser
    {
        $token=auth('api')->fromUser($user);
        $this->withHeaders(['Authorization' => 'Bearer '.$token]);
        return $this;
    }
}
