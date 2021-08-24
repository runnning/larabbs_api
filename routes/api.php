<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VerificationCodesController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\TopicsController;
use App\Http\Controllers\Api\RepliesController;
use App\Http\Controllers\Api\NotificationsController;

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
                //游客可以访问的接口

                //某个用户详情
                Route::get('users/{user}',[UserController::class,'show'])
                    ->name('users.show');
                //分类列表
                Route::get('categories',[CategoriesController::class,'index'])
                    ->name('categories.index');
                //某个用户发布的话题
                Route::get('users/{user}/topics',[TopicsController::class,'userIndex'])
                    ->name('users.topics.index');
                //话题列表、详情
                Route::resource('topics',TopicsController::class)->only([
                    'index','show'
                ]);
                //话题回复列表
                Route::get('topics/{topic}/replies',[RepliesController::class,'index'])
                    ->name('topics.replies.index');
                //某个用户的回复列表
                Route::get('users/{user}/replies',[RepliesController::class,'userIndex'])
                    ->name('users.replies.index');
                //登录后可以访问的接口
                Route::middleware('auth:api')->group(function (){
                    //当前登录用户信息
                    Route::get('user',[UserController::class,'me'])
                        ->name('user.show');
                    //编辑登录用户信息
                    Route::patch('user',[UserController::class,'update'])
                        ->name('user.update');
                    //上传图片
                    Route::post('images',[ImageController::class,'store'])
                        ->name('images.store');
                    //发布话题
                    Route::resource('topics',TopicsController::class)->only([
                        'destroy','store','update'
                    ]);
                    //发布回复
                    Route::post('topics/{topic}/replies',[RepliesController::class,'store'])
                        ->name('topics.replies.store');
                    //删除回复
                    Route::delete('topics/{topic}/replies/{reply}',[RepliesController::class,'destroy'])
                        ->name('topics.replies.destroy');
                    //通知列表
                    Route::get('notifications',[NotificationsController::class,'index'])
                        ->name('notifications.index');
                    //通知统计
                    Route::get('notifications/stats',[NotificationsController::class,'stats'])
                        ->name('notifications.stats');
                });

            });
});
