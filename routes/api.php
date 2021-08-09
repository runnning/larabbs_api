<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VerificationCodesController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\AuthorizationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function (){
        Route::middleware('throttle:'. config('api.rate_limits.sign'))
            ->group(function (){
                //图片验证码
                Route::post('captchas',[CaptchasController::class,'store'])
                    ->name('captchas.store');
                //短信验证
                Route::post('verificationCodes',[VerificationCodesController::class,'store'])
                    ->name('verificationCodes.store');
                //用户注册
                Route::post('users',[UserController::class,'store'])
                    ->name('users.store');
                //第三方登录
                Route::post('socials/{social_type}/authorizations',[AuthorizationsController::class,'socialStore'])
                    ->where('social_type','wechat')
                    ->name('socials.authorizations.store');
                //登录
                Route::post('authorizations',[AuthorizationsController::class,'store'])
                    ->name('authorizations.store');
                //刷新token
                Route::put('authorizations',[AuthorizationsController::class,'update'])
                    ->name('authorizations.update');
                //删除token
                Route::delete('authorizations',[AuthorizationsController::class,'destroy'])
                    ->name('authorizations.destroy');
            });

        Route::middleware('throttle:'. config('api.rate_limits.access'))
            ->group(function (){

            });
});
