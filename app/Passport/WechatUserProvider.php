<?php
namespace App\Passport;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Overtrue\LaravelSocialite\Socialite;
use Psr\Http\Message\ServerRequestInterface;
use Sk\Passport\UserProvider;

class WechatUserProvider extends UserProvider
{

    public function validate(ServerRequestInterface $request)
    {
        $this->validateRequest($request,[
            'code'=>'required_without:access_token|string',
            'access_token'=>'required_without:code|string',
            'openid'=>'required_with:access_token|string',
        ]);
    }

    /**
     * @throws AuthenticationException
     */
    public function retrieve(ServerRequestInterface $request)
    {
        $inputs=$this->only($request,[
            'code',
            'access_token',
            'openid',
        ]);
        $driver=Socialite::create('wechat');
        try {
            if ($code=$inputs['code']){
                $oauthUser=$driver->userFromCode($code);
            }else{
                $tokenData['access_token']=$inputs['access_token'];
                $driver->withOpenid($inputs['openid']);
                $oauthUser=$driver->userFromToken($inputs['access_token']);
            }
        }catch (\Exception $exception){
            throw new AuthenticationException('参数错误,未获取用户信息');
        }

        if (!$oauthUser->getId()){
            throw new AuthenticationException('参数错误,未获取用户信息');
        }
        $unionid=$oauthUser->getRaw()['unionid']??null;

        if ($unionid){
            $user=User::where('weixin_unionid',$unionid)->firt();
        }else{
            $user=User::where('weixin_openid',$oauthUser->getId())->first();
        }

        //没有用户,默认创建一个用户
        if(!$user){
            $user=User::create([
                'name'=>$oauthUser->getNickname(),
                'avatar'=>$oauthUser->getAvatar(),
                'weixin_openid'=>$oauthUser->getId(),
                'weixin_unionid'=>$unionid,
            ]);
        }

        return $user;
    }
}
