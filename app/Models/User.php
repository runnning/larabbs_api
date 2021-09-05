<?php

namespace App\Models;


use App\Models\Traits\ActiveUserHelper;
use App\Models\Traits\LastActivedAtHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory,MustVerifyEmailTrait;
    use ActiveUserHelper;
    use LastActivedAtHelper;
    use HasRoles;
    use Notifiable{
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        //如果通知的人是当前用户,就不必通知了！
        if($this->id==Auth::id()){
            return;
        }
        //只有数据库类型通知才需要提醒,直接发送Email或者其他都Pass
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');
        }
        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'introduction',
        'avatar',
        'phone',
        'weixin_openid',
        'weixin_unionid',
        'registration_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'weixin_openid',
        'weixin_unionid'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model): bool
    {
        return $this->id ===$model->user_id;
    }

    public function replies(){
        return $this->hasMany(Reply::class);
    }

    //通知状态设为已读,并清空未读消息。
    public function markAsRead(){
        $this->notification_count=0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }


    public function setPasswordAttribute($value){
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if(strlen($value)!=60){
            // 不等于 60，做密码加密处理
            $value=bcrypt($value);
        }
        $this->attributes['password']=$value;
    }

    public function setAvatarAttribute($path){
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if(!Str::startsWith($path,'http')){
            //拼接完整的URL
            $path=config('app.url')."/uploads/images/avatars/$path";
        }
        $this->attributes['avatar']=$path;
    }
}
