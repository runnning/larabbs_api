<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Overtrue\LaravelSocialite\Socialite;

class AuthorizationsController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    public function socialStore($type, SocialAuthorizationRequest $request): \Illuminate\Http\JsonResponse
    {
        $driver=Socialite::create($type);

        try {
            if($code=$request->code){
                $oauthUser=$driver->userFromCode($code);
            }else{
                //微信需要增加openid
                if($type=='wechat'){
                    $driver->withOpenid($request->openid);
                }
                $oauthUser=$driver->userFromToken($request->access_token);
            }
        }catch (\Exception $exception){
            throw new AuthenticationException('参数错误,未获取用户信息');
        }

        if(!$oauthUser->getId()){
            throw new AuthenticationException('参数错误,未获取用户信息');
        }
        switch ($type){
            case 'wechat':
               $unionid=$oauthUser->getRaw()['unionid']??null;

               if($unionid){
                   $user=User::where('weixin_unionid',$unionid)->first();
               }else{
                   $user=User::where('weixin_openid',$oauthUser->getId())->first();
               }

               //没有用户,默认创建个用户
                if(!$user){
                    $user=User::create([
                        'name'=>$oauthUser->getNickname(),
                        'avatar'=>$oauthUser->getAvatar(),
                        'weixin_openid'=>$oauthUser->getId(),
                        'weixin_unionid'=>$unionid,

                    ]);
                }
               break;
        }
        return response()->json(['token'=>$user->id]);
    }
}
