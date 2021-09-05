<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Overtrue\LaravelSocialite\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationsController extends AccessTokenController
{
    public function store(ServerRequestInterface $request){
        return $this->issueToken($request)->setStatusCode(201);
    }

    public function update(ServerRequestInterface $request): \Illuminate\Http\Response
    {
        return $this->issueToken($request);
    }

    public function destroy(){
        if(auth('api')->check()){
            auth('api')->user()->token()->revoke();
            return response(null,204);
        }else{
            throw new AuthenticationException('The token is invalid.');
        }
    }
}
